<?php

namespace App\Filament\Resources\LoannResource\Pages;

use App\Filament\Resources\LoannResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoann extends EditRecord
{
    protected static string $resource = LoannResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
