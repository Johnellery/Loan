<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Bike;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableColumn;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class Bikes extends BaseWidget
{
    // protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
        ->query(function () {
            // Create a new instance of the Eloquent query builder
            $query = Bike::query();
            $user = Auth::user();

            if ($user->role->name === 'Admin') {
                $query->where('status', 'pending');
            } elseif ($user->role->name === 'Staff' || $user->role->name === 'Collector') {
                $query->where('branch_id', $user->branch_id)
                      ->where('status', 'pending');
            }
            return $query;
        })
        ->defaultPaginationPageOption(5)
            ->columns([
                BadgeableColumn::make('name')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email address copied')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('status')
                    ->Badge()
                    ->getStateUsing(function (Bike $record): string {
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
                    ])
            ]);
    }
    public static function canView(): bool
    {
        $user = Auth::user();
        return $user->role->name === 'Admin'; // Use comparison operator (===) here
    }
}
