<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RepossessionResource\Pages;
use App\Filament\Resources\RepossessionResource\RelationManagers;
use App\Models\Applicant;
use App\Tables\Columns\ProgressColumn;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\ActionGroup;
use Filament\Infolists\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
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
        ->defaultSort('payment_schedule', 'desc')
        ->deferLoading()
        ->paginatedWhileReordering()
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
                Tables\Columns\TextColumn::make('payment_schedule')
                ->label('Payment Schedule')
                ->getStateUsing(function (Applicant $record) {
                        return $record->updateSched();
                     }),
                // ->description(function (Applicant $record) {
                //     return $record->updateDescription();
                // }),
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
                ProgressColumn::make('progress')
                ->getStateUsing(function (Applicant $record) {
                    $plus = $record->plus;
                    $down = $record->down_payment;
                    $remaining = $record->remaining_balance;
                    $total = $plus - $down;
                    $total1 = $total - $remaining;
                    $percentage = ($total1 / $total) * 100;
                    return $percentage;
                })
            ])
            ->filters([
                Filter::make('created_at')
                ->form([
                    DatePicker::make('created_from'),
                    DatePicker::make('created_until'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('payment_schedule', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('payment_schedule', '<=', $date),
                        );
                }),
                SelectFilter::make('is_paid')
                ->options([
                    'Paid' => 'Paid',
                    'Pending' => 'Pending',
                    'Missed' => 'Missed',
                ])
                ->native(false)
                ->label('Payment status'),
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
                        return $record->spouse_contact ?? 'none';
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
                ])->collapsed(),
                Infolists\Components\Section::make('Payment History')
                ->description('History of payment schedules')
                ->schema([
                    Infolists\Components\Grid::make(3)->schema([
                        Infolists\Components\TextEntry::make('payment_info')
                        ->label('Payment Info')
                        ->getStateUsing(function (Applicant $record) {
                            return $record->updatepaid();
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

    ]);
}


}
