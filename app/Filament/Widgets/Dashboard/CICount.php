<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Applicant;
use App\Models\Bike;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CICount extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        $pendingAccountCount = Applicant::where('ci_status', 'pending')
            ->where('branch_id', $branchId)
            ->count();
            $applicantsDueTodayCount = Applicant::where('payment_description', 'Today')
            ->whereIn('is_paid', ['Missed', 'Pending'])
            ->where('branch_id', $branchId)
            ->count();

        return [
            Stat::make('Pending Credit Investigation', $pendingAccountCount)
                ->description('Total Pending Credit Investigation')
                ->color('warning'),
            Stat::make('Due Today', $applicantsDueTodayCount)
                ->description('Loan Payment Due Today')
                ->color('success')

        ];
    }
    protected function getcolumns(): int
    {
        return 2;
    }
    public static function canView(): bool
    {
        $user = Auth::user();
        return $user->role->name === 'Collector'; // Use comparison operator (===) here
    }
}
