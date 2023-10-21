<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Applicant;
use PDF;
class PDFController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function generatePDF(Applicant $record)
    {
        $loan = $record;

        $data = [
            'title' => 'Loan Summary',
            'date' => date('m/d/Y'),
            'loan' => $loan,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('myPDF', $data);

        return $pdf->download('loanSummary.pdf');
    }
}
