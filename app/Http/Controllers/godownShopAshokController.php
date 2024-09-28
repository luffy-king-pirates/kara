<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class GodownShopAshokController extends Controller
{
    // Show the Brands view
    public function index(Request $request)
    {
        return view("stock-transfert.godwanToShopAshok");
    }
}
