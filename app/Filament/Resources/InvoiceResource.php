<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Applicant;
use App\Models\Billing;
use App\Models\Invoice;
use App\Models\User;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class InvoiceResource extends Resource
{
    protected static ?string $model = Billing::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $activeNavigationIcon = 'heroicon-s-credit-card';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'My Loan';
    public static function getLabel(): string
    {
        return 'Invoice'; // Replace this with your custom label
    }
    public static function shouldRegisterNavigation(): bool
    {
        $userole = Auth::user();
        $user = $userole->role->name;
        return $user && $user === 'Customer';
    }
    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $transactionNumber = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
        $userId = auth()->user()->id;
        $user1 = User::find($userId);

        $applicant = $user1->applicant->first(); // Retrieve the first (and hopefully only) applicant associated with the user

        $applicantId = optional($applicant)->id;
        return $form
            ->schema([
                Forms\Components\Section::make([
                Forms\Components\Hidden::make('transaction_number')
                ->default($transactionNumber),
                Forms\Components\Hidden::make('user_id')
                ->default($user->id),
                Forms\Components\Hidden::make('applicant_user_id')
                ->default($user->id),
                Forms\Components\Hidden::make('branch_id')
                ->default($user->branch_id),
                Forms\Components\Hidden::make('applicant_id')
                ->default($applicantId),
                Forms\Components\FileUpload::make('image')
                ->image()
                ->label('Transaction Screenshot')
                ->preserveFilenames()
                ->imageEditor()
                ->required()
                ->imageEditorAspectRatios([
                    '16:9',
                    '4:3',
                    '1:1',
                ])
                ->columnSpan(span:2)
                ->imageEditorEmptyFillColor('#000000')
                ->imageEditorMode(2),
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
                Forms\Components\TextInput::make('cashier')
                ->label('E-wallet type')
                ->placeholder('Enter your E-wallet Type')
                ->maxLength(255),
                PhoneInput::make('phone')
                ->countryStatePath('php')
                ->required()
                ->rule(rule:'numeric')
                ->label('Phone number'),
            ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultPaginationPageOption(5)
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
                Tables\Columns\TextColumn::make('created_at')
                ->label('Date')
                ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\Action::make('Invoice')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('success')
                    ->url(fn (Billing $record) => route('invoice', $record))
                    ->openUrlInNewTab(),

                    ])            ->button()
                    ->color('warning')
                    ->label('Actions')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $query = parent::getEloquentQuery();
        if ($user->role->name === 'Customer') {
            return $query
                ->where('applicant_user_id', $user->id)
                ->withoutGlobalScopes([SoftDeletingScope::class]);
        }

        // Make sure to return a default query if the above condition is not met.
        return $query;
    }

}
