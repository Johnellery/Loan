<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BikeResource\Pages;
use App\Models\Bike;
use App\Models\Rate;
use Closure;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Awcodes\FilamentBadgeableColumn\Components\Badge;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableColumn;
class BikeResource extends Resource
{
    protected static ?string $model = Bike::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $activeNavigationIcon = 'heroicon-s-shopping-cart';
    protected static ?int $navigationSort = 9;
    protected static ?string $navigationGroup = 'Set up';
    protected static ?string $recordTitleAttribute = 'name';

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
            Forms\Components\Hidden::make('status')
            ->default($user->role->name === 'Admin' ? 'approved' : 'pending'),
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
                ->imageEditorMode(2)
                ->columnSpan(span:2),
            Forms\Components\TextInput::make('name')
                ->label('Bike name')
                ->minLength(2)
                ->placeholder('Enter the Bike name')
                ->maxLength(255)
                ->dehydrateStateUsing(fn (string $state): string => ucwords($state))
                ->required(),
                Forms\Components\TextInput::make('brand')
                ->label('Brand')
                ->minLength(2)
                ->placeholder('Enter the Bike Brand')
                ->maxLength(255)
                ->dehydrateStateUsing(fn (string $state): string => ucwords($state))
                ->required(),
                Forms\Components\TextInput::make('price')
                ->required()
                ->reactive()
                ->placeholder('Enter the Bike Price')
                ->prefix('Php')
                ->rule('numeric')
                ->afterStateUpdated(function ($set, $get) {
                    $activeRate = Rate::where('status', 'active')->first();
                    $price = (float)$activeRate->low;
                    $price1 = (float)$activeRate->high;
                    $rate = (float)$activeRate->rate;
                    $rate1 = (float)$activeRate->rate1;
                    $inputPrice = (float)$get('price'); // Get the value of the "price" input field

                    if ($activeRate) {
                        if ($inputPrice <= $price) {
                            $set('rate', $rate);
                            $set('down', '1000');
                        } elseif ($inputPrice >= $price) {
                            $set('rate', '2000');
                        }
                    } else {
                        // Handle the case where no active rate is found
                    }
                }),
            Forms\Components\TextInput::make('rate')
                ->required()
                ->readonly()
                ->placeholder('Enter your Insterest rate')
                ->label('Interest rate')
                ->suffix('%')
                ->reactive(),
            Forms\Components\Select::make('category_id')
                ->relationship('Category', 'name')
                ->label('Category')
                ->native(false)
                ->required()
                ->preload(),
            Forms\Components\TextInput::make('down')
                ->required()
                ->reactive()
                ->placeholder('Enter your down payment')
                ->rule(rule:'numeric')
                ->label('Down payments'),
            Forms\Components\TextInput::make('description')
                ->minLength(2)
                ->columnSpan(span:2)
                ->placeholder('Enter the Description')
                ->maxLength(255)
                ->required(),
                ])->columns(2)
                ]);

    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                ->circular(),
                BadgeableColumn::make('name')
                ->searchable()
                ->copyable()
                ->copyMessage('Email address copied')
                ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('category.name'),
                Tables\Columns\TextColumn::make('price')
                ->sortable()
                ->numeric(
                    decimalPlaces: 2,
                    decimalSeparator: '.',
                    thousandsSeparator: ',',
                ),
                Tables\Columns\TextColumn::make('is_available')
                ->label('Availability')
                ->Badge()
                ->getStateUsing(function (Bike $record): string {
                    if ($record->isAvailable()) {
                        return 'Available';
                    } else {
                        return 'Unavailable';
                    }
                })
                ->colors([
                    'success' => 'Available',
                    'danger' => 'Unavailable',
                    'rejected' => 'Rejected'
                ]),
                Tables\Columns\TextColumn::make('status')
                ->Badge()
                ->getStateUsing(function (Bike $record): string {
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
                ])
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
                ->native(false)
                ->falseLabel('Pending')
                ->placeholder('All')
                ->default(null)
                ->nullable(),
            ])
            ->actions([
                ActionGroup::make([
                Tables\Actions\Action::make('Approve')
                ->action(function (Bike $record) {
                    $record->update(['status' => 'approved']);
                })
                ->requiresConfirmation()
                ->hidden(fn (Bike $record): bool => $record->status === 'approved' || $record->status === 'rejected')
                ->color('success')
                ->visible(function () {
                    $user = Auth::user();
                    return $user->role->name === 'Admin';
                })
                ->icon('heroicon-o-check-circle'),
                Tables\Actions\Action::make('Available')
                ->action(function (Bike $record) {
                    $record->update(['is_available' => 'available']);
                })
                ->requiresConfirmation()
                ->hidden(fn (Bike $record): bool => $record->is_available === 'available')
                ->color('success')
                ->icon('heroicon-o-document-check'),
                Tables\Actions\Action::make('Reject')
                ->action(function (Bike $record) {
                    $record->update(['status' => 'rejected']);
                })
                ->requiresConfirmation()
                ->hidden(fn (Bike $record): bool => $record->status === 'rejected' || $record->status === 'approved')
                ->color('danger')
                ->visible(function () {
                    $user = Auth::user();
                    return $user->role->name === 'Admin' ;
                })
                ->icon('heroicon-o-archive-box-arrow-down'),
                Tables\Actions\Action::make('Unavailable')
                ->action(function (Bike $record) {
                    $record->update(['is_available' => 'unavailable']);
                })
                ->requiresConfirmation()
                ->hidden(fn (Bike $record): bool => $record->is_available === 'unavailable' )
                ->color('danger')
                ->icon('heroicon-o-no-symbol'),
                Tables\Actions\DeleteAction::make()
                ->label('Archive')
                ->successNotification(
                    Notification::make()
                         ->success()
                         ->title('Category Archive')
                         ->body('The user has been Archived successfully.')),
                         Tables\Actions\EditAction::make()
                         ->color('warning'),
                         Tables\Actions\ViewAction::make()
                         ->color('primary'),

                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->button()
            ->color('warning')
            ->label('Actions')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListBikes::route('/'),
            'create' => Pages\CreateBike::route('/create'),
            'edit' => Pages\EditBike::route('/{record}/edit'),
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
    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        if ($user->role->name ==='Admin') {

            return static::getModel()::where('status', '=', 'pending')->count();
        }


        return null;
    }

}
