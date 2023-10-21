<?php

namespace App\Filament\Resources\LoannResource\Pages;

use App\Filament\Resources\LoannResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoanns extends ListRecords
{
    protected static string $resource = LoannResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
