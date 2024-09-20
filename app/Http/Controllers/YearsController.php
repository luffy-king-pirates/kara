<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class YearsController extends Controller
{
    public function index()
    {
        return view('settings.years'); // Assuming your view is in resources/views/settings/years
    }
}
