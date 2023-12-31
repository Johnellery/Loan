<?php

namespace App\Filament\Resources\ApplicantResource\Widgets;

use App\Models\Applicant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
class ApplicantOverview extends BaseWidget
{
    public ?Model $record = null;
    protected function getStats(): array
    {
        $user = Auth::user();
        $approvedBikeCount = Applicant::where('status', 'pending')
        ->where('branch_id', $user->branch_id)
        ->count();
        $availableBikeCount = Applicant::where('ci_status', 'pending')
        ->where('branch_id', $user->branch_id)->count();

        return [
        Stat::make('Applicants Awaiting Review', $approvedBikeCount)
            ->description('Number of Applicants Waiting for Review.')
            ->color('warning'),
        Stat::make('Total Pending Credit Investigation', $availableBikeCount)
            ->description('Payments awaiting processing.')
            ->color('warning')
        ];
    }
    protected function getcolumns(): int
    {
        return 2;
    }
}

