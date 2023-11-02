<?php

namespace App\Filament\Resources\LoanResource\Pages;

use App\Filament\Resources\LoanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

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
        $firstDayOfMonth = $currentDate->copy()->startOfMonth();
        $lastDayOfLastMonth = $firstDayOfMonth->copy()->subDay();
        $firstDayOfLastMonth = $lastDayOfLastMonth->copy()->startOfMonth();

        return [
            'all' => ListRecords\Tab::make(),
            'Today' => ListRecords\Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('created_at', $currentDate->toDateString())),
            'Yesterday' => ListRecords\Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('created_at', $yesterday->toDateString())),
            'Week' => ListRecords\Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('created_at', $thisWeekStart->toDateString())),
            'Month' => ListRecords\Tab::make()
                ->modifyQueryUsing(function (Builder $query) use ($firstDayOfLastMonth, $lastDayOfLastMonth) {
                    return $query->whereDate('created_at', '>=', $firstDayOfLastMonth->toDateString())
                                 ->whereDate('created_at', '<=', $lastDayOfLastMonth->toDateString());
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
