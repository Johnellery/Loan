<?php

namespace App\Filament\Resources\GcashResource\Pages;

use App\Filament\Resources\GcashResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGcash extends EditRecord
{
    protected static string $resource = GcashResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Payment method Updated';
    }
}
