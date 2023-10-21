<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Billing;
use App\Models\Invoice;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

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
        return $form
            ->schema([
                //
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
