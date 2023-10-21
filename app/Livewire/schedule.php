<?php

namespace App\Livewire;

use Illuminate\Support\Carbon;
use Livewire\Component;

use App\Models\Applicant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;

class schedule extends Component
{
    public $paymentDate;
    public $paymentnext;

    private static function calculateCurrentPaymentSchedule(Applicant $record, $decrement = true): string
    {
        $installment = $record->installment;
        $startDate = Carbon::parse($record->start)->startOfDay();
        $endDate = Carbon::parse($record->end)->startOfDay();
        $today = Carbon::now()->startOfDay();
        $currentPaymentDate = $startDate;

        if ($installment === '4') {
            while ($currentPaymentDate->isBefore($today)) {
                $currentPaymentDate->addWeek();
            }
        } elseif ($installment === '1') {
            while ($currentPaymentDate->isBefore($today)) {
                $currentPaymentDate->addMonth();
            }
        }

        // Optionally decrement by one week or one month
        if ($decrement) {
            if ($installment === '4') {
                $currentPaymentDate->subWeek();
            } elseif ($installment === '1') {
                $currentPaymentDate->subMonth();
            }
        }

        return $currentPaymentDate->format('F j, Y');
    }
    private static function calculatePaymentSchedule(Applicant $record): string
    {
        $installment = $record->installment;
        $startDate = Carbon::parse($record->start)->startOfDay();
        $endDate = Carbon::parse($record->end)->startOfDay();
        $today = Carbon::now()->startOfDay();
        $nextPaymentDate = $startDate;

        if ($installment === '4') {
            while ($nextPaymentDate->isBefore($today)) {
                $nextPaymentDate->addWeek();
            }
        } elseif ($installment === '1') {
            while ($nextPaymentDate->isBefore($today)) {
                $nextPaymentDate->addMonth();
            }
        }
        return $nextPaymentDate->format('F j, Y');
    }

    public function render(): View
    {
        $user = Auth::user();
        $loan = Applicant::where('user_id', $user->id)
                        ->where('status', 'approved')
                        ->where('ci_status', 'approved')
                        ->paginate(5);

        // Calculate the payment date for the first loan in the list
        if (!$loan->isEmpty()) {
            $this->paymentDate = $this->calculateCurrentPaymentSchedule($loan[0]);
        }
        if (!$loan->isEmpty()) {
            $this->paymentnext = $this->calculatePaymentSchedule($loan[0]);
        }

        return view('livewire.schedule', [
            'loan' => $loan,
        ]);
    }

}

