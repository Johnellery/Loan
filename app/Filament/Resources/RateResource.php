<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RateResource\Pages;
use App\Filament\Resources\RateResource\RelationManagers;
use App\Models\Rate;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\RateResource\Widgets\RateOverview;
class RateResource extends Resource
{
    protected static ?string $model = Rate::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';
    protected static ?string $activeNavigationIcon = 'heroicon-s-arrow-trending-up';
    protected static ?int $navigationSort = 9;
    protected static ?string $navigationGroup = 'Set up';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                Forms\Components\TextInput::make('low')
                ->required()
                ->label('Low price')
                ->placeholder('Enter the Bike Price')
                ->prefix('Php')
                ->rule(rule:'numeric'),
            Forms\Components\TextInput::make('rate')
                ->required()
                ->label('Interest rate')
                ->suffix('%')
                ->placeholder('Enter the Bike Interest Rate')
                ->rule(rule:'numeric'),
                Forms\Components\TextInput::make('high')
                ->required()
                ->label('High price')
                ->placeholder('Enter the Bike Price')
                ->prefix('Php')
                ->rule(rule:'numeric'),
            Forms\Components\TextInput::make('rate1')
                ->required()
                ->label('Interest rate')
                ->suffix('%')
                ->placeholder('Enter the Bike Interest Rate')
                ->rule(rule:'numeric'),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\TextColumn::make('low')
                ->sortable()
                ->searchable()
                ->numeric(
                    decimalPlaces: 2,
                    decimalSeparator: '.',
                    thousandsSeparator: ',',
                ),
                Tables\Columns\TextColumn::make('rate')
                ->sortable()
                ->searchable()
                ->getStateUsing(function (Rate $record) {
                    return "{$record->rate}% ";
                }),
                Tables\Columns\TextColumn::make('high')
                ->sortable()
                ->searchable()
                ->numeric(
                    decimalPlaces: 2,
                    decimalSeparator: '.',
                    thousandsSeparator: ',',
                ),
                Tables\Columns\TextColumn::make('rate1')
                ->sortable()
                ->label('Rate')
                ->searchable()
                ->getStateUsing(function (Rate $record) {
                    return "{$record->rate1}% ";
                }),
                Tables\Columns\TextColumn::make('status')
                ->Badge()
                ->getStateUsing(function (Rate $record): string {
                    if ($record->isApproved()) {
                        return 'Active';
                    } elseif ($record->isRejected()) {
                        return 'Deactivated';
                    } else {
                        return 'Pending';
                    }
                })
                ->colors([
                    'success' => 'Active',
                    'danger' => 'Deactivated',
                    'warning' => 'Pending'
                ])
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\Action::make('Active')
                    ->action(function (Rate $record) {
                        $record->update(['status' => 'active']);
                    })
                    ->requiresConfirmation()
                    ->hidden(fn (Rate $record): bool => $record->status === 'active')
                    ->color('success')
                    ->visible(function () {
                        $user = Auth::user();
                        return $user->role->name === 'Admin';
                    })
                    ->icon('heroicon-o-check-circle'),
                    Tables\Actions\Action::make('Deactivated')
                    ->action(function (Rate $record) {
                        $record->update(['status' => 'deactivated']);
                    })
                    ->requiresConfirmation()
                    ->hidden(fn (Rate $record): bool => $record->status === 'deactivated')
                    ->color('danger')
                    ->visible(function () {
                        $user = Auth::user();
                        return $user->role->name === 'Admin' ;
                    })
                    ->icon('heroicon-o-archive-box-arrow-down'),
                Tables\Actions\EditAction::make()
                ->color('warning'),
                ])            ->button()
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
            'index' => Pages\ListRates::route('/'),
            'create' => Pages\CreateRate::route('/create'),
            'edit' => Pages\EditRate::route('/{record}/edit'),
        ];
    }

}
