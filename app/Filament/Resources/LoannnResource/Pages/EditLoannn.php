<?php

namespace App\Filament\Resources\LoannnResource\Pages;

use App\Filament\Resources\LoannnResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoannn extends EditRecord
{
    protected static string $resource = LoannnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
