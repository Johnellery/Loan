<?php

namespace App\Filament\Resources\LoanResource\Widgets;

use App\Models\Applicant;

use App\Models\Billing;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
class LoanOverview extends BaseWidget
{
    public ?Model $record = null;
    protected function getStats(): array
    {
        $approvedBikeCount = Applicant::where('is_paid', 'Paid')->count();
        $availableBikeCount = Applicant::where('is_paid', 'Pending')->count();
        $outOfStockBikeCount = Applicant::where('is_paid', 'Missed')->count();
        $currentDate = Carbon::now();
        $billingRecords = Billing::where('billing_status', 'remitted')
            ->whereDate('created_at', $currentDate)
            ->count();

        return [
        Stat::make('Today Paid Payments', $billingRecords)
            ->description('Total number of successful payments made today.')
            ->color('primary'),
        Stat::make('Total Paid Payments', $approvedBikeCount)
            ->description('Total number of successful payments.')
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
        return 4;
    }
}

