<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class GodownShopController extends Controller
{
    // Show the Brands view
    public function index(Request $request)
    {
        return view("stock-transfert.godwanToShop");
    }
}
