<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Billing;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableColumn;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class Invoices extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
        ->query(function () {
            // Create a new instance of the Eloquent query builder
            $query = Billing::query();
            $user = Auth::user();

            if ($user->role->name === 'Admin') {
                $query->where('billing_status', 'processing');
            } elseif ($user->role->name === 'Staff' || $user->role->name === 'Collector') {
                $query->where('branch_id', $user->branch_id)
                      ->where('billing_status', 'processing');
            }
            return $query;
        })
        ->defaultPaginationPageOption(5)
            ->columns([
            Tables\Columns\TextColumn::make('Customer')
                // ->searchable()
                ->label('Applicant name')
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
                Tables\Columns\TextColumn::make('status')
                ->Badge()
                ->getStateUsing(function (Billing $record): string {
                    if ($record->isRemitted()) {
                        return 'Remitted';
                    } elseif ($record->isNot_recieved()) {
                        return 'Not received';
                    } else {
                        return 'Processing';
                    }
                })
                ->colors([
                    'success' => 'Remitted',
                    'danger' => 'Not received',
                    'warning' => 'Processing'
                ]),
            ]);
    }
    public static function canView(): bool
    {
        $user = Auth::user();
        return $user->role->name === 'Admin'; // Use comparison operator (===) here
    }
}
