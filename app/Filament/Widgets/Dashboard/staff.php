<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Applicant;
use App\Models\Billing;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class staff extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        $pendingAccountCount = Applicant::where('status', 'pending')
            ->where('branch_id', $branchId)
            ->count();
            $applicantsDueTodayCount = Billing::where('billing_status', 'processing')
            ->where('branch_id', $branchId)
            ->count();

        return [
            Stat::make('Applicants Awaiting Review', $pendingAccountCount)
            ->description('Number of Applicants Waiting for Review')
            ->color('info'),

            Stat::make('Transaction Awaiting Review', $applicantsDueTodayCount)
                ->description('Number of Transaction Waiting for Review')
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
        return $user->role->name === 'Staff'; // Use comparison operator (===) here
    }
}
