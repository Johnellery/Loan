<?php

namespace App\Http\Controllers;
use App\Mail\todaypayment;
use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function index(Applicant $record)
{
    $applicant = $record;
    $first = $applicant->first;
    $last = $applicant->last;
    $middle = $applicant->middle;
    if ($applicant->user) {
        $email = $applicant->user->email;

        $mailData = [
            'title' => 'Bisikleta Bike Shop - Payment Reminder',
            'body' => 'Dear, '. $first . ',',
        ];

        Mail::to($email)->send(new todaypayment($mailData));
        return redirect('/admin/repossessions');
    } else {
        dd('User not found for the given Applicant.');
    }
}

}
