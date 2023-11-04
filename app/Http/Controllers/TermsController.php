<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TermsController extends Controller
{
    public function show()
    {
        $termsAndConditions = 'Your terms and conditions go here';

        return view('terms.show', compact('termsAndConditions'));
    }
}
