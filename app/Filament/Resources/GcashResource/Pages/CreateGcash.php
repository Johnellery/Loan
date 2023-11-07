<?php

namespace App\Filament\Resources\GcashResource\Pages;

use App\Filament\Resources\GcashResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGcash extends CreateRecord
{
    protected static string $resource = GcashResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Payment method Created';
    }
}
