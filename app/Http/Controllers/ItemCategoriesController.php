<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItemCategoriesController extends Controller
{
    public function index()
    {
        return view('settings.item-categories'); // Assuming your view is in resources/views/settings/item-categories
    }
}
