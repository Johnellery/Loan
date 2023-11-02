<?php

namespace App\Filament\Resources\BikeResource\Pages;

use App\Filament\Resources\BikeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
class ListBikes extends ListRecords
{
    protected static string $resource = BikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Add new Bike item'),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            BikeResource\Widgets\BikeOverview::class,
        ];
    }
    public function getTabs(): array
{

    return [
        'all' => ListRecords\Tab::make(),
        'Approved Bike' => ListRecords\Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'approved')),
        'Pending Bike' => ListRecords\Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),
        'Rejected Bike' => ListRecords\Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'rejected')),
        'Available' => ListRecords\Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('is_available', 'available')),
        'Unavailable' => ListRecords\Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('is_available', 'unavailable')),
    ];
}
}
