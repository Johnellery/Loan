<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\Pages;
use App\Models\Branch;
use App\Models\Philprovince;
use App\Models\Philmuni;
use App\Models\Philbrgy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Squire\Models\Country;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $activeNavigationIcon = 'heroicon-s-map-pin';
    protected static ?int $navigationSort = 11;
    protected static ?string $navigationGroup = 'Set up';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                Forms\Components\TextInput::make('name')
                    ->minLength(2)
                    ->maxLength(255)
                    ->required()
                    ->placeholder('Enter the Branch name')
                    ->dehydrateStateUsing(fn (string $state): string => ucwords($state))
                    ->label('Branch name'),
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
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(5)
            ->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable()
                ->label('Branch name'),
            Tables\Columns\TextColumn::make('city')
                ->searchable()
                ->sortable()
                ->label('City'),
            Tables\Columns\TextColumn::make('province')
                ->searchable()
                ->sortable()
                ->label('Province'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                ->label('Archive Record')
                ->native(false)
                ->trueLabel(' With Archive Record')
                ->falseLabel('Archive Record Only')
                ->placeholder('All')
                ->default(null),
            ])
            ->actions([
            ActionGroup::make([
                Tables\Actions\EditAction::make()
                ->color('warning'),
                Tables\Actions\ViewAction::make()
                ->color('primary'),
                Tables\Actions\DeleteAction::make('Archive')
                ->label('Archive')
                ->successNotification(
                    Notification::make()
                         ->success()
                         ->title('Branch Archive')
                         ->body('The user has been Archived successfully.')),
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
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
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
