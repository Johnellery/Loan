<?php

namespace App\Filament\Resources\PaidLoanResource\Pages;

use App\Filament\Resources\PaidLoanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaidLoan extends EditRecord
{
    protected static string $resource = PaidLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
