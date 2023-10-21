<?php

namespace App\Filament\Resources\RateResource\Widgets;

use App\Models\Rate;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class RateOverview extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        $active = Rate::where('status', 'active')->count();
        $deactivated = Rate::where('status', 'deactivated')->count();
        return [
            Stat::make('Total Active Rate', $active)
            ->description('Number of Active Rate')
            ->color('success')
            ->icon('heroicon-s-user-group')
            ->descriptionIcon('heroicon-s-user-plus'),
        Stat::make('Total Deactivated Rate', $deactivated)
            ->description('Number of Deactivated Rate')
            ->color('danger')
            ->icon('heroicon-s-user-group')
            ->descriptionIcon('heroicon-s-user-minus'),
        ];
    }
    protected function getcolumns(): int
    {
        return 2;
    }
}
