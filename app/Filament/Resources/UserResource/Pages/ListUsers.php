<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Add new User'),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            UserResource\Widgets\UserStatsOverview::class,
        ];
    }
}
