<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Applicant;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableColumn;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class Schedule extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
        ->query(function () {
            $query = Applicant::query();
            $user = Auth::user();
            $currentDate = Carbon::now();
            if ($user->role->name === 'Admin') {
                $query->where('ci_status', 'pending');
            } elseif ($user->role->name === 'Staff' || $user->role->name === 'Collector') {
                $query->where('branch_id', $user->branch_id)
                      ->where('ci_status', 'pending')
                      ->where('ci_sched', '>', $currentDate);
            }
            return $query;
        })

        ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\TextColumn::make('fullname')
                ->searchable()
                ->label('Applicant name')
                ->getStateUsing(function (Applicant $record) {
                    return "{$record->last}, {$record->first} {$record->middle}";
                })
                // ->getStateUsing(function (Applicant $record) {
                //     return $record->updateCompute();
                // })
                // ->description(function (Applicant $record) {
                //     return $record->updateremaining();
                // })
                ->searchable(),
                Tables\Columns\TextColumn::make('bike.name')
                ->searchable(),
                Tables\Columns\TextColumn::make('bike.price')
                ->sortable()
                ->numeric(
                    decimalPlaces: 2,
                    decimalSeparator: '.',
                    thousandsSeparator: ',',
                )
                ->label('Total contract price'),
                Tables\Columns\TextColumn::make('ci_sched')
                ->label('CI Schedule')
            ]);
    }
    public static function canView(): bool
    {
        $user = Auth::user();
        return $user->role->name === 'Collector' ; // Use comparison operator (===) here
    }
}
