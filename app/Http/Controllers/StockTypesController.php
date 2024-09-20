<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockTypesController extends Controller
{
    public function index()
    {
        return view('settings.stock-types'); // Assuming your view is in resources/views/settings/stock-types
    }
}
