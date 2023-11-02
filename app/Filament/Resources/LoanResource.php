<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Filament\Resources\LoanResource\RelationManagers;
use App\Models\Applicant;
use App\Models\Payment;
use App\Models\PaymentSchedule;
use App\Tables\Columns\ProgressColumn;
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
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
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
        ->defaultSort('payment_schedule_slug', 'desc')
        ->deferLoading()
        ->paginatedWhileReordering()
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
                Tables\Columns\TextColumn::make('payment_schedule_slug')
                ->label('Payment Schedule')
                ->getStateUsing(function (Applicant $record) {
                        return $record->updateSched();
                     })
                ->description(function (Applicant $record) {
                    return $record->updateDescription();
                }),
                ProgressColumn::make('progress')
                ->getStateUsing(function (Applicant $record) {
                    $plus = $record->plus;
                    $down = $record->down_payment;
                    $remaining = $record->remaining_balance;
                    $total = $plus - $down;
                    $total1 = $total - $remaining;
                    $percentage = ($total1 / $total) * 100;
                    return $percentage;
                })

            ])
            ->filters([
                Filter::make('created_at')
                ->form([
                    DatePicker::make('created_from'),
                    DatePicker::make('created_until'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('payment_schedule', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('payment_schedule', '<=', $date),
                        );
                }),
                SelectFilter::make('is_paid')
                ->options([
                    'Paid' => 'Paid',
                    'Pending' => 'Pending',
                    'Missed' => 'Missed',
                ])
                ->native(false)
                ->label('Payment status'),
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
                         ->where('is_status', 'active')
                         ->where('ci_status', 'approved');
        } elseif ($user->role->name === 'Staff' || $user->role->name === 'Collector') {
            return $query->where('branch_id', $user->branch_id)
                         ->where('remaining_balance', '>', 0)
                         ->where('ci_status', 'approved')
                         ->where('is_status', 'active')
                         ->where(function ($query) {
                             $query->where('status', 'approved');
                         });
        } elseif ($user->role->name === 'Customer' ) {
            return $query->where('user_id', $user->id)
                         ->where('remaining_balance', '>', 0)
                         ->where('is_status', 'active')
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
            // Calculate the next payment date as a week from the start date
            $nextPaymentDate = $startDate->copy()->addWeek();
        } elseif ($installment === '1') {
            // Calculate the next payment date as a month from the start date
            $nextPaymentDate = $startDate->copy()->addMonth();
        }

        // Check if the calculated nextPaymentDate is before today and before the end date
        while ($nextPaymentDate->isBefore($today) && $nextPaymentDate->isBefore($endDate)) {
            if ($installment === '4') {
                $nextPaymentDate->addWeek();
            } elseif ($installment === '1') {
                $nextPaymentDate->addMonth();
            }
        }

        // Check if nextPaymentDate has exceeded the end date
        if ($nextPaymentDate->isAfter($endDate)) {
            return $endDate->format('F j, Y'); // Return the end date
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
            Infolists\Components\Section::make('Installment')
            ->description('Loan details')
            ->schema([
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
                ])->collapsed(),
        Infolists\Components\Section::make('Payment History')
        ->description('History of payment schedules')
        ->schema([
            Infolists\Components\Grid::make(3)->schema([
                Infolists\Components\TextEntry::make('payment_info')
                ->label('Payment Info')
                ->getStateUsing(function (Applicant $record) {
                    return $record->updatepaid();
                 }),
                // ->getStateUsing(function (Applicant $record) {
                //     return $record->updateDate();
                // })
                // ->getStateUsing(function (Applicant $applicant) {
                //     $billingRecords = $applicant->billing->where('billing_status', 'remitted');

                //     $startDate = new DateTime($applicant->start);
                //     $endDate = new DateTime($applicant->end);
                //     $installmentFrequency = $applicant->installment;
                //     $currentDate = Carbon::now();
                //     $paymentSchedule = [];

                //     while ($startDate <= $endDate) {
                //         $paymentDate = $startDate->format('F j, Y');

                //         $status = 'Pending';

                //         if ($currentDate->greaterThanOrEqualTo($startDate)) {
                //             $status = 'Missed';

                //             // Check if a payment was made between the current payment date and the next one
                //             $nextPaymentDate = clone $startDate;
                //             if ($installmentFrequency == 4) {
                //                 $nextPaymentDate->add(new DateInterval('P1W'));
                //             } elseif ($installmentFrequency == 1) {
                //                 $nextPaymentDate->add(new DateInterval('P1M'));
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
                //             $startDate->add(new DateInterval('P1W'));
                //         } elseif ($installmentFrequency == 1) {
                //             $startDate->add(new DateInterval('P1M'));
                //         }
                //     }

                //     return implode("\n_________________________________________________________\n", $paymentSchedule);
                // }),

                ])
            ])->collapsed()
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
