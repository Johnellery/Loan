<?php

namespace App\Filament\Resources\RepossessionResource\Pages;

use App\Filament\Resources\RepossessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
class ListRepossessions extends ListRecords
{
    protected static string $resource = RepossessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
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
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('payment_schedule', $currentDate->toDateString())),
            'Yesterday' => ListRecords\Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('payment_schedule', $yesterday->toDateString())),
            'Last Week' => ListRecords\Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('payment_schedule', $thisWeekStart->toDateString())),
            'Last Month' => ListRecords\Tab::make()
                ->modifyQueryUsing(function (Builder $query) use ($firstDayOfLastMonth, $lastDayOfLastMonth) {
                    return $query->whereDate('payment_schedule', '>=', $firstDayOfLastMonth->toDateString())
                                 ->whereDate('payment_schedule', '<=', $lastDayOfLastMonth->toDateString());
                }),
        ];
    }
}
