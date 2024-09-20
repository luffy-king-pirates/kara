<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductCountriesController extends Controller
{
    public function index()
    {
        return view('settings.product-countries'); // Assuming your view is in resources/views/settings/months
    }
}
