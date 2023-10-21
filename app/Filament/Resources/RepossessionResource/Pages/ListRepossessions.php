<?php

namespace App\Filament\Resources\RepossessionResource\Pages;

use App\Filament\Resources\RepossessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRepossessions extends ListRecords
{
    protected static string $resource = RepossessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
