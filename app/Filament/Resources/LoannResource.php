<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoannResource\Pages;
use App\Filament\Resources\LoannResource\RelationManagers;
use App\Models\Applicant;
use App\Models\Loann;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists;
use Filament\Infolists\Infolist;
class LoannResource extends Resource
{
    protected static ?string $model = Applicant::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $activeNavigationIcon = 'heroicon-s-users';
    protected static ?string $navigationGroup = 'My Loan';
    protected static ?int $navigationSort = 1;
    public static function getLabel(): string
    {
        return 'Loan';
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
                Tables\Columns\TextColumn::make('fullname')
                ->searchable()
                ->label('Customer name')
                ->getStateUsing(function (Applicant $record) {
                    return "{$record->last}, {$record->first} {$record->middle}";
                }),
                Tables\Columns\TextColumn::make('bike.name'),
                Tables\Columns\TextColumn::make('status')
                ->Badge()
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
                //
            ])
            ->actions([
                ActionGroup::make([
                        // Tables\Actions\EditAction::make()
                        // ->color('warning'),
                    Tables\Actions\ViewAction::make()
                    // ->slideOver()
                    ->color('primary'),
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
            'index' => Pages\ListLoanns::route('/'),
            'create' => Pages\CreateLoann::route('/create'),
            // 'edit' => Pages\EditLoann::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $query = parent::getEloquentQuery();

        if ($user->role->name === 'Customer') {
            return $query
                ->where('user_id', $user->id)
                ->withoutGlobalScopes([SoftDeletingScope::class]);
        }

        // Make sure to return a default query if the above condition is not met.
        return $query;
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
