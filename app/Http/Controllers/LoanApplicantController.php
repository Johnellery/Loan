<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use Illuminate\Http\Request;

class LoanApplicantController extends Controller
{
    // public function store(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:applicants',
    //         'phone' => 'required|string|max:15',
    //         // Add validation rules for other fields
    //     ]);

    //     // Create a new applicant instance and save it to the database
    //     $applicant = new Applicant();
    //     $applicant->name = $validatedData['name'];
    //     $applicant->email = $validatedData['email'];
    //     $applicant->phone = $validatedData['phone'];
    //     // Set other fields as needed
    //     $applicant->save();

    //     // You can also return a response, redirect, or perform any other actions here
    //     return redirect()->back()->with('success', 'Application submitted successfully.');
    // }
}
