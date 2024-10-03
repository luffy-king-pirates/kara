<?php

namespace App\Http\Controllers;

use App\Models\Transfert;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardStockTransfertController extends Controller
{
    public function index(Request $request)
    {




        return view('dashboard.dashboardStockTransfert');
    }
    public function getTransfertStatsAjax()
    {
        $routes = [
            'godown_to_shop' => ['from' => 'godown', 'to' => 'shop'],
            'shop_to_godown' => ['from' => 'shop', 'to' => 'godown'],
            'shop_ashok_to_godown' => ['from' => 'shop_ashok', 'to' => 'godown'],
            'godown_to_shop_service' => ['from' => 'godown', 'to' => 'shop-service'],
        ];

        $stats = [];

        foreach ($routes as $key => $route) {
            $today = Transfert::where('transfert_from', $route['from'])
                ->where('transfert_to', $route['to'])
                ->whereDate('created_at', Carbon::today())
                ->count();

            $yesterday = Transfert::where('transfert_from', $route['from'])
                ->where('transfert_to', $route['to'])
                ->whereDate('created_at', Carbon::yesterday())
                ->count();

            $percentageChange = $this->calculatePercentageChange($yesterday, $today);

            $stats[$key] = [
                'today' => $today,
                'yesterday' => $yesterday,
                'percentage_change' => $percentageChange
            ];
        }

        return response()->json($stats);
    }

    // Function to calculate the percentage change
    private function calculatePercentageChange($old, $new)
    {
        if ($old == 0 && $new > 0) {
            return 100; // 100% increase from 0 to a positive number
        } elseif ($old == 0 && $new == 0) {
            return 0; // No change
        } elseif ($new == 0 && $old > 0) {
            return -100; // 100% decrease
        } else {
            return (($new - $old) / $old) * 100;
        }
    }
}
