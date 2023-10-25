<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicantResource\Pages;
use App\Models\Applicant;
use App\Models\Bike;
use App\Models\Philbrgy;
use App\Models\Philmuni;
use App\Models\Philprovince;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Wizard;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneInputColumn;
use App\Models\Post;
use Filament\Forms\Components\Select;

class ApplicantResource extends Resource
{
    protected static ?string $model = Applicant::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $activeNavigationIcon = 'heroicon-s-users';
    protected static ?string $navigationGroup = 'Loan';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'first';
    public static function getGloballySearchableAttributes(): array
{
    return ['last', 'first', 'middle',];
}

    public static function getLabel(): string
    {
        return 'Loan applicant';
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
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Wizard::make([
                        Wizard\Step::make('Requirement')
                            ->icon('heroicon-o-identification')
                                ->schema([
                                    Forms\Components\Select::make('user_id')
                                        ->label('Email')
                                        ->relationship('user', 'email')
                                        ->native(false)
                                        ->required()
                                        ->preload()
                                        ->createOptionForm([
                                            Forms\Components\Hidden::make('role_id')
                                                ->default('4'),
                                            Forms\Components\Hidden::make('branch_id')
                                                ->default($user->branch_id),
                                            Forms\Components\TextInput::make('name')
                                                ->required()
                                                ->label('User name')
                                                ->placeholder('Enter your Username')
                                                ->dehydrateStateUsing(fn (string $state): string => ucwords($state))
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('first')
                                                ->required()
                                                ->label('First name')
                                                ->placeholder('Enter your First name')
                                                ->dehydrateStateUsing(fn (string $state): string => ucwords($state))
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('middle')
                                                ->label('Middle name(Optional)')
                                                ->placeholder('Enter your Middle name(Optional)')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('last')
                                                ->required()
                                                ->label('Last name')
                                                ->placeholder('Enter your Last name')
                                                ->dehydrateStateUsing(fn (string $state): string => ucwords($state))
                                                ->maxLength(255),
                                                \Ysfkaya\FilamentPhoneInput\Forms\PhoneInput::make('phone')
                                                ->countryStatePath('php')
                                                ->required()
                                                ->rule(rule:'numeric')
                                                ->label('Phone number'),
                                            Forms\Components\TextInput::make('email')
                                                ->email()
                                                ->required()
                                                ->placeholder('Enter the Email address')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('password')
                                                ->password()
                                                ->same('passwordConfirmation')
                                                ->placeholder('Enter your password')
                                                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                                ->dehydrated(fn ($state) => filled($state))
                                                ->required(fn (string $context): bool => $context === 'create')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('passwordConfirmation')
                                                ->password()
                                                ->required()
                                                ->placeholder('Confirm your password')
                                                ->maxLength(255)
                                    ]),
                                    Forms\Components\Hidden::make('user_id')
                                    ->default($user->id),
                                    Forms\Components\Hidden::make('branch_id')
                                    ->default($user->branch_id),
                                    Forms\Components\FileUpload::make('picture')
                                    ->image()
                                    ->label('Applicant picture')
                                    ->preserveFilenames()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->required()
                                    ->columnSpan(span:2),
                                    Forms\Components\Select::make('valid_id_list')
                                    ->options([
                                        'UMID' => 'UMID',
                                        'Driver License' => 'Drivers license',
                                        'Philhealth Card' => 'Philhealth card',
                                        'SSS ID' => 'SSS ID',
                                        'Passport' => 'Passport',
                                        'Tin_ID' => 'TIN ID',
                                        'Voter ID' => 'Voters ID',
                                        'Postal ID' => 'Postal ID',
                                        ])
                                        ->native(false)
                                        ->required()
                                        ->preload()
                                    ->placeholder('Select an ID')
                                    ->label('Choose your ID'),
                                    Forms\Components\FileUpload::make('valid_id')
                                    ->image()
                                    ->preserveFilenames()
                                    ->imageEditor()
                                    ->required()
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->columnSpan(span:2),
                                    Forms\Components\FileUpload::make('barangay_clearance')
                                    ->image()
                                    ->preserveFilenames()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->required()
                                    ->columnSpan(span:2),
                                ]),
                        Wizard\Step::make('Personal Information')
                        ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\TextInput::make('first')
                                ->minLength(2)
                                ->dehydrateStateUsing(fn (string $state): string => ucwords($state))
                                ->maxLength(255)
                                ->required()
                                ->placeholder('Enter your First name')
                                ->label('First name'),
                                Forms\Components\TextInput::make('middle')
                                ->minLength(2)
                                ->dehydrateStateUsing(fn (string $state): string => ucwords($state))
                                ->placeholder('Enter your Middle name')
                                ->maxLength(255)
                                ->label('Middle name (Optional)'),
                                Forms\Components\TextInput::make('last')
                                ->minLength(2)
                                ->maxLength(255)
                                ->placeholder('Enter your Last name')
                                ->dehydrateStateUsing(fn (string $state): string => ucwords($state))
                                ->required()
                                ->label('Last name'),
                                Forms\Components\TextInput::make('age')
                                ->required()
                                ->placeholder('Enter your Age')
                                ->rule(rule:'numeric')
                                ->label('Age'),
                                Forms\Components\Select::make('gender')
                                ->options([
                                    'Male' => 'Male',
                                    'Female' => 'Female'
                                    ])
                                ->native(false)
                                ->required()
                                ->preload()
                                ->placeholder('Select a Gender')
                                ->label('Gender'),
                                Forms\Components\Select::make('civil')
                                ->options([
                                    'Single' => 'Single',
                                    'Married' => 'Married',
                                    'Divorced' => 'Divorced',
                                    'Widowed' => 'Widowed',
                                    ])
                                    ->native(false)
                                    ->placeholder('Select a Civil Status')
                                    ->required()
                                    ->preload()
                                ->label('Civil Status'),
                                Forms\Components\TextInput::make('religion')
                                ->minLength(2)
                                ->placeholder('Enter your religion')
                                ->dehydrateStateUsing(fn (string $state): string => ucwords($state))
                                ->maxLength(255)
                                ->required()
                                ->label('Religion'),
                                Forms\Components\TextInput::make('occupation')
                                ->minLength(2)
                                ->dehydrateStateUsing(fn (string $state): string => ucwords($state))
                                ->maxLength(255)
                                ->required()
                                ->placeholder('Enter your Occupation')
                                ->label('Ocuppation'),
                                Forms\Components\TextInput::make('spouse')
                                ->minLength(2)
                                ->placeholder('Enter your Spouse')
                                ->maxLength(255)
                                ->label('Spouse (Optional)'),
                                Forms\Components\TextInput::make('occupation_spouse')
                                ->minLength(2)
                                ->placeholder('Enter your Spouse occupation')
                                ->maxLength(255)
                                ->label('Spouse Occupation (Optional)'),
                                Forms\Components\TextInput::make('contact_spouse')
                                ->placeholder('Enter your Contact number')
                                ->rule(rule:'numeric')
                                ->label('Spouse Contact number (Optional)'),
                            ])->columns(3)
                            ,
                        Wizard\Step::make('Address')
                        ->icon('heroicon-s-map-pin')
                            ->schema([
                                Forms\Components\Select::make('province')
                                ->reactive()
                                ->preload()
                                ->native(false)
                                ->label('Province Name')
                                ->options(function () {
                                    return Philprovince::all()->pluck('provDesc', 'provDesc');
                                }),
                                Forms\Components\Select::make('city')
                                ->reactive()
                                ->preload()
                                ->native(false)
                                ->label('City/Municipality Name')
                                ->options(function (callable $get) {
                                    $provCode = optional(Philprovince::where('provDesc', $get('province'))->first());
                                    return Philmuni::where('provCode', '=', $provCode->provCode ?? '')->pluck('citymunDesc', 'citymunDesc');
                                }),
                            Forms\Components\Select::make('barangay')
                                ->label('Barangay Name')
                                ->preload()
                                ->native(false)
                                ->options(function (callable $get) {
                                    $provCode = optional(Philprovince::where('provDesc', $get('province'))->first());
                                    $muniCode = optional(Philmuni::where('provCode', '=', $provCode->provCode ?? '')->where('citymunDesc', $get('city'))->first());
                                    return Philbrgy::where('citymunCode', '=', $muniCode->citymunCode ?? '')->pluck('brgyDesc', 'brgyDesc');
                                }),
                            Forms\Components\TextInput::make('unit')
                                ->minLength(2)
                                ->placeholder('Enter the Unit no., floor, building, street')
                                ->maxLength(255)
                                ->required()
                                ->dehydrateStateUsing(fn (string $state): string => ucwords($state))
                                ->label('Unit no., floor, building, street'),
                            ])->columns(2),
                        Wizard\Step::make('Loan Details')
                            ->icon('heroicon-s-user')
                            ->schema([
                        Forms\Components\Select::make('bike_id')
                                ->options(function () {
                                    $userBranchId = Auth::user()->branch_id;
                                    return Bike::where('status', 'approved')
                                        ->where('branch_id', $userBranchId)
                                        ->pluck('name', 'id');
                                })
                                ->label('Bike')
                                ->native(false)
                                ->required()
                                ->preload(),
                        Forms\Components\Select::make('installment')
                                ->native(false)
                                ->required()
                                ->placeholder('Weekly/Monthly')
                                ->label('Installment')
                                ->options([
                                    '4' => 'Weekly',
                                    '1' => 'Monthly',
                                        ]),
                        Forms\Components\Select::make('term')
                                ->native(false)
                                ->required()
                                ->label('Loan term')
                                ->preload()
                                ->placeholder('Loan Term')
                                ->options([
                                            '4' => '4 Months',
                                            '5' => '5 Months',
                                            '6' => '6 Months',
                                ]),
                        // Forms\Components\TextInput::make('down')
                        //         ->required()
                        //         ->placeholder('Enter your down payment')
                        //         ->rule(rule:'numeric')
                        //         ->label('Down payments'),
                            ])->columns(2),
                    ])
                ])
            ]);
    }
    public static function table(Table $table): Table
    {
        $user = Auth::user();
        return $table
        ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\TextColumn::make('fullname')
                ->searchable()
                ->label('Applicant name')
                ->getStateUsing(function (Applicant $record) {
                    return "{$record->last}, {$record->first} {$record->middle}";
                })
                // ->getStateUsing(function (Applicant $record) {
                //     return $record->updateCompute();
                // })
                // ->description(function (Applicant $record) {
                //     return $record->updateremaining();
                // })
                ->searchable(),
                Tables\Columns\TextColumn::make('bike.name')
                ->searchable(),
                Tables\Columns\TextColumn::make('bike.price')
                ->sortable()
                ->numeric(
                    decimalPlaces: 2,
                    decimalSeparator: '.',
                    thousandsSeparator: ',',
                )
                ->label('Total contract price'),
                Tables\Columns\TextColumn::make('status')
                ->Badge()
                ->visible(function () {
                    $user = Auth::user();
                    return $user->role->name === 'Admin' || $user->role->name === 'Staff';
                })
                ->getStateUsing(function (Applicant $record): string {
                    if ($record->isApproved()) {
                        return 'Approved';
                    } elseif ($record->isRejected()) {
                        return 'Rejected';
                    } else {
                        return 'Pending';
                    }
                })
                ->colors([
                    'success' => 'Approved',
                    'danger' => 'Rejected',
                    'warning' => 'Pending'
                ]),
                Tables\Columns\TextColumn::make('ci_status')
                ->Badge()
                ->label('CI status')
                ->getStateUsing(function (Applicant $record): string {
                    if ($record->isCIApproved()) {
                        return 'Approved';
                    } elseif ($record->isCIRejected()) {
                        return 'Rejected';
                    } else {
                        return 'Pending';
                    }
                })
                ->colors([
                    'success' => 'Approved',
                    'danger' => 'Rejected',
                    'warning' => 'Pending'
                ]),

            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                ->label('Archive Record')
                ->native(false)
                ->trueLabel(' With Archive Record')
                ->falseLabel('Archive Record Only')
                ->placeholder('All')
                ->default(null),
                Tables\Filters\TernaryFilter::make('status')
                ->label('Approval status')
                ->trueLabel('Approved')
                ->falseLabel('Pending')
                ->native(false)
                ->placeholder('All')
                ->default(null)
                ->nullable(),
            ])
            ->actions([
                ActionGroup::make([
                Tables\Actions\Action::make('Approved')
                ->form([
                    DatePicker::make('ci_sched')
                    ->required()
                    ->native(false)
                    ->hoursStep(2)
                    ->placeholder('mm/dd/yyyy')
                    ->minutesStep(15)
                    ->secondsStep(10)
                    ->firstDayOfWeek(7)
                    ->label('CI Schedule'),
                ])
                ->action(function (Applicant $record, $data) {
                    $selectedDate = $data['ci_sched'];
                    $formattedDate = \Carbon\Carbon::parse($selectedDate)->toDateString();


                    $bike = $record->bike;
                    $interest_rate = $bike->rate;
                    $principal = $bike->price;
                    $term = $record->term;
                    $perweek = $record->installment;
                    $bike_price = $record->bike->price;
                    $down = $bike->down;
                    $decimal_rate = $interest_rate / 100;
                    $computed_interest = $principal * $decimal_rate;
                    $complete = $principal + $computed_interest;

                    $interest = $principal * $decimal_rate;
                    $plus = $principal + $computed_interest;
                    $afterdownpayment = $plus - $down;
                    $payment = ($afterdownpayment / $term) / $perweek;
                    $full_name = $record->first . ' ' . $record->middle . ' ' . $record->last;
                    $startDate = now();
                    $endDate = $startDate->copy()->addMonths($term);
                    $record->update(['status' => 'approved',
                                    'total_interest' => $interest,
                                    'plus' => $plus,
                                    'payment' => $payment,
                                    'remaining_balance' => $afterdownpayment,
                                    'customer_name' => $full_name,
                                    'bike_price' => $bike_price,
                                    'down_payment' => $down,
                                    'start' => $startDate,
                                    'end' => $endDate,
                                    'ci_sched' => $formattedDate,
                                    // 'week11' => $week11,
                                    // 'month1' => $month1,
                                    ]);

                })
                ->requiresConfirmation()
                ->hidden(fn (Applicant $record): bool => $record->status === 'approved' || $record->status === 'rejected')
                ->color('success')
                ->visible(function () {
                    $user = Auth::user();
                    return $user->role->name === 'Admin' || $user->role->name === 'Staff';
                })
                ->icon('heroicon-o-clipboard-document-check'),
                Tables\Actions\Action::make('Approve')
                ->action(function (Applicant $record, $data) {
                    $term = $record->term;
                    $startDate = now();

                    $week11 = now()->addweeks(1);
                    $week12 = now()->addweeks(2);
                    $week13 = now()->addweeks(3);
                    $week14 = now()->addweeks(4);
                    $week21 = now()->addweeks(5);
                    $week22 = now()->addweeks(6);
                    $week23 = now()->addweeks(7);
                    $week24 = now()->addweeks(8);
                    $week31 = now()->addweeks(9);
                    $week32 = now()->addweeks(10);
                    $week33 = now()->addweeks(11);
                    $week34 = now()->addweeks(12);
                    $week41 = now()->addweeks(13);
                    $week42 = now()->addweeks(14);
                    $week43 = now()->addweeks(15);
                    $week44 = now()->addweeks(16);
                    $week51 = now()->addweeks(17);
                    $week52 = now()->addweeks(18);
                    $week53 = now()->addweeks(19);
                    $week54 = now()->addweeks(20);
                    $week61 = now()->addweeks(21);
                    $week62 = now()->addweeks(22);
                    $week63 = now()->addweeks(23);
                    $week64 = now()->addweeks(24);
                    $month1 = now()->addmonths(1);
                    $month2 = now()->addmonths(2);
                    $month3 = now()->addmonths(3);
                    $month4 = now()->addmonths(4);
                    $month5 = now()->addmonths(5);
                    $month6 = now()->addmonths(6);
                    $endDate = $startDate->copy()->addMonths($term);
                    $record->update(['ci_status' => 'approved',
                                        'start' => $startDate,
                                        'end' => $endDate,
                                        'week11' => $week11,
                                        'week12' => $week12,
                                        'week13' => $week13,
                                        'week14' => $week14,
                                        'week21' => $week21,
                                        'week22' => $week22,
                                        'week23' => $week23,
                                        'week24' => $week24,
                                        'week31' => $week31,
                                        'week32' => $week32,
                                        'week33' => $week33,
                                        'week34' => $week34,
                                        'week41' => $week41,
                                        'week42' => $week42,
                                        'week43' => $week43,
                                        'week44' => $week44,
                                        'week51' => $week51,
                                        'week52' => $week52,
                                        'week53' => $week53,
                                        'week54' => $week54,
                                        'week61' => $week61,
                                        'week62' => $week62,
                                        'week63' => $week63,
                                        'week64' => $week64,
                                        'month1' => $month1,
                                        'month2' => $month2,
                                        'month3' => $month3,
                                        'month4' => $month4,
                                        'month5' => $month5,
                                        'month6' => $month6,
                                    ]);
                })
                ->visible(function () {
                    $user = Auth::user();
                    return $user->role->name === 'Collector' ;
                })
                ->requiresConfirmation()
                ->hidden(fn (Applicant $record): bool => $record->ci_status === 'rejected' || $record->ci_status === 'approved' ||  $record->status === 'pending') // Hide if already approved
                ->color('success')
                ->icon('heroicon-o-check-circle'),
                Tables\Actions\Action::make('Reject')
                ->action(function (Applicant $record) {
                    $record->update(['status' => 'rejected']);

                })
                ->visible(function () {
                    $user = Auth::user();
                    return $user->role->name === 'Admin' ;
                })
                ->requiresConfirmation()
                ->hidden(fn (Applicant $record): bool => $record->status === 'rejected' || $record->status === 'approved') // Hide if already approved
                ->color('danger')
                ->icon('heroicon-o-archive-box-x-mark'),
                Tables\Actions\Action::make('Rejected')
                ->action(function (Applicant $record) {
                    $record->update(['ci_status' => 'rejected']);
                })
                ->visible(function () {
                    $user = Auth::user();
                    return $user->role->name === 'Collector' ;
                })
                ->requiresConfirmation()
                ->hidden(fn (Applicant $record): bool => $record->ci_status === 'rejected' || $record->ci_status === 'approved') // Hide if already approved
                ->color('danger')
                ->icon('heroicon-o-archive-box-x-mark'),
                Tables\Actions\EditAction::make()
                ->color('warning'),
                Tables\Actions\ViewAction::make()
                // ->slideOver()
                ->color('primary'),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->button()
            ->color('warning')
            ->label('Actions')
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListApplicants::route('/'),
            'create' => Pages\CreateApplicant::route('/create'),
            'edit' => Pages\EditApplicant::route('/{record}/edit'),
        ];
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
    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        $roleName = $user->role->name;
        $branchId = $user->branch_id;
        if ($roleName === 'Admin') {
            return static::getModel()::where('status', '=', 'pending')->count();
        } elseif ($roleName === 'Collector') {

            return static::getModel()::where('ci_status', '=', 'pending')
            ->where('branch_id', $branchId)
            ->count();
        }
        return null;
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
                    ->label('Fullname'),
                    Infolists\Components\TextEntry::make('gender')
                    // ->color('warning')
                    // ->badge()
                    ->label('Gender'),
                    Infolists\Components\TextEntry::make('age')
                    ->label('Age'),
                    Infolists\Components\TextEntry::make('contact_applicant')
                    ->label('Contact number'),
                    Infolists\Components\TextEntry::make('civil')
                    ->label('Civil status'),
                    Infolists\Components\TextEntry::make('religion')
                    ->label('Religion'),
                    Infolists\Components\TextEntry::make('occupation')
                    ->label('Occupation'),
                    Infolists\Components\TextEntry::make('spouse')
                    ->label('Spouse')
                    ->getStateUsing(function (Applicant $record) {
                        return $record->spouse ?? 'none';
                    }),
                    Infolists\Components\TextEntry::make('spouse_contact')
                    ->label('Contact number')
                    ->getStateUsing(function (Applicant $record) {
                        return $record->spouse_contact ?? 'none';
                    }),
                ])
            ])
                ]),
        Infolists\Components\Fieldset::make('Address')
        ->schema([
        Infolists\Components\Section::make()->schema([
            Infolists\Components\Grid::make(4)->schema([
                Infolists\Components\TextEntry::make('unit')
                ->label('Unit no., street'),
                Infolists\Components\TextEntry::make('barangay')
                // ->color('warning')
                // ->badge()
                ->label('Barangay'),
                Infolists\Components\TextEntry::make('city')
                ->label('City/Municipality'),
                Infolists\Components\TextEntry::make('province')
                ->label('Province'),

                ])
            ])
            ]),
            Infolists\Components\Fieldset::make('Loan Details')
            ->schema([
            Infolists\Components\Section::make()->schema([
                Infolists\Components\Grid::make(3)->schema([
                    Infolists\Components\TextEntry::make('bike.name')
                    ->label('Bike name'),
                    Infolists\Components\TextEntry::make('term')
                    // ->color('warning')
                    // ->badge()
                    ->getStateUsing(function (Applicant $record) {
                        return "{$record->term} Months";
                    })
                    ->label('Loan term'),
                    Infolists\Components\TextEntry::make('installment')
                    ->label('Payment Mode')
                    ->getStateUsing(function (Applicant $record) {
                        $or = $record->installment;
                        if ($or == 4) {
                            return "Weekly";
                        } elseif ($or == 1) {
                            return "Monthly";
                        } else {
                            return "Error";
                        }
                    }),
                    ])
                ])
                ]),
                Infolists\Components\Fieldset::make('Barangay Clearance')
                ->schema([
                Infolists\Components\Section::make()->schema([
                    Infolists\Components\Grid::make(1)->schema([
                        Infolists\Components\ImageEntry::make('barangay_clearance')
                        ->hiddenlabel()
                        ->width(750)
                        ->height(750),
                        ])
                    ])
                    ]),
                    Infolists\Components\Fieldset::make('Valid ID')
                    ->schema([
                    Infolists\Components\Section::make()->schema([
                        Infolists\Components\Grid::make(1)->schema([
                            Infolists\Components\TextEntry::make('valid_id_list')
                            ->label('ID type'),
                            Infolists\Components\ImageEntry::make('valid_id')
                            ->hiddenlabel()
                            ->width(750)
                            ->size(750)
                            ->height(300),
                            ])
                        ])
                        ]),
        // Infolists\Components\Fieldset::make('Documents')
        // ->schema([
        // Infolists\Components\Section::make()->schema([
        //     Infolists\Components\Grid::make(4)->schema([
        //         Infolists\Components\TextEntry::make('unit')
        //         ->label('Unit no., floor, building, street'),
        //         Infolists\Components\TextEntry::make('valid_id')
        //         // ->color('warning')
        //         // ->badge()
        //         ->label('Barangay'),
        //         Infolists\Components\TextEntry::make('city')
        //         ->label('City/Municipality'),
        //         Infolists\Components\TextEntry::make('province')
        //         ->label('Province'),

        //         ])
            // ])
        // ])
    ]);
}

}
