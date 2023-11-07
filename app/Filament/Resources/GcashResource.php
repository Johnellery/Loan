<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GcashResource\Pages;
use App\Filament\Resources\GcashResource\RelationManagers;
use App\Models\Gcash;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Filament\Tables\Actions\ActionGroup;
class GcashResource extends Resource
{
    protected static ?string $model = Gcash::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $activeNavigationIcon = 'heroicon-s-credit-card';
    protected static ?int $navigationSort = 20;
    protected static ?string $navigationGroup = 'Set up';
    public static function getLabel(): string
    {
        return 'Payment Method';
    }
    public static function shouldRegisterNavigation(): bool
    {
        $userole = Auth::user();
        $user = $userole->role->name;
        return $user && $user === 'Admin'  || $user === 'Staff';
    }
    public static function form(Form $form): Form
    {
        $user = Auth::user();
        return $form
            ->schema([
                Forms\Components\Section::make([
                Forms\Components\Hidden::make('user_id')
                ->default($user->id),
                Forms\Components\Hidden::make('branch_id')
                ->default($user->branch_id),
                Forms\Components\TextInput::make('ewallet')
                ->label('E-wallet')
                ->minLength(2)
                ->placeholder('Enter the E-wallet')
                ->maxLength(255)
                ->dehydrateStateUsing(fn (string $state): string => ucwords($state))
                ->required(),
                Forms\Components\FileUpload::make('image')
                ->image()
                ->preserveFilenames()
                ->imageEditor()
                ->required()
                ->imageEditorAspectRatios([
                    '16:9',
                    '4:3',
                    '1:1',
                ])
                ->imageEditorEmptyFillColor('#000000')
                ->imageEditorMode(2),
                Forms\Components\TextInput::make('name')
                ->label('Receiver Name')
                ->minLength(2)
                ->placeholder('Enter the Receiver name')
                ->maxLength(255)
                ->dehydrateStateUsing(fn (string $state): string => ucwords($state))
                ->required(),
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
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                ->circular(),
                BadgeableColumn::make('ewallet')
                ->searchable()
                ->copyable()
                ->label('E-wallet')
                ->copyMessage('E-wallet copied')
                ->copyMessageDuration(1500),
                BadgeableColumn::make('name')
                ->searchable()
                ->copyable()
                ->label('Receiver name')
                ->copyMessage('Receiver name copied')
                ->copyMessageDuration(1500),
                BadgeableColumn::make('phone')
                ->searchable()
                ->copyable()
                ->label('Phone')
                ->copyMessage('Phone Number copied')
                ->copyMessageDuration(1500),

            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                    Tables\Actions\EditAction::make()
                    ->color('primary'),
                    Tables\Actions\ViewAction::make()
                    ->color('warning'),
                    Tables\Actions\DeleteAction::make()
                    ->label('Archive')
                ])
                ->button()
                ->color('warning')
                ->label('Actions')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
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
            'index' => Pages\ListGcashes::route('/'),
            'create' => Pages\CreateGcash::route('/create'),
            'edit' => Pages\EditGcash::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $query = parent::getEloquentQuery();

        if ($user->role->name === 'Admin') {
            return $query->withoutGlobalScopes([SoftDeletingScope::class]);
        } elseif ($user->role->name === 'Staff') {
            return $query->where('branch_id', $user->branch_id)
                ->withoutGlobalScopes([SoftDeletingScope::class]);
        }
        return $query;
    }
}
