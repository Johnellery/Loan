<?php

namespace App\Filament\Resources\BikeResource\Widgets;

use App\Models\Bike;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
class BikeOverview extends BaseWidget
{
    public ?Model $record = null;
    protected function getStats(): array
    {
        $user = Auth::user();
        $approvedBikeCount = Bike::where('status', 'approved')
        ->where('branch_id', $user->branch_id)
                                ->count();
        $availableBikeCount = Bike::where('is_available', 'available')
        ->where('branch_id', $user->branch_id)
                                ->where('status', 'approved')
                                ->count();
        $outOfStockBikeCount = Bike::where('is_available', 'unavailable')
        ->where('branch_id', $user->branch_id)
                                ->where('status', 'approved')
                                ->count();
        return [
        Stat::make('Total Approved Bikes', $approvedBikeCount)
            ->description('Number of bikes with an approved status')
            ->color('success'),

        Stat::make('Total Available Bikes', $availableBikeCount)
            ->description('Number of bikes currently available for loan')
            ->color('success'),

        Stat::make('Total Out of Stock Bikes', $outOfStockBikeCount)
            ->description('Number of bikes that are currently out of stock')
            ->color('danger'),
        ];
    }
    protected function getcolumns(): int
    {
        return 3;
    }
}

