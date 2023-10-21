<?php

namespace App\Filament\Resources\PaidLoanResource\Pages;

use App\Filament\Resources\PaidLoanResource;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Settings extends Page
{
    protected static string $resource = PaidLoanResource::class;

    protected static string $view = 'filament.resources.paid-loan-resource.pages.settings';

    public function mount(): void
    {
        parent::mount();
        $user = Auth::user();
        abort_unless( $user->role->name === 'Admin',403);
    }
}
