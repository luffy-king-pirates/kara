<?php

namespace App\Http\Controllers;

use App\Models\Transfert;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\TransfertDetails;


class DashboardStockTransfertController extends Controller
{
    public function index(Request $request)
    {

        $items = Item::all();


        return view('dashboard.dashboardStockTransfert',compact('items'));
    }

    public function getTransfertStatsAjax (Request $request)
{
    // Define the routes with their from and to locations
    $routes = [
        'godown_to_shop' => ['from' => 'godown', 'to' => 'shop'],
        'shop_to_godown' => ['from' => 'shop', 'to' => 'godown'],
        'shop_ashok_to_godown' => ['from' => 'shop_ashok', 'to' => 'godown'],
        'godown_to_shop_service' => ['from' => 'godown', 'to' => 'shop-service'],
    ];

    // Get the date range and item ID from the request
    $fromDate = $request->input('from_date'); // e.g., '2024-10-01'
    $toDate = $request->input('to_date');     // e.g., '2024-10-07'
    $itemId = $request->input('item_id');     // e.g., 1 or 'all'

    $results = [];

    // Loop through each route to calculate the transferred quantities
    foreach ($routes as $key => $route) {
        $query = TransfertDetails::whereHas('godownshop', function ($q) use ($route, $fromDate, $toDate) {
                $q->where('transfert_from', $route['from'])
                  ->where('transfert_to', $route['to'])
                  ->whereBetween('transfert_date', [$fromDate, $toDate]);
            });

        // Check if itemId is 'all' or a specific item ID
        if ($itemId !== 'all') {
            $query->where('item_id', $itemId);
        }

        // Sum the quantities for the specified item or all items
        $totalQuantity = $query->sum('quantity');

        $results[$key] = [
            'route' => "{$route['from']} to {$route['to']}",
            'total_quantity' => $totalQuantity,
        ];
    }

    return response()->json($results);
}



}
