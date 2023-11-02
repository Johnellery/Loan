<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoannResource\Pages;
use App\Filament\Resources\LoannResource\RelationManagers;
use App\Models\Applicant;
use App\Models\Loann;
use App\Tables\Columns\ProgressColumn;
use Filament\Forms;
use Filament\Infolists\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
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
                ProgressColumn::make('progress')
                ->getStateUsing(function (Applicant $record) {
                    $created = 20;
                    $status = $record->status;
                    $CI = $record->ci_status;

                    if ($status == 'approved') {
                        if ($CI == 'approved') {
                            $approved = $created + 80;
                        } elseif ($CI == 'pending') {
                            $approved = $created + 40;
                        } elseif ($CI == 'rejected') {
                            $approved = $created + 80;
                        } else {
                            $approved = $created + 40; // Default if CI status is not specified
                        }
                    } elseif ($status == 'rejected') {
                        $approved = $created + 80;
                    } elseif ($status == 'pending') {
                        $approved = $created;
                    } else {
                        $approved = 0; // Default if status is not specified
                    }

                    return $approved;
                })
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
            Infolists\Components\Section::make('Loan Applicantion Progress')
                ->description('Track the status and progress of your loan application.')
                ->schema([
                    Infolists\Components\Grid::make(1)->schema([
                        Infolists\Components\Grid::make(3)->schema([
                            Section::make('')
                            ->schema([
                                Infolists\Components\Grid::make(3)
                                ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                        ->label('')
                        ->getStateUsing(function (Applicant $record) {
                            $date = new \DateTime($record->created_at);
                            return $date->format('F j, Y');
                        }),
                        IconEntry::make('status')
                        ->label('Status')
                        ->icon(fn (string $state): string => match ($state) {
                            'pending' => 'heroicon-s-exclamation-circle',
                            'approved' => 'heroicon-s-exclamation-circle',
                            'rejected' => 'heroicon-s-exclamation-circle',
                        })
                        ->color(fn (string $state): string => match ($state) {
                            'pending' => 'primary',
                            'approved' => 'primary',
                            'rejected' => 'primary',
                            default => 'primary',
                        }),
                        TextEntry::make('Loan Applicantion')
                        ->label('Loan Applicantion')
                        ->weight(FontWeight::Light)
                        ->getStateUsing(function (Applicant $record) {
                            return "Date of your Loan Applicant created";
                        }),
                        ])
                    ]),
                        Section::make('')
                        ->schema([
                            Infolists\Components\Grid::make(3)
                            ->schema([
                        Infolists\Components\TextEntry::make('status_date')
                        ->label('')
                        ->getStateUsing(function (Applicant $record) {
                            $date =  $record->status_date;

                            if ($date) {
                                $date1 =  new \DateTime($date);
                                return $date1->format('F j, Y');
                            } else {
                                return 'Currently being review';
                            }
                        }),
                        IconEntry::make('status')
                        ->label('')
                        ->icon(fn (string $state): string => match ($state) {
                            'pending' => 'heroicon-s-clock',
                            'approved' => 'heroicon-s-check-circle',
                            'rejected' => 'heroicon-s-x-circle',
                        })
                        ->color(fn (string $state): string => match ($state) {

                            'pending' => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default => 'primary',
                        }),
                        TextEntry::make('Loan Applicantion')
                        ->color('gray')
                        ->weight(FontWeight::Light)
                        ->label('Loan Application Review in Progress')
                        ->getStateUsing(function (Applicant $record) {
                            $status = $record->status;
                            if ($status == 'approved') {
                                return "Your loan application has been approved and will now proceed to the Credit Investigation (CI) stage.";
                            } elseif ($status == 'rejected') {
                                return "Your loan application has been rejected.";
                            } else {
                                return "Your loan application is currently under review, and we will keep you updated on its status.";
                            }
                        }),
                        ])
                    ]),///
                        Section::make('')
                        ->schema([
                            Infolists\Components\Grid::make(3)
                            ->schema([
                        Infolists\Components\TextEntry::make('status_date')
                        ->label('')
                        ->getStateUsing(function (Applicant $record) {
                            $date =  $record->ci_sched;
                            $status = $record->status;
                            if ($date) {
                                $date1 =  new \DateTime($date);
                                return $date1->format('F j, Y');
                            }  else if ($status == 'rejected'){
                                return '--------------------';
                            } else {
                                return 'Currently being review';
                            }
                        }),
                        IconEntry::make('status')
                        ->label('')
                        ->icon(fn (string $state): string => match ($state) {
                            'pending' => 'heroicon-s-clock',
                            'approved' => 'heroicon-s-exclamation-circle',
                            'rejected' => 'heroicon-s-x-circle',
                        })
                        ->color(fn (string $state): string => match ($state) {
                            'pending' => 'warning',
                            'approved' => 'primary',
                            'rejected' => 'danger',
                            default => 'primary',
                        }),
                        TextEntry::make('Loan Applicantion')
                        ->color('gray')
                        ->weight(FontWeight::Light)
                        ->label('Date of your Credit Investigation')
                        ->getStateUsing(function (Applicant $record) {
                            $status = $record->status;
                            if ($status == 'approved') {
                                return "Please be prepared for your upcoming Credit Investigation date.";
                            } elseif ($status == 'rejected') {
                                return "Your loan application has been rejected.";
                            } else {
                                return "Your loan application is currently under review. We'll schedule the Credit Investigation (CI) date shortly and keep you informed";
                            }
                        }),
                        ])
                    ]),///
                                Section::make('')
                                ->schema([
                                    Infolists\Components\Grid::make(3)
                                    ->schema([
                                Infolists\Components\TextEntry::make('ci_date')
                                ->label('')
                                ->getStateUsing(function (Applicant $record) {
                                    $date =  $record->ci_date;
                                    $status = $record->status;
                                    if ($date) {
                                        $date1 =  new \DateTime($date);
                                        return $date1->format('F j, Y');
                                    }  else if ($status == 'rejected'){
                                        return '--------------------';
                                    } else {
                                        return '--------------------';
                                    }
                                }),
                                IconEntry::make('status')
                                ->label('')
                                ->icon(fn (string $state): string => match ($state) {
                                    'pending' => 'heroicon-s-clock',
                                    'approved' => 'heroicon-s-check-circle',
                                    'rejected' => 'heroicon-s-x-circle',
                                })
                                ->color(fn (string $state): string => match ($state) {
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    default => 'primary',
                                }),
                                TextEntry::make('Loan Applicantion')
                                ->color('gray')
                                ->weight(FontWeight::Light)
                                ->label('Credit Investigation Status')
                                ->getStateUsing(function (Applicant $record) {
                                    $status = $record->status;
                                    if ($status == 'approved') {
                                        return "We are pleased to inform you that your loan application has been approved following a successful Credit Investigation.";
                                    } elseif ($status == 'rejected') {
                                        return "Regrettably, your loan application has been declined during the Credit Investigation process.";
                                    } else {
                                        return "Your loan application is currently undergoing a Credit Investigation review, and we will provide regular updates on its status.";
                                    }
                                }),
                                ])
                            ]),///
                            Section::make('')
                            ->schema([
                                Infolists\Components\Grid::make(3)
                                ->schema([
                            Infolists\Components\TextEntry::make('ci_date')
                            ->label('')
                            ->getStateUsing(function (Applicant $record) {
                                $date =  $record->ci_date;
                                $status = $record->status;
                                if ($date) {
                                    $date1 =  new \DateTime($date);
                                    return $date1->format('F j, Y');
                                }  else if ($status == 'rejected'){
                                    return '--------------------';
                                } else {
                                    return '--------------------';
                                }
                            }),
                            IconEntry::make('status')
                            ->label('')
                            ->icon(fn (string $state): string => match ($state) {
                                'pending' => 'heroicon-s-clock',
                                'approved' => 'heroicon-s-exclamation-circle',
                                'rejected' => 'heroicon-s-x-circle',
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'primary',
                                'rejected' => 'danger',
                                default => 'primary',
                            }),
                            TextEntry::make('Loan Applicantion')
                            ->color('gray')
                            ->weight(FontWeight::Light)
                            ->label('Loan Details')
                            ->getStateUsing(function (Applicant $record) {
                                $status = $record->status;
                                $installment = $record->installment;
                                if ($status == 'approved' && $installment == '1') {
                                    return "Your loan payments are scheduled to commence next month." ;
                                } else if ($status == 'approved' && $installment == '4') {
                                    return "Your loan payments are scheduled to commence next week." ;
                                }elseif ($status == 'rejected') {
                                    return "Regrettably, your loan application has been declined during the Credit Investigation process.";
                                } else {
                                    return "Your loan application is currently undergoing a Credit Investigation review, and we will provide regular updates on its status.";
                                }
                            }),
                            ])
                        ]),///
                        ])
                        ])
                    ])->collapsed(),
                Section::make('Customer')
                ->description('Customer Loan Information')
                ->schema([
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
                            return empty($record->spouse) ? 'None' : $record->spouse;
                        }),
                        Infolists\Components\TextEntry::make('spouse_contact')
                        ->label('Contact number')
                        ->getStateUsing(function (Applicant $record) {
                            return $record->spouse_contact ?? 'None';
                        }),
                    ])
                ])->collapsed(),
            Infolists\Components\Section::make('Address')
            ->description('Current Address')
            ->schema([
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
                ])->collapsed(),
                Infolists\Components\Section::make('Installment')
                ->description('Loan details')
                ->schema([
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
                    ])->collapsed(),
                    Infolists\Components\Section::make('Barangay Clearance')
                    ->description('Requirements Details')
                    ->schema([
                        Infolists\Components\Grid::make(1)->schema([
                            Infolists\Components\ImageEntry::make('barangay_clearance')
                            ->hiddenlabel()
                            ->width(750)
                            ->height(750),
                            ])
                        ])->collapsed(),
                        Infolists\Components\Section::make('Valid ID')
                        ->description('Valid ID Requirements')
                        ->schema([
                            Infolists\Components\Grid::make(1)->schema([
                                Infolists\Components\TextEntry::make('valid_id_list')
                                ->label('ID type'),
                                Infolists\Components\ImageEntry::make('valid_id')
                                ->hiddenlabel()
                                ->width(750)
                                ->size(750)
                                ->height(300),
                                ])
                            ])->collapsed(),
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
