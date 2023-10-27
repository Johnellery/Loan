<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RepossessionResource\Pages;
use App\Filament\Resources\RepossessionResource\RelationManagers;
use App\Models\Applicant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\ActionGroup;

class RepossessionResource extends Resource
{
    protected static ?string $model = Applicant::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-circle';
    protected static ?string $activeNavigationIcon = 'heroicon-s-exclamation-circle';
    protected static ?string $navigationGroup = 'Loan';
    protected static ?int $navigationSort = 5;
    public static function getLabel(): string
    {
        return 'Repossession'; // Replace this with your custom label
    }
    public static function shouldRegisterNavigation(): bool
    {
        $userole = Auth::user();
        $user = $userole->role->name;
        return $user && $user=== 'Admin'   || $user === 'Staff';
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
        ->query(fn (Applicant $query) => self::applyRoleConditions($query))
        ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\TextColumn::make('fullname')
                ->searchable()
                ->label('Customer name')
                ->getStateUsing(function (Applicant $record) {
                    return "{$record->last}, {$record->first} {$record->middle}";
                }),

                Tables\Columns\TextColumn::make('payment')
                ->description(function (Applicant $record) {
                    $or = $record->installment;
                    if ($or == 4) {
                        return "Weekly";
                    } elseif ($or == 1) {
                        return "Monthly";
                    } else {
                        return "Error";
                    }
                })
                ->label('Installment')
                ->numeric(
                    decimalPlaces: 2,
                    decimalSeparator: '.',
                    thousandsSeparator: ',',
                ),
                Tables\Columns\TextColumn::make('remaining_balance')
                ->label('Remaining balance')
                ->numeric(
                    decimalPlaces: 2,
                    decimalSeparator: '.',
                    thousandsSeparator: ',',
                )
                ->getStateUsing(function (Applicant $record) {
                    return $record->updateRemainingBalance();
                }),
                Tables\Columns\TextColumn::make('is_status')
                ->badge()
                ->getStateUsing(function (Applicant $record): string {
                    if ($record->isRepossessing()) {
                        return 'Repossessing';
                    } elseif ($record->isFailed()) {
                        return 'Repossessing Failed';
                    } elseif ($record->isSuccess()) {
                        return 'Repossessing Success';
                    } else {
                        return 'Active Loan';
                    }
                })
                ->colors([
                    'success' => 'Active Loan',
                    'warning' => 'Repossessing' ,
                    'danger' => 'Repossessing Failed',
                    'primary' => 'Repossessing Success',
                ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                ActionGroup::make([
                    Tables\Actions\Action::make('Repossession Success')
                    ->label('Repossession Success')
                    ->action(function (Applicant $record) {
                        $record->update(['is_status' => 'success']);
                    })
                    ->requiresConfirmation()
                    ->hidden(fn (Applicant $record): bool => $record->is_status === 'active' || $record->is_status === 'failed' || $record->is_status === 'success')
                    ->color('success')
                    ->icon('heroicon-o-check-circle'),
                Tables\Actions\Action::make('Repossession Failed')
                ->label('Repossession Failed')
                    ->action(function (Applicant $record) {
                        $record->update(['is_status' => 'failed']);
                    })
                    ->requiresConfirmation()
                    ->hidden(fn (Applicant $record): bool => $record->is_status === 'success' || $record->is_status === 'active' || $record->is_status === 'failed')
                    ->color('warning')
                    ->icon('heroicon-o-exclamation-circle'),
                    Tables\Actions\Action::make('Repossession notice')
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->requiresConfirmation()
                    ->hidden(fn (Applicant $record): bool => $record->is_status === 'failed' || $record->is_status === 'success')
                    ->url(fn (Applicant $record) => route('repossession1.index', $record))
                    ->successNotificationTitle('Repossession notice send successfully')
                    ->color('danger'),
                    Tables\Actions\Action::make('Send Notice')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->requiresConfirmation()
                    ->hidden(fn (Applicant $record): bool => $record->is_status === 'failed' || $record->is_status === 'success' || $record->is_status === 'repossessing')
                    ->url(fn (Applicant $record) => route('repossession.index', $record))
                    ->successNotification(
                        Notification::make()
                             ->success()
                             ->title('Payment reminder')
                             ->body('The notice successfully send to the user.'),
                     )
                    ->color('warning'),
                    Tables\Actions\Action::make('Loan Summary')
                    ->icon('heroicon-o-folder-minus')
                    ->color('success')
                    ->url(fn (Applicant $record) => route('generate-pdf', $record))
                    ->openUrlInNewTab(),
                    Tables\Actions\viewAction::make()
                    ->color('primary'),
                    ])
                    ->button()
                    ->color('warning')
                    ->label('Actions')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListRepossessions::route('/'),
            // 'create' => Pages\CreateRepossession::route('/create'),
            // 'edit' => Pages\EditRepossession::route('/{record}/edit'),
        ];
    }
    private static function applyRoleConditions($query): Builder
{
    $user = Auth::user();

    if ($user->role->name === 'Admin') {
        return $query->where('status', 'approved')
                     ->where('ci_status', 'approved')
                     ->where('remaining_balance', '>', 0)
                     ->where('repossession', 'LIKE', '%"status":"Missed"%');
    } elseif ($user->role->name === 'Staff' || $user->role->name === 'Collector') {
        return $query->where('branch_id', $user->branch_id)
                     ->where('ci_status', 'approved')
                     ->where('remaining_balance', '>', 0)
                     ->where(function ($query) {
                         $query->where('status', 'approved');
                     })
                     ->where(function ($query) {
                         // Retrieve and decode the 'repossession' JSON data from the database
                         $query->where('repossession', 'LIKE', '%"status":"Missed"%');
                     });
    }

    return $query;
}
public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Infolists\Components\Fieldset::make('Personal Information')
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
                    })
                    ,
                    Infolists\Components\TextEntry::make('spouse_contact')
                    ->label('Contact number')
                    ->getStateUsing(function (Applicant $record) {
                        return $record->spouse_contact ?? 'none';
                    })
                    ,
                ])
            ])
                ]),
                Infolists\Components\Fieldset::make('Loan Information')
                ->schema([
                Infolists\Components\Section::make()->schema([
                    Infolists\Components\Grid::make(3)->schema([
                        Infolists\Components\TextEntry::make('customer_name')
                        ->label('Customer name'),
                        Infolists\Components\TextEntry::make('bike_price')
                        ->label('Total Contract')
                        ->numeric(
                            decimalPlaces: 2,
                            decimalSeparator: '.',
                            thousandsSeparator: ',',
                        ),
                        Infolists\Components\TextEntry::make('total_interest')
                        ->label('Total interest')
                        ->numeric(
                            decimalPlaces: 2,
                            decimalSeparator: '.',
                            thousandsSeparator: ',',
                        ),
                        Infolists\Components\TextEntry::make('plus')
                        ->label('Total amount')
                        ->numeric(
                            decimalPlaces: 2,
                            decimalSeparator: '.',
                            thousandsSeparator: ',',
                        ),
                        Infolists\Components\TextEntry::make('payment')
                        ->label('Installment')
                        ->numeric(
                            decimalPlaces: 2,
                            decimalSeparator: '.',
                            thousandsSeparator: ',',
                        ),
                        Infolists\Components\TextEntry::make('remaining_balance')
                        ->label('Remaining balance')
                        ->numeric(
                            decimalPlaces: 2,
                            decimalSeparator: '.',
                            thousandsSeparator: ',',
                        ),
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
