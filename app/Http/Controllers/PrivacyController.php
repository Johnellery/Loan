<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrivacyController extends Controller
{
    public function show()
    {
        $termsAndConditions = 'Your terms and conditions go here';

        return view('terms.privacy', compact('termsAndConditions'));
    }
}
