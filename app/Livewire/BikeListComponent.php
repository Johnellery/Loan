<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Bike;
use Illuminate\Support\Facades\Auth;
class BikeListComponent extends Component
{
    public function render()
    {
        $user = Auth::user();
        $bikes = Bike::where('status', 'approved')
            ->where('branch_id', $user->branch_id)
            ->paginate(6);

        return view('livewire.bike-list', ['bikes' => $bikes]);
    }
}
