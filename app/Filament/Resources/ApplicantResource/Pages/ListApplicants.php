<?php

namespace App\Filament\Resources\ApplicantResource\Pages;

use App\Filament\Resources\ApplicantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
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

public function getTabs(): array
{
    return [
        'all' => ListRecords\Tab::make(),
        'Status Approved' => ListRecords\Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'approved')),
        'Status Pending' => ListRecords\Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),
        'Status Rejected' => ListRecords\Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'rejected')),
        'CI Approved' => ListRecords\Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('ci_status', 'approved')),
        'CI Pending' => ListRecords\Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('ci_status', 'pending')),
        'CI Rejected' => ListRecords\Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('ci_status', 'rejected')),
    ];
}
}
