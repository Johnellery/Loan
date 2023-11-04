<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Applicant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use PDF;

class summaryController extends Controller
{
    /**
     * Display a form to input the date.
     *
     * @return \Illuminate\Http\Response
     */
    public function summary_form()
    {
        return view('summary');
    }

    /**
     * Generate and download the PDF report with the specified date.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

   public function summary(Request $request)
{
    $ids = $request->input('applicant'); // This will be an array of selected IDs
    $user = Auth::user();
    $branch = $user->branch->name;

    // Use whereIn to get multiple models based on the selected IDs
    $loans = Applicant::whereIn('id', $ids)->with('billing')->get();
// dd($loans);
    // Check if any valid models were found
    if ($loans->isNotEmpty()) {
        $data = [
            'title' => 'Bisikleta Bike Shop - Loan Summary',
            'loans' => $loans, // Pass the collection of loans
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('loansummary', $data);
        return $pdf->download($branch . 'LoanSummary' . '.pdf');
    } else {
        // Handle the case where no loans with the specified IDs were found
        // You might want to return a response or redirect to an error page
    }
}


}
