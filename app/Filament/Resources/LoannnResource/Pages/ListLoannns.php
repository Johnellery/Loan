<?php

namespace App\Filament\Resources\LoannnResource\Pages;

use App\Filament\Resources\LoannnResource;
use App\Models\Applicant;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoannns extends ListRecords
{
    protected static string $resource = LoannnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),

        ];
    }
}
