<?php

namespace App\Filament\Resources\BillingResource\Pages;

use App\Filament\Resources\BillingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
class ListBillings extends ListRecords
{
    protected static string $resource = BillingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Add new Invoice'),
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
            'Remitted' => ListRecords\Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('billing_status', 'remitted')),
            'Processing' => ListRecords\Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('billing_status', 'processing')),
            'Not received' => ListRecords\Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('billing_status', 'not_received')),
        ];
    }

}
