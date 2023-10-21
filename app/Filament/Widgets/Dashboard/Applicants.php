<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Applicant;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableColumn;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class Applicants extends BaseWidget
{
    // protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
        ->query(function () {
            // Create a new instance of the Eloquent query builder
            $query = Applicant::query();
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
                    ])
            ]);
    }
    public static function canView(): bool
    {
        $user = Auth::user();
        return $user->role->name === 'Admin'  || $user->role->name === 'Staff'; // Use comparison operator (===) here
    }
}
