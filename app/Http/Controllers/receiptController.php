<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billing;
use App\Models\Applicant;
use PDF;
use Illuminate\Support\Facades\Blade;
class receiptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function generateINVOICE(Billing $record)
    {
        $loan = $record;

        $data = [
            'title' => 'Bisikleta Bike Shop Invoice',
            'date' => date('m/d/Y'),
            'loan' => $loan,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoice', $data);

        return $pdf->download('invoice.pdf');
    }
}
