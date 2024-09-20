<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MonthsController extends Controller
{
    public function index()
    {
        return view('settings.months'); // Assuming your view is in resources/views/settings/months
    }
}
