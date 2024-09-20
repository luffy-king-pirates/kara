<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UnitsController extends Controller
{
    public function index()
    {
        return view('settings.units'); // Assuming your view is in resources/views/settings/units
    }
}
