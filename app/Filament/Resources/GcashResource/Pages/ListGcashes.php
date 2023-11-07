<?php

namespace App\Filament\Resources\GcashResource\Pages;

use App\Filament\Resources\GcashResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGcashes extends ListRecords
{
    protected static string $resource = GcashResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
