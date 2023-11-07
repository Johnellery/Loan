<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Gcash;
use App\Models\Billing;
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
use Filament\Infolists;
use Filament\Infolists\Infolist;
class PaymentResource extends Resource
{
    protected static ?string $model = Gcash::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $activeNavigationIcon = 'heroicon-s-credit-card';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationGroup = 'My Loan';
    public static function getLabel(): string
    {
        return 'Payment Method';
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
        $user = Auth::user();
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
    public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Infolists\Components\Section::make('Payment method')
            ->description('')
            ->schema([
                Infolists\Components\Grid::make(1)->schema([
                    Infolists\Components\TextEntry::make('')
                    ->helperText('Please follow these instructions:'),
                    Infolists\Components\TextEntry::make('')
                    ->helperText('1.Take a screenshot of the transaction.'),
                    Infolists\Components\TextEntry::make('')
                    ->helperText(' 2.Ensure that your name is visible in the screenshot.'),
                    Infolists\Components\TextEntry::make('')
                    ->helperText(' 3.Send the screenshot to the invoice for verification by the cashier.'),
                    Infolists\Components\TextEntry::make('ewallet')
                    ->label('E-wallet Type'),
                    Infolists\Components\ImageEntry::make('image')
                    ->hiddenlabel()
                    ->width(500)
                    ->height(500),
                    Infolists\Components\TextEntry::make('name')
                    ->label('Receiver name'),
                    Infolists\Components\TextEntry::make('phone')
                    ->label('Phone Number'),

                ])
                ]),
    ]);
}
}
