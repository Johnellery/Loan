<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Billing;
use App\Models\Applicant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use DataTables;
class transaction extends Component
{
    // use WithPagination;

    public function render(): View
    {
        $user = Auth::user();
        return view('livewire.transaction', [
            'billing' => Billing::where( 'applicant_user_id',  $user->id)
                ->paginate(5),
        ]);


    }


}
 // public $to_date;

    // public function render()
    // {
    //     return view('livewire.transaction', [
    //         'transactions' => $this->getData(),
    //     ]);
    // }

    // private function getData()
    // {
    //     $data = Billing::select('*');

    //     if ($this->from_date && $this->to_date) {
    //         $data = $data->whereBetween('created_at', [$this->from_date, $this->to_date]);
    //     }

    //     return DataTables::of($data)->addIndexColumn()->make(true);
    // }
