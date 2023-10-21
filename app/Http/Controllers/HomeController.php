<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function search(Request $request)
{
    $searchQuery = $request->input('search');

    // Perform the search logic based on the $searchQuery

    // Return the filtered results to the view
}


}
