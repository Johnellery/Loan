<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Bike;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
class BikeList extends Component
{
    // use WithPagination;

    public function render(): View
    {
        $user = Auth::user();
        return view('livewire.bike-list', [
            'bikes' => Bike::where('status', 'approved')
                ->where('branch_id', $user->branch_id)
                ->where('is_available', 'available')
                ->paginate(6),
        ]);
    }
}
