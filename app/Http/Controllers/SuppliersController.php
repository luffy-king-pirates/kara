<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuppliersController extends Controller
{
    public function index()
    {
        return view('settings.suppliers'); // Assuming your view is in resources/views/settings/suppliers
    }
}
