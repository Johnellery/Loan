<?php

namespace App\Filament\Resources\ApplicantResource\Pages;

use App\Filament\Resources\ApplicantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListApplicants extends ListRecords
{
    protected static string $resource = ApplicantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->visible(function () {
                $user = Auth::user();
                return $user->role->name === 'Admin' || $user->role->name === 'Staff';
            })
            ->label('Add new Loan applicant'),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            ApplicantResource\Widgets\ApplicantOverview::class,
        ];
    }
}
