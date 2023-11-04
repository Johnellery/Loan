<?php

namespace App\Filament\Resources\ApplicantResource\Pages;

use App\Filament\Resources\ApplicantResource;
use App\Models\Applicant;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
class ListApplicants extends ListRecords
{
    protected static string $resource = ApplicantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Generate Reports')
            ->color('warning')
            ->url(fn() => route('report-pdf-form'))
            ->openUrlInNewTab()
            ->visible(function () {
                $user = Auth::user();
                return $user->role->name === 'Admin' || $user->role->name === 'Staff';
            }),
            Actions\Action::make('Loan Summary')
            ->color('success')
            ->url(fn() => route('summary-form'))
            ->openUrlInNewTab()
            ->visible(function () {
                $user = Auth::user();
                return $user->role->name === 'Admin' || $user->role->name === 'Staff';
            }),
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
