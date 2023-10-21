<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoannnResource\Pages;
use App\Filament\Resources\LoannnResource\RelationManagers;
use App\Models\Applicant;
use DateInterval;
use DateTime;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class LoannnResource extends Resource
{
    protected static ?string $model = Applicant::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $activeNavigationIcon = 'heroicon-s-clipboard-document';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'My Loan';
    public static function getLabel(): string
    {
        return 'Schedule'; // Replace this with your custom label
    }
    public static function shouldRegisterNavigation(): bool
    {
        $userole = Auth::user();
        $user = $userole->role->name;
        return $user && $user=== 'Customer';
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
                // Tables\Columns\TextColumn::make('payment_schedulee')
                // ->label('Current Payment Schedule')
                // ->getStateUsing(fn (Applicant $record) => self::calculateCurrentPaymentSchedule($record)),
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
                    Tables\Actions\Action::make('Pay Bills')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('success')
                    ->url(fn (Applicant $record) => route('payment', $record))
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
            'index' => Pages\ListLoannns::route('/'),
            'create' => Pages\CreateLoannn::route('/create'),
            // 'edit' => Pages\EditLoannn::route('/{record}/edit'),
            // 'create' => Pages\CreateLoan::route('/create'),
            // 'edit' => Pages\EditLoan::route('/{record}/edit'),
        ];
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
    } elseif ($user->role->name === 'Customer') {
        return $query->where('user_id', $user->id)
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
                ->getStateUsing(function (Applicant $applicant) {
                    $billingRecords = $applicant->billing->where('billing_status', 'remitted');

                    $startDate = new DateTime($applicant->start);
                    $endDate = new DateTime($applicant->end);
                    $installmentFrequency = $applicant->installment;
                    $currentDate = Carbon::now();
                    $paymentSchedule = [];

                    while ($startDate <= $endDate) {
                        $paymentDate = $startDate->format('F j, Y');

                        $status = 'Pending';

                        if ($currentDate->greaterThanOrEqualTo($startDate)) {
                            $status = 'Missed';

                            // Check if a payment was made between the current payment date and the next one
                            $nextPaymentDate = clone $startDate;
                            if ($installmentFrequency == 4) {
                                $nextPaymentDate->add(new DateInterval('P1W'));
                            } elseif ($installmentFrequency == 1) {
                                $nextPaymentDate->add(new DateInterval('P1M'));
                            }

                            foreach ($billingRecords as $billingRecord) {
                                $recordDate = Carbon::parse($billingRecord->created_at)->format('F j, Y');
                                if ($recordDate >= $nextPaymentDate->format('F j, Y')) {
                                    $status = 'Paid';
                                    break;
                                }
                            }
                        }

                        $paymentSchedule[] = "$paymentDate - $status";

                        if ($installmentFrequency == 4) {
                            $startDate->add(new DateInterval('P1W'));
                        } elseif ($installmentFrequency == 1) {
                            $startDate->add(new DateInterval('P1M'));
                        }
                    }

                    return implode("\n_________________________________________________________\n", $paymentSchedule);
                }),
                ])
            ])
        ])
    ]);
}

}
