<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Applicant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;

class Loan extends Component
{
    public function render(): View
    {
        $user = Auth::user();
        return view('livewire.loan', [
            'loan' => Applicant::where('user_id', $user->id)->paginate(5),
        ]);
    }
}

