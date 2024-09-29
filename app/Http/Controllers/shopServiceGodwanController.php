<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class shopServiceGodwanController extends Controller
{
    // Show the Brands view
    public function index(Request $request)
    {
        return view("stock-transfert.shopServicesToGodwan");
    }
}
