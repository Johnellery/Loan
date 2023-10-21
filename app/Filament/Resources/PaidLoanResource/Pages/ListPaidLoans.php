<?php

namespace App\Filament\Resources\PaidLoanResource\Pages;

use App\Filament\Resources\PaidLoanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaidLoans extends ListRecords
{
    protected static string $resource = PaidLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
