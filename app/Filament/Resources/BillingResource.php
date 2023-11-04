<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillingResource\Pages;
use App\Filament\Resources\BillingResource\RelationManagers;
use App\Models\Applicant;
use App\Models\Billing;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Coolsam\SignaturePad\Forms\Components\Fields\SignaturePad;
use Filament\Forms\Components\Radio;


class BillingResource extends Resource
{
    protected static ?string $model = Billing::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $activeNavigationIcon = 'heroicon-s-credit-card';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Loan';
    public static function getLabel(): string
    {
        return 'Invoice'; // Replace this with your custom label
    }
    public static function shouldRegisterNavigation(): bool
    {
        $userole = Auth::user();
        $user = $userole->role->name;
        return $user && $user=== 'Admin' || $user === 'Staff' || $user === 'Collector';
    }
    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $transactionNumber = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT); // Generate a random 8-digit number with leading zeros
        return $form
            ->schema([

                Forms\Components\Section::make([
                    Forms\Components\Hidden::make('billing_status')
                    ->default(function () {
                        $user = Auth::user();
                        if ($user->role->name === 'Admin' || $user->role->name === 'Staff') {
                            return 'remitted'; // Set default to 'remitted' for Admin and Staff
                        } else {
                            return 'processing'; // Set default to 'processing' for other roles
                        }
                    }),
                    Forms\Components\Hidden::make('transaction_number')
                    ->default($transactionNumber),
                    Forms\Components\Hidden::make('user_id')
                    ->default($user->id),
                    Forms\Components\Hidden::make('branch_id')
                    ->default($user->branch_id),
                    Forms\Components\Select::make('applicant_id')
                    ->options(function (Applicant $applicant){
                        $userBranchId = Auth::user()->branch->id;
                        return Applicant::where('status', 'approved')
                            ->where('branch_id', $userBranchId)
                             ->where('ci_status', 'approved')
                            ->where('remaining_balance', '>', 0)
                            ->where('is_status', 'active')
                            ->pluck(DB::raw("CONCAT(first, ' ', middle, ' ', last)"), 'id');
                    })
                    ->label('Customer')
                    ->placeholder('Select a Customer')
                    ->native(false)
                    ->required()
                    ->reactive()
                    ->preload(),
                    Forms\Components\Select::make('payment_type')
                    ->options(function ($get) {
                        $applicantId = $get('applicant_id');
                        $applicant = Applicant::find($applicantId);

                        if ($applicant) {
                            $createdDate = $applicant->created_at;
                            $currentDate = now();


                            $oneMonthAfter = $createdDate->copy()->addMonth();

                            $twoMonthsAfter = $createdDate->copy()->addMonths(2);


                            $options = [
                                'in_partial' => 'In partial',
                                'fullypaid' => 'Fully paid',
                                'custom' => 'Custom Amount',

                            ];

                            // Check if today is between one month after and two months after the created date
                            if ($currentDate->greaterThanOrEqualTo($oneMonthAfter)) {
                                $options['1st_month_paid'] = 'Early repayment';
                            } elseif ($oneMonthAfter->lessThanOrEqualTo($twoMonthsAfter)) {
                                $options['2nd_month_paid'] = 'Early loan termination fee';
                            }


                            return $options;
                        } else {
                            return [];
                        }
                    })

                    ->native(false)
                    ->label('Payment Type')
                    ->required()
                    ->placeholder('Select a payment type')
                    ->reactive()
                    ->afterStateUpdated(function ($set, $get) {
                        $applicantId = $get('applicant_id');
                        $applicant = Applicant::find($applicantId);
                        $amount = 0;
                        $pdf = 0;
                        $interest = 0;
                        $type = $get('payment_type');
                        if ($type === 'in_partial') {
                            $amount = $applicant->payment;
                            $remaining = $applicant->remaining_balance;
                            $pdf = $remaining -  $amount;
                            $applicant_user_id = $applicant->user_id;
                        } elseif ($type === 'fullypaid') {
                            $amount = $applicant->remaining_balance;
                            $amountpdf = $applicant->payment;
                            $remainingpdf = $applicant->remaining_balance;
                            $pdf = $remainingpdf -  $amountpdf;
                            $applicant_user_id = $applicant->user_id;
                        } elseif ($type === '1st_month_paid') {
                            $remaining = $applicant->remaining_balance;
                            $interest = $applicant->total_interest;
                            $month = $remaining - $interest;
                            $amount = $month;
                            $amountpdf = $applicant->payment;
                            $remainingpdf = $applicant->remaining_balance;
                            $pdf = $remainingpdf -  $amountpdf;
                            $applicant_user_id = $applicant->user_id;
                        } elseif ($type === '2nd_month_paid') {
                            $remaining = $applicant->remaining_balance;
                            $interest = $applicant->total_interest;
                            $month = $remaining - $interest;
                            $months = $month + 500;
                            $amount = $months;
                            $amountpdf = $applicant->payment;
                            $remainingpdf = $applicant->remaining_balance;
                            $pdf = $remainingpdf -  $amountpdf;
                            $applicant_user_id = $applicant->user_id;
                        }
                        $set('amount', $amount);
                        $set('interests', $interest);
                        $set('amountpdf', $pdf);
                        $set('applicant_user_id', $applicant_user_id);
                    }),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->reactive()
                    ->placeholder('Enter Amount')
                    ->rule('numeric'),
                Forms\Components\Hidden::make('interests')
                    ->reactive(),
                Forms\Components\Hidden::make('applicant_user_id')
                    ->reactive(),
                Forms\Components\Hidden::make('amountpdf'),
                Forms\Components\TextInput::make('cashier')
                    ->label('Cashier / Authorized Representative')
                    ->default(self::getCashierDefault($user))
                    ->readonly()
                    ->maxLength(255),

                // Forms\Components\TextInput::make('cashier')
                //     ->label('Cashier / Authorized Representative')
                //     ->default(function () use ($user) {
                //         return $user->last . ', ' . $user->first . ' ' . $user->middle.' - ' . $user->role->name;
                //     }) // Set the default value to the concatenated name
                //     ->disabled() // Disable the field
                //     ->maxLength(255),

                SignaturePad::make('signature')
                ->backgroundColor('white')
                ->penColor('black')
                ->strokeMinDistance(2.0)
                ->strokeMaxWidth(2.5)
                ->strokeMinWidth(1.0)
                ->strokeDotSize(2.0),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultPaginationPageOption(5)
        ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('transaction_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Customer')
                    ->searchable()
                    ->getStateUsing(function (Billing $record) {
                        return "{$record->applicant->last}, {$record->applicant->first} {$record->applicant->middle}";
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->sortable()
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    ),
                Tables\Columns\TextColumn::make('cashier')
                    ->label('Cashier / Authorized Representative')
                    ->searchable(),
                    Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime(),
                    Tables\Columns\TextColumn::make('status')
                    ->Badge()
                    ->getStateUsing(function (Billing $record): string {
                        if ($record->isRemitted()) {
                            return 'Remitted';
                        } elseif ($record->isNot_recieved()) {
                            return 'Not received';
                        } else {
                            return 'Processing';
                        }
                    })
                    ->colors([
                        'success' => 'Remitted',
                        'danger' => 'Not received',
                        'warning' => 'Processing'
                    ]),
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
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                }),
                SelectFilter::make('billing_status')
                ->options([
                    'remitted' => 'Remitted',
                    'processing' => 'Processing',
                    'not_received' => 'Not Received',
                ])
                ->native(false)
                ->label('Status'),
            ])
            ->actions([
                ActionGroup::make([
                Tables\Actions\Action::make('Remitted')
                ->action(function (Billing $record) {
                    $record->update(['billing_status' => 'remitted']);
                })
                ->visible(function () {
                    $user = Auth::user();
                    return $user->role->name === 'Staff' || $user->role->name === 'Admin';
                })
                ->requiresConfirmation()
                ->hidden(fn (Billing $record): bool => $record->billing_status === 'not_recieved' || $record->billing_status === 'remitted') // Hide if already approved

                ->color('success')
                ->icon('heroicon-o-check-circle'),
                Tables\Actions\Action::make('not_received')
                ->action(function (Billing $record) {
                    $record->update(['billing_status' => 'not_recieved']);
                })
                ->visible(function () {
                    $user = Auth::user();
                    return $user->role->name === 'Staff' || $user->role->name === 'Admin';
                })
                ->requiresConfirmation()
                ->hidden(fn (Billing $record): bool => $record->billing_status === 'not_recieved' || $record->billing_status === 'remitted') // Hide if already approved
                ->color('danger')
                ->icon('heroicon-o-archive-box-x-mark'),
                Tables\Actions\Action::make('Invoice')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('success')
                ->url(fn (Billing $record) => route('invoice', $record))
                ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make()
                    ->color('primary'),
                ])            ->button()
                ->color('warning')
                ->label('Actions')
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     // Tables\Actions\DeleteBulkAction::make(),
                //     // Tables\Actions\ForceDeleteBulkAction::make(),
                //     // Tables\Actions\RestoreBulkAction::make(),
                // ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListBillings::route('/'),
            'create' => Pages\CreateBilling::route('/create'),
            // 'edit' => Pages\EditBilling::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $query = parent::getEloquentQuery();

        if ($user->role->name === 'Admin') {

            return $query->withoutGlobalScopes([SoftDeletingScope::class]);
        } elseif ($user->role->name === 'Staff' || $user->role->name === 'Collector') {

            return $query->where('branch_id', $user->branch_id)
                ->withoutGlobalScopes([SoftDeletingScope::class]);
        }



        return $query;
    }
    protected static function getCashierDefault($user)
{
    return "{$user->last}, {$user->first} {$user->middle} - {$user->role->name}";
}
protected static function getpartial($applicant)
{
    return "{$applicant->payment}";
}
protected static function getremaining($applicant)
{
    return "{$applicant->remaining}";
}
}
