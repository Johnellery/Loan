<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Filament\Resources\LoanResource\RelationManagers;
use App\Models\Applicant;
use App\Models\Payment;
use App\Models\PaymentSchedule;
use DateInterval;
use DateTime;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\PaymentReminder;
use Illuminate\Support\Facades\Notification;
use Filament\Tables\Actions\ViewAction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class LoanResource extends Resource
{
    protected static ?string $model = Applicant::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $activeNavigationIcon = 'heroicon-s-clipboard-document';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Loan';
    public static function getLabel(): string
    {
        return 'Active Loan'; // Replace this with your custom label
    }
    public static function shouldRegisterNavigation(): bool
    {
        $userole = Auth::user();
        $user = $userole->role->name;
        return $user && $user=== 'Admin' || $user === 'Staff' || $user === 'Collector';
    }

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             //
    //         ]);
    // }

    public static function table(Table $table): Table
    {

        return $table
        ->query(fn (Applicant $query) => self::applyRoleConditions($query))
        ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\TextColumn::make('fullname')
                ->searchable()
                ->label('Customer name')
                ->getStateUsing(function (Applicant $record) {
                    return "{$record->last}, {$record->first} {$record->middle}";
                }),

                Tables\Columns\TextColumn::make('payment')
                ->description(function (Applicant $record) {
                    $or = $record->installment;
                    if ($or == 4) {
                        return "Weekly";
                    } elseif ($or == 1) {
                        return "Monthly";
                    } else {
                        return "Error";
                    }
                })
                ->label('Installment')
                ->numeric(
                    decimalPlaces: 2,
                    decimalSeparator: '.',
                    thousandsSeparator: ',',
                ),
                Tables\Columns\TextColumn::make('remaining_balance')
                ->label('Remaining balance')
                ->numeric(
                    decimalPlaces: 2,
                    decimalSeparator: '.',
                    thousandsSeparator: ',',
                )
                ->getStateUsing(function (Applicant $record) {
                    return $record->updateRemainingBalance();
                }),
                Tables\Columns\TextColumn::make('payment_status')
                ->Badge()
                ->colors([
                    'success' => 'Paid',
                    'danger' => 'Missed',
                    'warning' => 'Pending'
                ])
                // ->getStateUsing(fn (Applicant $record) => self::calculatePaymentStatus($record)),
                ->getStateUsing(function (Applicant $record) {
                    return $record->updateStatus();
                }),
                Tables\Columns\TextColumn::make('payment_schedule')
                ->label('Payment Schedule')
                ->getStateUsing(fn (Applicant $record) => self::calculatePaymentSchedule($record))
                ->description(function (Applicant $record) {
                    return $record->updateDescription();
                }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('Loan Summary')
                    ->icon('heroicon-o-folder-minus')
                    ->color('success')
                    ->url(fn (Applicant $record) => route('generate-pdf', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make()
                ->slideOver()
                ->color('primary'),
                ])
                ->button()
                ->color('warning')
                ->label('Actions')
            ])
            ->bulkActions([
                //
            ])
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoans::route('/'),
            'view' => Pages\ViewSchedule::route('/{record}'),
            // 'create' => Pages\CreateLoan::route('/create'),
            // 'edit' => Pages\EditLoan::route('/{record}/edit'),
        ];
    }
    private static function applyRoleConditions($query): Builder
    {
        $user = Auth::user();

        if ($user->role->name === 'Admin') {
            return $query->where('status', 'approved')
                         ->where('remaining_balance', '>', 0)
                         ->where('ci_status', 'approved');
        } elseif ($user->role->name === 'Staff' || $user->role->name === 'Collector') {
            return $query->where('branch_id', $user->branch_id)
                         ->where('remaining_balance', '>', 0)
                         ->where('ci_status', 'approved')
                         ->where(function ($query) {
                             $query->where('status', 'approved');
                         });
        } elseif ($user->role->name === 'Customer' ) {
            return $query->where('user_id', $user->id)
                         ->where('remaining_balance', '>', 0)
                         ->where('ci_status', 'approved')
                         ->where(function ($query) {
                             $query->where('status', 'approved');
                         });
        }

        return $query;
    }


    private static function calculatePaymentSchedule(Applicant $record): string
    {
        $installment = $record->installment;
        $startDate = Carbon::parse($record->start)->startOfDay();
        $endDate = Carbon::parse($record->end)->startOfDay();
        $today = Carbon::now()->startOfDay();
        $nextPaymentDate = $startDate;

        if ($installment === '4') {
            while ($nextPaymentDate->isBefore($today)) {
                $nextPaymentDate->addWeek();
            }
        } elseif ($installment === '1') {
            while ($nextPaymentDate->isBefore($today)) {
                $nextPaymentDate->addMonth();
            }
        }
        return $nextPaymentDate->format('F j, Y');
    }
    private static function calculateCurrentPaymentSchedule(Applicant $record, $decrement = true): string
{
    $installment = $record->installment;
    $startDate = Carbon::parse($record->start)->startOfDay();
    $endDate = Carbon::parse($record->end)->startOfDay();
    $today = Carbon::now()->startOfDay();
    $currentPaymentDate = $startDate;

    if ($installment === '4') {
        while ($currentPaymentDate->isBefore($today)) {
            $currentPaymentDate->addWeek();
        }
    } elseif ($installment === '1') {
        while ($currentPaymentDate->isBefore($today)) {
            $currentPaymentDate->addMonth();
        }
    }

    // Optionally decrement by one week or one month
    if ($decrement) {
        if ($installment === '4') {
            $currentPaymentDate->subWeek();
        } elseif ($installment === '1') {
            $currentPaymentDate->subMonth();
        }
    }

    return $currentPaymentDate->format('F j, Y');
}
public static function getEloquentQuery(): Builder
{
    $user = Auth::user();
    $query = parent::getEloquentQuery();

    if ($user->role->name === 'Admin') {
        return $query->withoutGlobalScopes([SoftDeletingScope::class]);
    } elseif ($user->role->name === 'Staff'  || $user->role->name === 'Collector') {
        return $query->where('branch_id', $user->branch_id)
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
    return $query;
}
public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Infolists\Components\Fieldset::make('Customer Loan Information')
            ->schema([
            Infolists\Components\Section::make()->schema([
                Infolists\Components\Grid::make(3)->schema([
                    Infolists\Components\TextEntry::make('customer_name')
                    ->label('Customer name'),
                    Infolists\Components\TextEntry::make('bike_price')
                    ->label('Total Contract')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    ),
                    Infolists\Components\TextEntry::make('total_interest')
                    ->label('Total interest')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    ),
                    Infolists\Components\TextEntry::make('plus')
                    ->label('Total amount')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    ),
                    Infolists\Components\TextEntry::make('payment')
                    ->label('Installment')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    ),
                    Infolists\Components\TextEntry::make('remaining_balance')
                    ->label('Remaining balance')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    ),
                ])
            ])
                ]),
        Infolists\Components\Fieldset::make('Payment History')
        ->schema([
        Infolists\Components\Section::make()->schema([
            Infolists\Components\Grid::make(3)->schema([
                Infolists\Components\TextEntry::make('payment_info')
                ->label('Payment Info')
                // ->getStateUsing(function (Applicant $record) {
                //     return $record->updateDate();
                // })
                // ->getStateUsing(function (Applicant $applicant) {
                //     $billingRecords = $applicant->billing->where('billing_status', 'remitted');

                //     $startDate = Carbon::parse($applicant->start);
                //     $endDate = Carbon::parse($applicant->end);
                //     $installmentFrequency = $applicant->installment;
                //     $currentDate = Carbon::now();
                //     $paymentSchedule = [];

                //     while ($startDate <= $endDate) {
                //         $paymentDate = $startDate->format('F j, Y');

                //         $status = 'Pending';

                //         if ($currentDate->greaterThanOrEqualTo($startDate)) {
                //             $status = 'Missed';

                //             // Check if a payment was made between the current payment date and the next one
                //             $nextPaymentDate = $startDate;
                //             if ($installmentFrequency == 4) {
                //                 $nextPaymentDate->addWeek();
                //             } elseif ($installmentFrequency == 1) {
                //                 $nextPaymentDate->addMonth();
                //             }

                //             foreach ($billingRecords as $billingRecord) {
                //                 $recordDate = Carbon::parse($billingRecord->created_at)->format('F j, Y');
                //                 if ($recordDate >= $nextPaymentDate->format('F j, Y')) {
                //                     $status = 'Paid';
                //                     break;
                //                 }
                //             }
                //         }

                //         $paymentSchedule[] = "$paymentDate - $status";

                //         if ($installmentFrequency == 4) {
                //             $startDate->addWeek();
                //         } elseif ($installmentFrequency == 1) {
                //             $startDate->addMonth();
                //         }
                //     }

                //     return implode("\n_________________________________________________________\n", $paymentSchedule);
                // }),
                ->getStateUsing(function (Applicant $record) {

                    //
                    $term = $record->term;
                    $perweek = $record->installment;
                    $billingRecords = $record->billing->where('billing_status', 'remitted');
                    $currentDate = Carbon::now();
                    if($term === '4' && $perweek ==='1'){
                        $month1 = new DateTime($record->month1);
                        $month2 = new DateTime($record->month2);
                        $month3 = new DateTime($record->month3);
                        $month4 = new DateTime($record->month4);
                        $month5 = new DateTime($record->month5);
                        $formattedMonth1 = $month1->format('F j, Y');
                        $formattedMonth2 = $month2->format('F j, Y');
                        $formattedMonth3 = $month3->format('F j, Y');
                        $formattedMonth4 = $month4->format('F j, Y');

                        $paid1 = "";
                        $paid2 = "";
                        $paid3 = "";
                        $paid4 = "";
                        //
                        $paidOnScheduledDate = $billingRecords
                            ->where('created_at', '>=', $month1)
                            ->where('created_at', '<', $month2)
                            ->isNotEmpty();
                        if ($paidOnScheduledDate) {
                            $paid1 = "Paid";
                        } elseif ($currentDate > $month1) {
                            $paid1 = "Missed";
                        } else {
                            $paid1 = "Pending";
                        }
                        //
                        $paidOnScheduledDate2 = $billingRecords
                        ->where('created_at', '>=', $month2)
                        ->where('created_at', '<', $month3)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate2) {
                            $paid2 = "Paid";
                        } elseif ($currentDate > $month2) {
                            $paid2 = "Missed";
                        } else {
                            $paid2 = "Pending";
                        }
                        //
                        $paidOnScheduledDate3 = $billingRecords
                        ->where('created_at', '>=', $month3)
                        ->where('created_at', '<', $month4)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate3) {
                            $paid3 = "Paid";
                        } elseif ($currentDate > $month3) {
                            $paid3 = "Missed";
                        } else {
                            $paid3 = "Pending";
                        }
                        //
                        $paidOnScheduledDate4 = $billingRecords
                        ->where('created_at', '>=', $month4)
                        ->where('created_at', '<', $month5)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate4) {
                            $paid4 = "Paid";
                        } elseif ($currentDate > $month4) {
                            $paid4 = "Missed";
                        } else {
                            $paid4 = "Pending";
                        }
                        $result = $formattedMonth1 . '---' . $paid1 . "\n_________________________________________________________\n" .
                                  $formattedMonth2 . '---' . $paid2 . "\n_________________________________________________________\n" .
                                  $formattedMonth3 . '---' . $paid3 ."\n_________________________________________________________\n" .
                                  $formattedMonth4 . '---'. $paid4;

                        return $result;

                    } else if ($term === '4' && $perweek ==='4'){
                        $week11DateTime = new DateTime($record->week11);
                        $week12DateTime = new DateTime($record->week12);
                        $week13DateTime = new DateTime($record->week13);
                        $week14DateTime = new DateTime($record->week14);
                        $week21DateTime = new DateTime($record->week21);
                        $week22DateTime = new DateTime($record->week22);
                        $week23DateTime = new DateTime($record->week23);
                        $week24DateTime = new DateTime($record->week24);
                        $week31DateTime = new DateTime($record->week31);
                        $week32DateTime = new DateTime($record->week32);
                        $week33DateTime = new DateTime($record->week33);
                        $week34DateTime = new DateTime($record->week34);
                        $week41DateTime = new DateTime($record->week41);
                        $week42DateTime = new DateTime($record->week42);
                        $week43DateTime = new DateTime($record->week43);
                        $week44DateTime = new DateTime($record->week44);
                        $week51DateTime = new DateTime($record->week51);


                        $week11Formatted = $week11DateTime->format('F j, Y');
                        $week12Formatted = $week12DateTime->format('F j, Y');
                        $week13Formatted = $week13DateTime->format('F j, Y');
                        $week14Formatted = $week14DateTime->format('F j, Y');
                        $week21Formatted = $week21DateTime->format('F j, Y');
                        $week22Formatted = $week22DateTime->format('F j, Y');
                        $week23Formatted = $week23DateTime->format('F j, Y');
                        $week24Formatted = $week24DateTime->format('F j, Y');
                        $week31Formatted = $week31DateTime->format('F j, Y');
                        $week32Formatted = $week32DateTime->format('F j, Y');
                        $week33Formatted = $week33DateTime->format('F j, Y');
                        $week34Formatted = $week34DateTime->format('F j, Y');
                        $week41Formatted = $week41DateTime->format('F j, Y');
                        $week42Formatted = $week42DateTime->format('F j, Y');
                        $week43Formatted = $week43DateTime->format('F j, Y');
                        $week44Formatted = $week44DateTime->format('F j, Y');

                        $paid11 = "";
                        $paid12 = "";
                        $paid13 = "";
                        $paid14 = "";
                        $paid21 = "";
                        $paid22 = "";
                        $paid23 = "";
                        $paid24 = "";
                        $paid31 = "";
                        $paid32 = "";
                        $paid33 = "";
                        $paid34 = "";
                        $paid41 = "";
                        $paid42 = "";
                        $paid43 = "";
                        $paid44 = "";

                        //
                    $paidOnScheduledDate11 = $billingRecords
                        ->where('created_at', '>=', $week11DateTime)
                        ->where('created_at', '<', $week12DateTime)
                        ->isNotEmpty();
                    if ($paidOnScheduledDate11) {
                        $paid11 = "Paid";
                    } else if ($currentDate > $week11DateTime) {
                        $paid11 = "Missed";
                    } else {
                        $paid11 = "Pending";
                    }
                    //
                    $paidOnScheduledDate12 = $billingRecords
                    ->where('created_at', '>=', $week12DateTime)
                    ->where('created_at', '<', $week13DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate12) {
                        $paid12 = "Paid";
                    } else if ($currentDate > $week12DateTime) {
                        $paid12 = "Missed";
                    } else {
                        $paid12 = "Pending";
                    }
                    //
                    $paidOnScheduledDate13 = $billingRecords
                    ->where('created_at', '>=', $week13DateTime)
                    ->where('created_at', '<', $week14DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate13) {
                        $paid13 = "Paid";
                    } else if ($currentDate > $week13DateTime) {
                        $paid13 = "Missed";
                    } else {
                        $paid13 = "Pending";
                    }
                    //
                    $paidOnScheduledDate14 = $billingRecords
                    ->where('created_at', '>=', $week14DateTime)
                    ->where('created_at', '<', $week21DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate14) {
                        $paid14 = "Paid";
                    } elseif ($currentDate > $week14DateTime) {
                        $paid14 = "Missed";
                    } else {
                        $paid14 = "Pending";
                    }
                    //
                    $paidOnScheduledDate21 = $billingRecords
                    ->where('created_at', '>=', $week21DateTime)
                    ->where('created_at', '<', $week22DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate21) {
                        $paid21 = "Paid";
                    } elseif ($currentDate > $week21DateTime) {
                        $paid21 = "Missed";
                    } else {
                        $paid21 = "Pending";
                    }
                    //
                    $paidOnScheduledDate22 = $billingRecords
                    ->where('created_at', '>=', $week22DateTime)
                    ->where('created_at', '<', $week23DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate22) {
                        $paid22 = "Paid";
                    } elseif ($currentDate > $week22DateTime) {
                        $paid22 = "Missed";
                    } else {
                        $paid22 = "Pending";
                    }
                    //
                    $paidOnScheduledDate23 = $billingRecords
                    ->where('created_at', '>=', $week23DateTime)
                    ->where('created_at', '<', $week24DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate23) {
                        $paid23 = "Paid";
                    } elseif ($currentDate > $week23DateTime) {
                        $paid23 = "Missed";
                    } else {
                        $paid23 = "Pending";
                    }
                    //
                    $paidOnScheduledDate24 = $billingRecords
                    ->where('created_at', '>=', $week24DateTime)
                    ->where('created_at', '<', $week31DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate24) {
                        $paid24 = "Paid";
                    } elseif ($currentDate > $week24DateTime) {
                        $paid24 = "Missed";
                    } else {
                        $paid24 = "Pending";
                    }
                    //
                    $paidOnScheduledDate31 = $billingRecords
                    ->where('created_at', '>=', $week31DateTime)
                    ->where('created_at', '<', $week32DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate31) {
                        $paid31 = "Paid";
                    } elseif ($currentDate > $week31DateTime) {
                        $paid31 = "Missed";
                    } else {
                        $paid31 = "Pending";
                    }
                    //
                    $paidOnScheduledDate32 = $billingRecords
                    ->where('created_at', '>=', $week32DateTime)
                    ->where('created_at', '<', $week33DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate32) {
                        $paid32 = "Paid";
                    } elseif ($currentDate > $week32DateTime) {
                        $paid32 = "Missed";
                    } else {
                        $paid32 = "Pending";
                    }
                    //
                    $paidOnScheduledDate33 = $billingRecords
                    ->where('created_at', '>=', $week33DateTime)
                    ->where('created_at', '<', $week34DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate33) {
                        $paid33 = "Paid";
                    } elseif ($currentDate > $week33DateTime) {
                        $paid33 = "Missed";
                    } else {
                        $paid33 = "Pending";
                    }
                    //
                    $paidOnScheduledDate34 = $billingRecords
                    ->where('created_at', '>=', $week34DateTime)
                    ->where('created_at', '<', $week41DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate34) {
                        $paid34 = "Paid";
                    } elseif ($currentDate > $week34DateTime) {
                        $paid34 = "Missed";
                    } else {
                        $paid34 = "Pending";
                    }
                    //




                    $paidOnScheduledDate41 = $billingRecords
                    ->where('created_at', '>=', $week41DateTime)
                    ->where('created_at', '<', $week42DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate41) {
                        $paid41 = "Paid";
                    } elseif ($currentDate > $week41DateTime) {
                        $paid41 = "Missed";
                    } else {
                        $paid41 = "Pending";
                    }
                    //
                    $paidOnScheduledDate42 = $billingRecords
                    ->where('created_at', '>=', $week42DateTime)
                    ->where('created_at', '<', $week43DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate42) {
                        $paid42 = "Paid";
                    } elseif ($currentDate > $week42DateTime) {
                        $paid42 = "Missed";
                    } else {
                        $paid42 = "Pending";
                    }
                    //
                    $paidOnScheduledDate43 = $billingRecords
                    ->where('created_at', '>=', $week43DateTime)
                    ->where('created_at', '<', $week44DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate43) {
                        $paid43 = "Paid";
                    } elseif ($currentDate > $week43DateTime) {
                        $paid43 = "Missed";
                    } else {
                        $paid43 = "Pending";
                    }
                    //
                    $paidOnScheduledDate44 = $billingRecords
                    ->where('created_at', '>=', $week44DateTime)
                    ->where('created_at', '<', $week51DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate44) {
                        $paid44 = "Paid";
                    } elseif ($currentDate > $week44DateTime) {
                        $paid44 = "Missed";
                    } else {
                        $paid44 = "Pending";
                    }
                    //
                        return $week11Formatted . '---' . $paid11 ."\n_________________________________________________________\n" .
                                $week12Formatted . '---' . $paid12 ."\n_________________________________________________________\n" .
                                $week13Formatted . '---' . $paid13 ."\n_________________________________________________________\n" .
                                $week14Formatted . '---' . $paid14 ."\n_________________________________________________________\n" .
                                $week21Formatted . '---' . $paid21 ."\n_________________________________________________________\n" .
                                $week22Formatted . '---' . $paid22 ."\n_________________________________________________________\n" .
                                $week23Formatted . '---' . $paid23 ."\n_________________________________________________________\n" .
                                $week24Formatted . '---' . $paid24 ."\n_________________________________________________________\n" .
                                $week31Formatted . '---' . $paid31 ."\n_________________________________________________________\n" .
                                $week32Formatted . '---' . $paid32 ."\n_________________________________________________________\n" .
                                $week33Formatted . '---' . $paid33 ."\n_________________________________________________________\n" .
                                $week34Formatted . '---' . $paid34 ."\n_________________________________________________________\n" .
                                $week41Formatted . '---' . $paid41 ."\n_________________________________________________________\n" .
                                $week42Formatted . '---' . $paid42 ."\n_________________________________________________________\n" .
                                $week43Formatted . '---' . $paid43 ."\n_________________________________________________________\n" .
                                $week44Formatted . '---' . $paid44 ;

                    }  else if($term === '5' && $perweek ==='1'){
                        $billingRecords = $record->billing->where('billing_status', 'remitted');
                        $currentDate = Carbon::now();
                        $month1 = new DateTime($record->month1);
                        $month2 = new DateTime($record->month2);
                        $month3 = new DateTime($record->month3);
                        $month4 = new DateTime($record->month4);
                        $month5 = new DateTime($record->month5);
                        $month6 = new DateTime($record->month6);
                        $formattedMonth1 = $month1->format('F j, Y');
                        $formattedMonth2 = $month2->format('F j, Y');
                        $formattedMonth3 = $month3->format('F j, Y');
                        $formattedMonth4 = $month4->format('F j, Y');
                        $formattedMonth5 = $month5->format('F j, Y');
                        $formattedMonth6 = $month6->format('F j, Y');

                        $paid1 = "";
                        $paid2 = "";
                        $paid3 = "";
                        $paid4 = "";
                        $paid5 = "";
                        $paid6 = "";
                        //
                        $paidOnScheduledDate = $billingRecords
                            ->where('created_at', '>=', $month1)
                            ->where('created_at', '<', $month2)
                            ->isNotEmpty();
                        if ($paidOnScheduledDate) {
                            $paid1 = "Paid";
                        } elseif ($currentDate > $month1) {
                            $paid1 = "Missed";
                        } else {
                            $paid1 = "Pending";
                        }
                        //
                        $paidOnScheduledDate2 = $billingRecords
                        ->where('created_at', '>=', $month2)
                        ->where('created_at', '<', $month3)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate2) {
                            $paid2 = "Paid";
                        } elseif ($currentDate > $month2) {
                            $paid2 = "Missed";
                        } else {
                            $paid2 = "Pending";
                        }
                        //
                        $paidOnScheduledDate3 = $billingRecords
                        ->where('created_at', '>=', $month3)
                        ->where('created_at', '<', $month4)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate3) {
                            $paid3 = "Paid";
                        } elseif ($currentDate > $month3) {
                            $paid3 = "Missed";
                        } else {
                            $paid3 = "Pending";
                        }
                        //
                        $paidOnScheduledDate4 = $billingRecords
                        ->where('created_at', '>=', $month4)
                        ->where('created_at', '<', $month5)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate4) {
                            $paid4 = "Paid";
                        } elseif ($currentDate > $month4) {
                            $paid4 = "Missed";
                        } else {
                            $paid4 = "Pending";
                        }
                        //


                        $paidOnScheduledDate5 = $billingRecords
                        ->where('created_at', '>=', $month5)
                        ->where('created_at', '<', $month6)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate5) {
                            $paid5 = "Paid";
                        } elseif ($currentDate > $month5) {
                            $paid5 = "Missed";
                        } else {
                            $paid5 = "Pending";
                        }
                        $result = $formattedMonth1 . '---' . $paid1 . "\n_________________________________________________________\n" .
                                  $formattedMonth2 . '---' . $paid2 . "\n_________________________________________________________\n" .
                                  $formattedMonth3 . '---' . $paid3 ."\n_________________________________________________________\n" .
                                  $formattedMonth4 . '---'. $paid4 ."\n_________________________________________________________\n" .
                                  $formattedMonth5 . '---'. $paid5;


                        return $result;

                    } else if ($term === '5' && $perweek ==='4'){
                        $week11DateTime = new DateTime($record->week11);
                        $week12DateTime = new DateTime($record->week12);
                        $week13DateTime = new DateTime($record->week13);
                        $week14DateTime = new DateTime($record->week14);
                        $week21DateTime = new DateTime($record->week21);
                        $week22DateTime = new DateTime($record->week22);
                        $week23DateTime = new DateTime($record->week23);
                        $week24DateTime = new DateTime($record->week24);
                        $week31DateTime = new DateTime($record->week31);
                        $week32DateTime = new DateTime($record->week32);
                        $week33DateTime = new DateTime($record->week33);
                        $week34DateTime = new DateTime($record->week34);
                        $week41DateTime = new DateTime($record->week41);
                        $week42DateTime = new DateTime($record->week42);
                        $week43DateTime = new DateTime($record->week43);
                        $week44DateTime = new DateTime($record->week44);
                        $week51DateTime = new DateTime($record->week51);
                        $week52DateTime = new DateTime($record->week52);
                        $week53DateTime = new DateTime($record->week53);
                        $week54DateTime = new DateTime($record->week54);
                        $week61DateTime = new DateTime($record->week61);


                        $week11Formatted = $week11DateTime->format('F j, Y');
                        $week12Formatted = $week12DateTime->format('F j, Y');
                        $week13Formatted = $week13DateTime->format('F j, Y');
                        $week14Formatted = $week14DateTime->format('F j, Y');
                        $week21Formatted = $week21DateTime->format('F j, Y');
                        $week22Formatted = $week22DateTime->format('F j, Y');
                        $week23Formatted = $week23DateTime->format('F j, Y');
                        $week24Formatted = $week24DateTime->format('F j, Y');
                        $week31Formatted = $week31DateTime->format('F j, Y');
                        $week32Formatted = $week32DateTime->format('F j, Y');
                        $week33Formatted = $week33DateTime->format('F j, Y');
                        $week34Formatted = $week34DateTime->format('F j, Y');
                        $week41Formatted = $week41DateTime->format('F j, Y');
                        $week42Formatted = $week42DateTime->format('F j, Y');
                        $week43Formatted = $week43DateTime->format('F j, Y');
                        $week44Formatted = $week44DateTime->format('F j, Y');
                        $week51Formatted = $week51DateTime->format('F j, Y');
                        $week52Formatted = $week52DateTime->format('F j, Y');
                        $week53Formatted = $week53DateTime->format('F j, Y');
                        $week54Formatted = $week54DateTime->format('F j, Y');

                        $paid11 = "";
                        $paid12 = "";
                        $paid13 = "";
                        $paid14 = "";
                        $paid21 = "";
                        $paid22 = "";
                        $paid23 = "";
                        $paid24 = "";
                        $paid31 = "";
                        $paid32 = "";
                        $paid33 = "";
                        $paid34 = "";
                        $paid41 = "";
                        $paid42 = "";
                        $paid43 = "";
                        $paid44 = "";
                        $paid51 = "";
                        $paid52 = "";
                        $paid53 = "";
                        $paid54 = "";

                        //
                    $paidOnScheduledDate11 = $billingRecords
                        ->where('created_at', '>=', $week11DateTime)
                        ->where('created_at', '<', $week12DateTime)
                        ->isNotEmpty();
                    if ($paidOnScheduledDate11) {
                        $paid11 = "Paid";
                    } else if ($currentDate > $week11DateTime) {
                        $paid11 = "Missed";
                    } else {
                        $paid11 = "Pending";
                    }
                    //
                    $paidOnScheduledDate12 = $billingRecords
                    ->where('created_at', '>=', $week12DateTime)
                    ->where('created_at', '<', $week13DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate12) {
                        $paid12 = "Paid";
                    } else if ($currentDate > $week12DateTime) {
                        $paid12 = "Missed";
                    } else {
                        $paid12 = "Pending";
                    }
                    //
                    $paidOnScheduledDate13 = $billingRecords
                    ->where('created_at', '>=', $week13DateTime)
                    ->where('created_at', '<', $week14DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate13) {
                        $paid13 = "Paid";
                    } else if ($currentDate > $week13DateTime) {
                        $paid13 = "Missed";
                    } else {
                        $paid13 = "Pending";
                    }
                    //
                    $paidOnScheduledDate14 = $billingRecords
                    ->where('created_at', '>=', $week14DateTime)
                    ->where('created_at', '<', $week21DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate14) {
                        $paid14 = "Paid";
                    } elseif ($currentDate > $week14DateTime) {
                        $paid14 = "Missed";
                    } else {
                        $paid14 = "Pending";
                    }
                    //
                    $paidOnScheduledDate21 = $billingRecords
                    ->where('created_at', '>=', $week21DateTime)
                    ->where('created_at', '<', $week22DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate21) {
                        $paid21 = "Paid";
                    } elseif ($currentDate > $week21DateTime) {
                        $paid21 = "Missed";
                    } else {
                        $paid21 = "Pending";
                    }
                    //
                    $paidOnScheduledDate22 = $billingRecords
                    ->where('created_at', '>=', $week22DateTime)
                    ->where('created_at', '<', $week23DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate22) {
                        $paid22 = "Paid";
                    } elseif ($currentDate > $week22DateTime) {
                        $paid22 = "Missed";
                    } else {
                        $paid22 = "Pending";
                    }
                    //
                    $paidOnScheduledDate23 = $billingRecords
                    ->where('created_at', '>=', $week23DateTime)
                    ->where('created_at', '<', $week24DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate23) {
                        $paid23 = "Paid";
                    } elseif ($currentDate > $week23DateTime) {
                        $paid23 = "Missed";
                    } else {
                        $paid23 = "Pending";
                    }
                    //
                    $paidOnScheduledDate24 = $billingRecords
                    ->where('created_at', '>=', $week24DateTime)
                    ->where('created_at', '<', $week31DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate24) {
                        $paid24 = "Paid";
                    } elseif ($currentDate > $week24DateTime) {
                        $paid24 = "Missed";
                    } else {
                        $paid24 = "Pending";
                    }
                    //
                    $paidOnScheduledDate31 = $billingRecords
                    ->where('created_at', '>=', $week31DateTime)
                    ->where('created_at', '<', $week32DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate31) {
                        $paid31 = "Paid";
                    } elseif ($currentDate > $week31DateTime) {
                        $paid31 = "Missed";
                    } else {
                        $paid31 = "Pending";
                    }
                    //
                    $paidOnScheduledDate32 = $billingRecords
                    ->where('created_at', '>=', $week32DateTime)
                    ->where('created_at', '<', $week33DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate32) {
                        $paid32 = "Paid";
                    } elseif ($currentDate > $week32DateTime) {
                        $paid32 = "Missed";
                    } else {
                        $paid32 = "Pending";
                    }
                    //
                    $paidOnScheduledDate33 = $billingRecords
                    ->where('created_at', '>=', $week33DateTime)
                    ->where('created_at', '<', $week34DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate33) {
                        $paid33 = "Paid";
                    } elseif ($currentDate > $week33DateTime) {
                        $paid33 = "Missed";
                    } else {
                        $paid33 = "Pending";
                    }
                    //
                    $paidOnScheduledDate34 = $billingRecords
                    ->where('created_at', '>=', $week34DateTime)
                    ->where('created_at', '<', $week41DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate34) {
                        $paid34 = "Paid";
                    } elseif ($currentDate > $week34DateTime) {
                        $paid34 = "Missed";
                    } else {
                        $paid34 = "Pending";
                    }
                    //
                    $paidOnScheduledDate41 = $billingRecords
                    ->where('created_at', '>=', $week41DateTime)
                    ->where('created_at', '<', $week42DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate41) {
                        $paid41 = "Paid";
                    } elseif ($currentDate > $week41DateTime) {
                        $paid41 = "Missed";
                    } else {
                        $paid41 = "Pending";
                    }
                    //
                    $paidOnScheduledDate42 = $billingRecords
                    ->where('created_at', '>=', $week42DateTime)
                    ->where('created_at', '<', $week43DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate42) {
                        $paid42 = "Paid";
                    } elseif ($currentDate > $week42DateTime) {
                        $paid42 = "Missed";
                    } else {
                        $paid42 = "Pending";
                    }
                    //
                    $paidOnScheduledDate43 = $billingRecords
                    ->where('created_at', '>=', $week43DateTime)
                    ->where('created_at', '<', $week44DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate43) {
                        $paid43 = "Paid";
                    } elseif ($currentDate > $week43DateTime) {
                        $paid43 = "Missed";
                    } else {
                        $paid43 = "Pending";
                    }
                    //
                    $paidOnScheduledDate44 = $billingRecords
                    ->where('created_at', '>=', $week44DateTime)
                    ->where('created_at', '<', $week51DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate44) {
                        $paid44 = "Paid";
                    } elseif ($currentDate > $week44DateTime) {
                        $paid44 = "Missed";
                    } else {
                        $paid44 = "Pending";
                    }
                    //


                    $paidOnScheduledDate51 = $billingRecords
                    ->where('created_at', '>=', $week51DateTime)
                    ->where('created_at', '<', $week52DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate51) {
                        $paid51 = "Paid";
                    } elseif ($currentDate > $week51DateTime) {
                        $paid51 = "Missed";
                    } else {
                        $paid51 = "Pending";
                    }
                    //
                    $paidOnScheduledDate52 = $billingRecords
                    ->where('created_at', '>=', $week52DateTime)
                    ->where('created_at', '<', $week53DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate52) {
                        $paid52 = "Paid";
                    } elseif ($currentDate > $week52DateTime) {
                        $paid52 = "Missed";
                    } else {
                        $paid52 = "Pending";
                    }
                    //
                    $paidOnScheduledDate53 = $billingRecords
                    ->where('created_at', '>=', $week53DateTime)
                    ->where('created_at', '<', $week54DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate53) {
                        $paid53 = "Paid";
                    } elseif ($currentDate > $week53DateTime) {
                        $paid53 = "Missed";
                    } else {
                        $paid53 = "Pending";
                    }
                    //
                    $paidOnScheduledDate54 = $billingRecords
                    ->where('created_at', '>=', $week54DateTime)
                    ->where('created_at', '<', $week61DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate54) {
                        $paid54 = "Paid";
                    } elseif ($currentDate > $week54DateTime) {
                        $paid54 = "Missed";
                    } else {
                        $paid54 = "Pending";
                    }
                    //
                        return $week11Formatted . '---' . $paid11 ."\n_________________________________________________________\n" .
                                $week12Formatted . '---' . $paid12 ."\n_________________________________________________________\n" .
                                $week13Formatted . '---' . $paid13 ."\n_________________________________________________________\n" .
                                $week14Formatted . '---' . $paid14 ."\n_________________________________________________________\n" .
                                $week21Formatted . '---' . $paid21 ."\n_________________________________________________________\n" .
                                $week22Formatted . '---' . $paid22 ."\n_________________________________________________________\n" .
                                $week23Formatted . '---' . $paid23 ."\n_________________________________________________________\n" .
                                $week24Formatted . '---' . $paid24 ."\n_________________________________________________________\n" .
                                $week31Formatted . '---' . $paid31 ."\n_________________________________________________________\n" .
                                $week32Formatted . '---' . $paid32 ."\n_________________________________________________________\n" .
                                $week33Formatted . '---' . $paid33 ."\n_________________________________________________________\n" .
                                $week34Formatted . '---' . $paid34 ."\n_________________________________________________________\n" .
                                $week41Formatted . '---' . $paid41 ."\n_________________________________________________________\n" .
                                $week42Formatted . '---' . $paid42 ."\n_________________________________________________________\n" .
                                $week43Formatted . '---' . $paid43 ."\n_________________________________________________________\n" .
                                $week44Formatted . '---' . $paid44 ."\n_________________________________________________________\n" .
                                $week51Formatted . '---' . $paid51 ."\n_________________________________________________________\n" .
                                $week52Formatted . '---' . $paid52 ."\n_________________________________________________________\n" .
                                $week53Formatted . '---' . $paid53 ."\n_________________________________________________________\n" .
                                $week54Formatted . '---' . $paid54;

                    }  else if($term === '6' && $perweek ==='1'){
                        $month1 = new DateTime($record->month1);
                        $month2 = new DateTime($record->month2);
                        $month3 = new DateTime($record->month3);
                        $month4 = new DateTime($record->month4);
                        $month5 = new DateTime($record->month5);
                        $month6 = new DateTime($record->month6);
                        $month7 = clone $month6;
                        $month7->add(new DateInterval('P1M'));
                        $formattedMonth1 = $month1->format('F j, Y');
                        $formattedMonth2 = $month2->format('F j, Y');
                        $formattedMonth3 = $month3->format('F j, Y');
                        $formattedMonth4 = $month4->format('F j, Y');
                        $formattedMonth5 = $month5->format('F j, Y');
                        $formattedMonth6 = $month6->format('F j, Y');
                        $formattedMonth7 = $month7->format('F j, Y');

                        $paid1 = "";
                        $paid2 = "";
                        $paid3 = "";
                        $paid4 = "";
                        $paid5 = "";
                        $paid6 = "";
                        //
                        $paidOnScheduledDate = $billingRecords
                            ->where('created_at', '>=', $month1)
                            ->where('created_at', '<', $month2)
                            ->isNotEmpty();
                        if ($paidOnScheduledDate) {
                            $paid1 = "Paid";
                        } elseif ($currentDate > $month1) {
                            $paid1 = "Missed";
                        } else {
                            $paid1 = "Pending";
                        }
                        //
                        $paidOnScheduledDate2 = $billingRecords
                        ->where('created_at', '>=', $month2)
                        ->where('created_at', '<', $month3)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate2) {
                            $paid2 = "Paid";
                        } elseif ($currentDate > $month2) {
                            $paid2 = "Missed";
                        } else {
                            $paid2 = "Pending";
                        }
                        //
                        $paidOnScheduledDate3 = $billingRecords
                        ->where('created_at', '>=', $month3)
                        ->where('created_at', '<', $month4)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate3) {
                            $paid3 = "Paid";
                        } elseif ($currentDate > $month3) {
                            $paid3 = "Missed";
                        } else {
                            $paid3 = "Pending";
                        }
                        //
                        $paidOnScheduledDate4 = $billingRecords
                        ->where('created_at', '>=', $month4)
                        ->where('created_at', '<', $month5)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate4) {
                            $paid4 = "Paid";
                        } elseif ($currentDate > $month4) {
                            $paid4 = "Missed";
                        } else {
                            $paid4 = "Pending";
                        }
                        //
                        $paidOnScheduledDate5 = $billingRecords
                        ->where('created_at', '>=', $month5)
                        ->where('created_at', '<', $month6)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate5) {
                            $paid5 = "Paid";
                        } elseif ($currentDate > $month5) {
                            $paid5 = "Missed";
                        } else {
                            $paid5 = "Pending";
                        }

                        //
                        $paidOnScheduledDate6 = $billingRecords
                        ->where('created_at', '>=', $month6)
                        ->where('created_at', '<', $month7)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate6) {
                            $paid6 = "Paid";
                        } elseif ($currentDate > $month6) {
                            $paid6 = "Missed";
                        } else {
                            $paid6 = "Pending";
                        }
                        $result = $formattedMonth1 . '---' . $paid1 . "\n_________________________________________________________\n" .
                                  $formattedMonth2 . '---' . $paid2 . "\n_________________________________________________________\n" .
                                  $formattedMonth3 . '---' . $paid3 ."\n_________________________________________________________\n" .
                                  $formattedMonth4 . '---'. $paid4 ."\n_________________________________________________________\n" .
                                  $formattedMonth5 . '---'. $paid5 ."\n_________________________________________________________\n" .
                                  $formattedMonth6 . '---'. $paid6;


                        return $result;

                    } else if ($term === '6' && $perweek ==='4'){
                        $week11DateTime = new DateTime($record->week11);
                        $week12DateTime = new DateTime($record->week12);
                        $week13DateTime = new DateTime($record->week13);
                        $week14DateTime = new DateTime($record->week14);
                        $week21DateTime = new DateTime($record->week21);
                        $week22DateTime = new DateTime($record->week22);
                        $week23DateTime = new DateTime($record->week23);
                        $week24DateTime = new DateTime($record->week24);
                        $week31DateTime = new DateTime($record->week31);
                        $week32DateTime = new DateTime($record->week32);
                        $week33DateTime = new DateTime($record->week33);
                        $week34DateTime = new DateTime($record->week34);
                        $week41DateTime = new DateTime($record->week41);
                        $week42DateTime = new DateTime($record->week42);
                        $week43DateTime = new DateTime($record->week43);
                        $week44DateTime = new DateTime($record->week44);
                        $week51DateTime = new DateTime($record->week51);
                        $week52DateTime = new DateTime($record->week52);
                        $week53DateTime = new DateTime($record->week53);
                        $week54DateTime = new DateTime($record->week54);
                        $week61DateTime = new DateTime($record->week61);
                        $week62DateTime = new DateTime($record->week62);
                        $week63DateTime = new DateTime($record->week63);
                        $week64DateTime = new DateTime($record->week64);
                        $week71DateTime = clone $week64DateTime;
                        $week71DateTime->add(new DateInterval('P7D'));


                        $week11Formatted = $week11DateTime->format('F j, Y');
                        $week12Formatted = $week12DateTime->format('F j, Y');
                        $week13Formatted = $week13DateTime->format('F j, Y');
                        $week14Formatted = $week14DateTime->format('F j, Y');
                        $week21Formatted = $week21DateTime->format('F j, Y');
                        $week22Formatted = $week22DateTime->format('F j, Y');
                        $week23Formatted = $week23DateTime->format('F j, Y');
                        $week24Formatted = $week24DateTime->format('F j, Y');
                        $week31Formatted = $week31DateTime->format('F j, Y');
                        $week32Formatted = $week32DateTime->format('F j, Y');
                        $week33Formatted = $week33DateTime->format('F j, Y');
                        $week34Formatted = $week34DateTime->format('F j, Y');
                        $week41Formatted = $week41DateTime->format('F j, Y');
                        $week42Formatted = $week42DateTime->format('F j, Y');
                        $week43Formatted = $week43DateTime->format('F j, Y');
                        $week44Formatted = $week44DateTime->format('F j, Y');
                        $week51Formatted = $week51DateTime->format('F j, Y');
                        $week52Formatted = $week52DateTime->format('F j, Y');
                        $week53Formatted = $week53DateTime->format('F j, Y');
                        $week54Formatted = $week54DateTime->format('F j, Y');
                        $week61Formatted = $week61DateTime->format('F j, Y');
                        $week62Formatted = $week62DateTime->format('F j, Y');
                        $week63Formatted = $week63DateTime->format('F j, Y');
                        $week64Formatted = $week64DateTime->format('F j, Y');

                        $paid11 = "";
                        $paid12 = "";
                        $paid13 = "";
                        $paid14 = "";
                        $paid21 = "";
                        $paid22 = "";
                        $paid23 = "";
                        $paid24 = "";
                        $paid31 = "";
                        $paid32 = "";
                        $paid33 = "";
                        $paid34 = "";
                        $paid41 = "";
                        $paid42 = "";
                        $paid43 = "";
                        $paid44 = "";
                        $paid51 = "";
                        $paid52 = "";
                        $paid53 = "";
                        $paid54 = "";
                        $paid61 = "";
                        $paid62 = "";
                        $paid63 = "";
                        $paid64 = "";

                        //
                    $paidOnScheduledDate11 = $billingRecords
                        ->where('created_at', '>=', $week11DateTime)
                        ->where('created_at', '<', $week12DateTime)
                        ->isNotEmpty();
                    if ($paidOnScheduledDate11) {
                        $paid11 = "Paid";
                    } else if ($currentDate > $week11DateTime) {
                        $paid11 = "Missed";
                    } else {
                        $paid11 = "Pending";
                    }
                    //
                    $paidOnScheduledDate12 = $billingRecords
                    ->where('created_at', '>=', $week12DateTime)
                    ->where('created_at', '<', $week13DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate12) {
                        $paid12 = "Paid";
                    } else if ($currentDate > $week12DateTime) {
                        $paid12 = "Missed";
                    } else {
                        $paid12 = "Pending";
                    }
                    //
                    $paidOnScheduledDate13 = $billingRecords
                    ->where('created_at', '>=', $week13DateTime)
                    ->where('created_at', '<', $week14DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate13) {
                        $paid13 = "Paid";
                    } else if ($currentDate > $week13DateTime) {
                        $paid13 = "Missed";
                    } else {
                        $paid13 = "Pending";
                    }
                    //
                    $paidOnScheduledDate14 = $billingRecords
                    ->where('created_at', '>=', $week14DateTime)
                    ->where('created_at', '<', $week21DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate14) {
                        $paid14 = "Paid";
                    } elseif ($currentDate > $week14DateTime) {
                        $paid14 = "Missed";
                    } else {
                        $paid14 = "Pending";
                    }
                    //
                    $paidOnScheduledDate21 = $billingRecords
                    ->where('created_at', '>=', $week21DateTime)
                    ->where('created_at', '<', $week22DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate21) {
                        $paid21 = "Paid";
                    } elseif ($currentDate > $week21DateTime) {
                        $paid21 = "Missed";
                    } else {
                        $paid21 = "Pending";
                    }
                    //
                    $paidOnScheduledDate22 = $billingRecords
                    ->where('created_at', '>=', $week22DateTime)
                    ->where('created_at', '<', $week23DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate22) {
                        $paid22 = "Paid";
                    } elseif ($currentDate > $week22DateTime) {
                        $paid22 = "Missed";
                    } else {
                        $paid22 = "Pending";
                    }
                    //
                    $paidOnScheduledDate23 = $billingRecords
                    ->where('created_at', '>=', $week23DateTime)
                    ->where('created_at', '<', $week24DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate23) {
                        $paid23 = "Paid";
                    } elseif ($currentDate > $week23DateTime) {
                        $paid23 = "Missed";
                    } else {
                        $paid23 = "Pending";
                    }
                    //
                    $paidOnScheduledDate24 = $billingRecords
                    ->where('created_at', '>=', $week24DateTime)
                    ->where('created_at', '<', $week31DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate24) {
                        $paid24 = "Paid";
                    } elseif ($currentDate > $week24DateTime) {
                        $paid24 = "Missed";
                    } else {
                        $paid24 = "Pending";
                    }
                    //
                    $paidOnScheduledDate31 = $billingRecords
                    ->where('created_at', '>=', $week31DateTime)
                    ->where('created_at', '<', $week32DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate31) {
                        $paid31 = "Paid";
                    } elseif ($currentDate > $week31DateTime) {
                        $paid31 = "Missed";
                    } else {
                        $paid31 = "Pending";
                    }
                    //
                    $paidOnScheduledDate32 = $billingRecords
                    ->where('created_at', '>=', $week32DateTime)
                    ->where('created_at', '<', $week33DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate32) {
                        $paid32 = "Paid";
                    } elseif ($currentDate > $week32DateTime) {
                        $paid32 = "Missed";
                    } else {
                        $paid32 = "Pending";
                    }
                    //
                    $paidOnScheduledDate33 = $billingRecords
                    ->where('created_at', '>=', $week33DateTime)
                    ->where('created_at', '<', $week34DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate33) {
                        $paid33 = "Paid";
                    } elseif ($currentDate > $week33DateTime) {
                        $paid33 = "Missed";
                    } else {
                        $paid33 = "Pending";
                    }
                    //
                    $paidOnScheduledDate34 = $billingRecords
                    ->where('created_at', '>=', $week34DateTime)
                    ->where('created_at', '<', $week41DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate34) {
                        $paid34 = "Paid";
                    } elseif ($currentDate > $week34DateTime) {
                        $paid34 = "Missed";
                    } else {
                        $paid34 = "Pending";
                    }
                    //
                    $paidOnScheduledDate41 = $billingRecords
                    ->where('created_at', '>=', $week41DateTime)
                    ->where('created_at', '<', $week42DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate41) {
                        $paid41 = "Paid";
                    } elseif ($currentDate > $week41DateTime) {
                        $paid41 = "Missed";
                    } else {
                        $paid41 = "Pending";
                    }
                    //
                    $paidOnScheduledDate42 = $billingRecords
                    ->where('created_at', '>=', $week42DateTime)
                    ->where('created_at', '<', $week43DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate42) {
                        $paid42 = "Paid";
                    } elseif ($currentDate > $week42DateTime) {
                        $paid42 = "Missed";
                    } else {
                        $paid42 = "Pending";
                    }
                    //
                    $paidOnScheduledDate43 = $billingRecords
                    ->where('created_at', '>=', $week43DateTime)
                    ->where('created_at', '<', $week44DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate43) {
                        $paid43 = "Paid";
                    } elseif ($currentDate > $week43DateTime) {
                        $paid43 = "Missed";
                    } else {
                        $paid43 = "Pending";
                    }
                    //
                    $paidOnScheduledDate44 = $billingRecords
                    ->where('created_at', '>=', $week44DateTime)
                    ->where('created_at', '<', $week51DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate44) {
                        $paid44 = "Paid";
                    } elseif ($currentDate > $week44DateTime) {
                        $paid44 = "Missed";
                    } else {
                        $paid44 = "Pending";
                    }
                    //



                    $paidOnScheduledDate51 = $billingRecords
                    ->where('created_at', '>=', $week51DateTime)
                    ->where('created_at', '<', $week52DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate51) {
                        $paid51 = "Paid";
                    } elseif ($currentDate > $week51DateTime) {
                        $paid51 = "Missed";
                    } else {
                        $paid51 = "Pending";
                    }
                    //
                    $paidOnScheduledDate52 = $billingRecords
                    ->where('created_at', '>=', $week52DateTime)
                    ->where('created_at', '<', $week53DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate52) {
                        $paid52 = "Paid";
                    } elseif ($currentDate > $week52DateTime) {
                        $paid52 = "Missed";
                    } else {
                        $paid52 = "Pending";
                    }
                    //
                    $paidOnScheduledDate53 = $billingRecords
                    ->where('created_at', '>=', $week53DateTime)
                    ->where('created_at', '<', $week54DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate53) {
                        $paid53 = "Paid";
                    } elseif ($currentDate > $week53DateTime) {
                        $paid53 = "Missed";
                    } else {
                        $paid53 = "Pending";
                    }
                    //
                    $paidOnScheduledDate54 = $billingRecords
                    ->where('created_at', '>=', $week54DateTime)
                    ->where('created_at', '<', $week61DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate54) {
                        $paid54 = "Paid";
                    } elseif ($currentDate > $week54DateTime) {
                        $paid54 = "Missed";
                    } else {
                        $paid54 = "Pending";
                    }
                    //

                    $paidOnScheduledDate61 = $billingRecords
                    ->where('created_at', '>=', $week61DateTime)
                    ->where('created_at', '<', $week62DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate61) {
                        $paid61 = "Paid";
                    } elseif ($currentDate > $week61DateTime) {
                        $paid61 = "Missed";
                    } else {
                        $paid61 = "Pending";
                    }
                    //
                    $paidOnScheduledDate62 = $billingRecords
                    ->where('created_at', '>=', $week62DateTime)
                    ->where('created_at', '<', $week63DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate62) {
                        $paid62 = "Paid";
                    } elseif ($currentDate > $week62DateTime) {
                        $paid62 = "Missed";
                    } else {
                        $paid62 = "Pending";
                    }
                    //
                    $paidOnScheduledDate63 = $billingRecords
                    ->where('created_at', '>=', $week63DateTime)
                    ->where('created_at', '<', $week64DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate63) {
                        $paid63 = "Paid";
                    } elseif ($currentDate > $week63DateTime) {
                        $paid63 = "Missed";
                    } else {
                        $paid63 = "Pending";
                    }
                    //
                    $paidOnScheduledDate64 = $billingRecords
                    ->where('created_at', '>=', $week64DateTime)
                    ->where('created_at', '<', $week71DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate64) {
                        $paid64 = "Paid";
                    } elseif ($currentDate > $week64DateTime) {
                        $paid64 = "Missed";
                    } else {
                        $paid64 = "Pending";
                    }
                    //
                        return $week11Formatted . '---' . $paid11 ."\n_________________________________________________________\n" .
                                $week12Formatted . '---' . $paid12 ."\n_________________________________________________________\n" .
                                $week13Formatted . '---' . $paid13 ."\n_________________________________________________________\n" .
                                $week14Formatted . '---' . $paid14 ."\n_________________________________________________________\n" .
                                $week21Formatted . '---' . $paid21 ."\n_________________________________________________________\n" .
                                $week22Formatted . '---' . $paid22 ."\n_________________________________________________________\n" .
                                $week23Formatted . '---' . $paid23 ."\n_________________________________________________________\n" .
                                $week24Formatted . '---' . $paid24 ."\n_________________________________________________________\n" .
                                $week31Formatted . '---' . $paid31 ."\n_________________________________________________________\n" .
                                $week32Formatted . '---' . $paid32 ."\n_________________________________________________________\n" .
                                $week33Formatted . '---' . $paid33 ."\n_________________________________________________________\n" .
                                $week34Formatted . '---' . $paid34 ."\n_________________________________________________________\n" .
                                $week41Formatted . '---' . $paid41 ."\n_________________________________________________________\n" .
                                $week42Formatted . '---' . $paid42 ."\n_________________________________________________________\n" .
                                $week43Formatted . '---' . $paid43 ."\n_________________________________________________________\n" .
                                $week44Formatted . '---' . $paid44 ."\n_________________________________________________________\n" .
                                $week51Formatted . '---' . $paid51 ."\n_________________________________________________________\n" .
                                $week52Formatted . '---' . $paid52 ."\n_________________________________________________________\n" .
                                $week53Formatted . '---' . $paid53 ."\n_________________________________________________________\n" .
                                $week54Formatted . '---' . $paid54 ."\n_________________________________________________________\n" .
                                $week61Formatted . '---' . $paid61 ."\n_________________________________________________________\n" .
                                $week62Formatted . '---' . $paid62 ."\n_________________________________________________________\n" .
                                $week63Formatted . '---' . $paid63 ."\n_________________________________________________________\n" .
                                $week64Formatted . '---' . $paid64;

                    } else{
                        return "Error";
                    }
                }),
                ])
            ])
        ])
    ]);
}
private static function calculatePaymentStatus(Applicant $record): string
{
    $currentDate = Carbon::now();
    $nextPaymentDate = Carbon::parse(self::calculateCurrentPaymentSchedule($record));

    $billingRecords = $record->billing->where('billing_status', 'remitted');

    $paidOnScheduledDate = $billingRecords
        ->where('created_at', '>=', $nextPaymentDate->startOfDay())
        ->where('created_at', '<', $nextPaymentDate->endOfDay())
        ->isNotEmpty();
    $paidWithinMonth = $billingRecords
        ->where('created_at', '>=', $currentDate->startOfMonth())
        ->isNotEmpty();

   if ($paidOnScheduledDate) {
        return "Paid";
    } elseif ($paidWithinMonth) {
        return "Paid";
    } elseif ($currentDate->lessThanOrEqualTo($nextPaymentDate)) {
        return "Missed";
    } else {
        return "Pending";
    }
}
}
