<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserStatsOverview extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        $user = Auth::user();

        $active = User::where('status', 'active')->count();
        $deactivated = User::where('status', 'deactivated')->count();

        return [
            Stat::make('Total Active Accounts', $active)
                ->description('Number of Active accounts')
                ->color('success')
                ->icon('heroicon-s-user-group')
                ->descriptionIcon('heroicon-s-user-plus'),
            Stat::make('Total Deactivated Accounts', $deactivated)
                ->description('Number of Deactivated accounts')
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
