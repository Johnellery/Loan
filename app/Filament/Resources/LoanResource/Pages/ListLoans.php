<?php

namespace App\Filament\Resources\LoanResource\Pages;

use App\Filament\Resources\LoanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Carbon;
class ListLoans extends ListRecords
{
    protected static string $resource = LoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            LoanResource\Widgets\LoanOverview::class,
        ];
    }
    public function getTabs(): array
    {
        $currentDate = Carbon::now();
        $yesterday = $currentDate->copy()->subDay();
        $thisWeekStart = $currentDate->copy()->startOfWeek();
        $nextWeekStart = $currentDate->copy()->addWeek()->startOfWeek();
        $lastWeekStart = $currentDate->copy()->subWeek()->startOfWeek();
        $firstDayOfMonth = $currentDate->copy()->startOfMonth();
        $lastDayOfLastMonth = $firstDayOfMonth->copy()->subDay();
        $firstDayOfLastMonth = $lastDayOfLastMonth->copy()->startOfMonth();
        $firstDayOfNextMonth = $firstDayOfLastMonth->copy()->addMonth()->startOfMonth();
        $lastDayOfNextMonth = $firstDayOfNextMonth->copy()->endOfMonth();
        return [
            'all' => ListRecords\Tab::make(),
            'Today' => ListRecords\Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('payment_schedule', $currentDate->toDateString())),
            'Yesterday' => ListRecords\Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('payment_schedule', $yesterday->toDateString())),
            'Last Week' => ListRecords\Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('payment_schedule', $lastWeekStart->toDateString())),
            'Last Month' => ListRecords\Tab::make()
                ->modifyQueryUsing(function (Builder $query) use ($firstDayOfLastMonth, $lastDayOfLastMonth) {
                    return $query->whereDate('payment_schedule', '>=', $firstDayOfLastMonth->toDateString())
                                 ->whereDate('payment_schedule', '<=', $lastDayOfLastMonth->toDateString());
                }),
            'Next Week' => ListRecords\Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('payment_schedule', $nextWeekStart->toDateString())),
            'Next Month' => ListRecords\Tab::make()
                ->modifyQueryUsing(function (Builder $query) use ($firstDayOfNextMonth, $lastDayOfNextMonth) {
                    return $query->whereDate('payment_schedule', '>=', $firstDayOfNextMonth->toDateString())
                                 ->whereDate('payment_schedule', '<=', $lastDayOfNextMonth->toDateString());
                }),
            'Paid' => ListRecords\Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_paid', 'Paid')),
            'Pending' => ListRecords\Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_paid', 'Pending')),
            'Missed' => ListRecords\Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_paid', 'Missed')),
        ];
    }
}
