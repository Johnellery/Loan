<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Applicant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use PDF;

class ReportController extends Controller
{
    /**
     * Display a form to input the date.
     *
     * @return \Illuminate\Http\Response
     */
    public function reportPDForm()
    {
        return view('dateForm');
    }

    /**
     * Generate and download the PDF report with the specified date.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

    public function reportPDF(Request $request)
    {
        $date = $request->input('date');
        $user = Auth::user();
        $branch = $user->branch->name;
        $data = [
            'title' => 'Bisikleta Bike Shop',
            'date' => null, // Initialize date variable
            'loan' => null,
            'billing' => null,
            'repossession' => null,
        ];

        if ($date === 'today') {
            $currentDate = Carbon::now();
            $data['date'] = $currentDate->format('m/d/Y');
            $data['loan'] = Applicant::whereDate('created_at', $currentDate)->get();
            $data['billing'] = Billing::whereDate('created_at', $currentDate)->get();
            $data['repossession'] = Applicant::whereDate('repossession_date', $currentDate)->get();
        } elseif ($date === 'this_week') {
            $data['date'] = 'This Week';
            $data['loan'] = Applicant::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
            $data['billing'] = Billing::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
            $data['repossession'] = Applicant::whereBetween('repossession_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
        } elseif ($date === 'this_month') {
            $data['date'] = 'This Month';
            $data['loan'] = Applicant::whereMonth('created_at', Carbon::now()->month)->get();
            $data['billing'] = Billing::whereMonth('created_at', Carbon::now()->month)->get();
            $data['repossession'] = Applicant::whereMonth('repossession_date', Carbon::now()->month)->get();
        } elseif ($date === 'custom') {
            $customDateFrom = $request->input('custom_date_from');
            $customDateUntil = $request->input('custom_date_until');
            $data['date'] = "Custom Date: " . date('m/d/Y', strtotime($customDateFrom)) . ' - ' . date('m/d/Y', strtotime($customDateUntil));

            $data['loan'] = Applicant::whereBetween('created_at', [$customDateFrom, $customDateUntil])->get();
            $data['billing'] = Billing::whereBetween('created_at', [$customDateFrom, $customDateUntil])->get();
            $data['repossession'] = Applicant::whereBetween('repossession_date', [$customDateFrom, $customDateUntil])->get();
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('report', $data);

        return $pdf->download($branch . 'Report' .'.pdf');
    }

}
