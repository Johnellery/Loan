<?php

namespace App\Filament\Resources\LoanResource\Widgets;

use App\Models\Applicant;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
class LoanOverview extends BaseWidget
{
    public ?Model $record = null;
    protected function getStats(): array
    {
        $approvedBikeCount = Applicant::where('is_paid', 'Paid')->count();
        $availableBikeCount = Applicant::where('is_paid', 'Pending')->count();
        $outOfStockBikeCount = Applicant::where('is_paid', 'Missed')->count();
        return [
        Stat::make('Paid Payments', $approvedBikeCount)
            ->description('Total successful payments.')
            ->color('success'),

        Stat::make('Pending Payments', $availableBikeCount)
            ->description('Payments awaiting processing.')
            ->color('warning'),
            Stat::make('Missed Payments', $outOfStockBikeCount)
            ->description('Payments that have not been made or are overdue.')
            ->color('danger'),
        ];
    }
    protected function getcolumns(): int
    {
        return 3;
    }
}

