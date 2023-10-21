<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Applicant;
use App\Models\Bike;
use App\Models\Billing;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PendingCount extends BaseWidget
{

    protected static ?int $sort = 3;
    protected function getStats(): array
    {
        $user = Auth::user();
        $branchId = $user->branch_id;
        $pendingBikeCount = Bike::where('status', 'pending')

        ->count();
        $pendingApplicantCount = Applicant::where('status', 'pending')->count();
        $applicantsDueTodayCount = Billing::where('billing_status', 'processing')
        ->count();


        return [
            Stat::make('Bikes Awaiting Approval', $pendingBikeCount)
                ->description('Number of Bikes Pending Approval')
                ->color('warning'),
            Stat::make('Applicants Awaiting Review', $pendingApplicantCount)
                ->description('Number of Applicants Waiting for Review')
                ->color('info'),

                Stat::make('Transaction Awaiting Review', $applicantsDueTodayCount)
                ->description('Number of Transaction Waiting for Review')
                ->color('success')

        ];
    }
    public static function canView(): bool
    {
        $user = Auth::user();
        return $user->role->name === 'Admin'; // Use comparison operator (===) here
    }
}
