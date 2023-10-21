<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Widgets;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneInputColumn;
use App\Filament\Resources\UserResource\Widgets\UserStatsOverview;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $activeNavigationIcon = 'heroicon-s-user-group';
    protected static ?int $navigationSort = 19;
    protected static ?string $navigationGroup = 'Settings';
    public static function form(Form $form): Form
    {
        $user = Auth::user();
        return $form
            ->schema([
                Forms\Components\Section::make([
                Forms\Components\Hidden::make('status')
                ->default($user->role->name === 'Admin' ? 'active' : 'pending'),
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
                PhoneInput::make('phone')
                            ->countryStatePath('php')
                            ->required()
                            ->rule(rule:'numeric')
                            ->label('Phone number'),
                Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->placeholder('Enter the Email address')
                ->maxLength(255),
                Forms\Components\Select::make('role_id')
                ->relationship('Role', 'name')
                ->label('Role')
                ->native(false)
                ->required()
                ->preload(),
                Forms\Components\Select::make('branch_id')
                ->relationship('Branch', 'name')
                ->label('Branch')
                ->native(false)
                ->required()
                ->preload(),
                Forms\Components\TextInput::make('password')
                ->password()
                ->rule(Password::default()
                ->mixedCase()
                ->numbers(1) // Require at least one number
                ->uncompromised(3)
            )
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
                ->maxLength(255),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label('User name')
                ->searchable(),
            Tables\Columns\TextColumn::make('email')
                ->searchable()
                ->copyable()
                ->copyMessage('Email address copied')
                ->copyMessageDuration(1500),
            PhoneInputColumn::make('phone')
                ->countryColumn('phone_country')
                ->label('Phone number'),
            Tables\Columns\TextColumn::make('role.name')
                ->Badge(),
            Tables\Columns\TextColumn::make('status')
                ->Badge()
                ->getStateUsing(function (User $record): string {
                    if ($record->isActive()) {
                        return 'Active';
                    } elseif ($record->isRejected()) {
                        return 'Rejected';
                    } elseif ($record->isDeactive()) {
                        return 'Deactivated';
                    } else {
                        return 'Pending';
                    }
                })
                ->colors([
                    'success' => 'Active',
                    // 'danger' => 'Rejected' ,
                    // 'warning' => 'Pending',
                    'danger' => 'Deactivated',
                ]),
            ])
            ->filters([
            Tables\Filters\TernaryFilter::make('status')
                ->label('Account status')
                ->trueLabel('Active')
                ->falseLabel('Pending')
                ->native(false)
                ->placeholder('All')
                ->default(null)
                ->nullable(),
            ])
            ->actions([
                ActionGroup::make([
                Tables\Actions\Action::make('Active')
                ->action(function (User $record) {
                    $record->update(['status' => 'active']);

                })
                ->requiresConfirmation()
                ->hidden(fn (User $record): bool => $record->status === 'active' || $record->status === 'rejected')
                ->color('success')
                ->icon('heroicon-o-check-circle'),
            // Tables\Actions\Action::make('Reject')
            //     ->action(function (User $record) {
            //         $record->update(['status' => 'rejected']);
            //     })
            //     ->requiresConfirmation()
            //     ->hidden(fn (User $record): bool => $record->status === 'rejected' || $record->status === 'active')
            //     ->color('danger')
            //     ->icon('heroicon-o-archive-box-x-mark'),
                Tables\Actions\Action::make('deactivate')
                ->action(function (User $record) {
                    $record->update(['status' => 'deactivated']);
                })
                ->requiresConfirmation()
                ->hidden(fn (User $record): bool => $record->status === 'deactivated' || $record->status === 'pending')
                ->color('danger')
                ->icon('heroicon-o-archive-box-x-mark'),
                ViewAction::make()
                ->color('primary')
                ->form([
                    TextInput::make('name')->label('Name'),
                    TextInput::make('first')->label('First Name'),
                    TextInput::make('middle')->label('Middle Name'),
                    TextInput::make('last')->label('Last Name'),
                    TextInput::make('email')->label('Email'),
                ]),

                ])
                ->button()
                ->color('warning')
                ->label('Actions')
            ])
            ->bulkActions([
                //
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
    public static function getWidgets(): array
    {
        return [
            Widgets\UserStatsOverview::class,
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            // 'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

}
