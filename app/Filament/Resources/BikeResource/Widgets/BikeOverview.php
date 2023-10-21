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
        $approvedBikeCount = Bike::where('status', 'approved')->count();
        $availableBikeCount = Bike::where('is_available', 'available')->count();
        $outOfStockBikeCount = Bike::where('is_available', 'unavailable')->count();
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

