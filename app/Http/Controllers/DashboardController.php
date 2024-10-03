<?php

namespace App\Http\Controllers;

use App\Models\Transfert;
use App\Models\TransfertDetails;
use App\Models\Item;
use App\Models\Units;
use App\Models\User;

use App\Models\CashDetails;
use App\Models\CreditDetails;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Cash;
use App\Models\Credit;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filter = '1day';



        return view('dashboard.dashboard', compact('filter'));
    }

    public function getSalesData(Request $request)
    {
        // Get the selected filter from the AJAX request
        $filter = $request->input('filter', '1day');

        // Determine the start date based on the filter
        switch ($filter) {
            case '1day':
                $startDate = Carbon::today();
                break;
            case '2days':
                $startDate = Carbon::today()->subDays(2);
                break;
            case '3days':
                $startDate = Carbon::today()->subDays(3);
                break;
            case 'month':
                $startDate = Carbon::today()->subMonth();
                break;
            case 'year':
                $startDate = Carbon::today()->subYear();
                break;
            default:
                $startDate = Carbon::today();
                break;
        }
        $endDate = Carbon::now();

        // Calculate total sales for both cash and credit within the selected period
        $cashSalesTotal = Cash::whereBetween('creation_date', [$startDate, $endDate])->sum('total_amount');
        $creditSalesTotal = Credit::whereBetween('creation_date', [$startDate, $endDate])->sum('total_amount');

        // Return the sales data as JSON for Chart.js
        return response()->json([
            'cashSales' => $cashSalesTotal,
            'creditSales' => $creditSalesTotal,
        ]);




    }






    public function getHourlySales()
{
    $currentDate = Carbon::now();

    // Group by hour for the current day and return total sales for each hour
    $cashSales = Cash::whereDate('created_at', $currentDate->format('Y-m-d'))
                    ->selectRaw('HOUR(created_at) as hour, SUM(total_amount) as total')
                    ->groupBy('hour')
                    ->orderBy('hour')
                    ->get();

    $creditSales = Credit::whereDate('created_at', $currentDate->format('Y-m-d'))
                    ->selectRaw('HOUR(created_at) as hour, SUM(total_amount) as total')
                    ->groupBy('hour')
                    ->orderBy('hour')
                    ->get();

    // Create an array of labels for each hour (0 to 23)
    $labels = range(0, 23);

    // Prepare data with default values (0) for missing hours
    $cashHourlyData = array_fill(0, 24, 0); // Fill an array of size 24 with default value 0
    $creditHourlyData = array_fill(0, 24, 0);

    // Fill in the actual sales data for cash, casting the total as a float
    foreach ($cashSales as $cashSale) {
        $cashHourlyData[$cashSale->hour] = (float)$cashSale->total;
    }

    // Fill in the actual sales data for credit, casting the total as a float
    foreach ($creditSales as $creditSale) {
        $creditHourlyData[$creditSale->hour] = (float)$creditSale->total;
    }

    return response()->json([
        'cash' => $cashHourlyData,
        'credit' => $creditHourlyData,
        'labels' => $labels  // This will return [0, 1, 2, ..., 23] representing hours of the day
    ]);
}
    // Fetch daily sales for the current week (grouped by day)
    public function getWeeklySales()
    {
        $currentDate = Carbon::now();

        // Group by day within the current week
        $cashSales = Cash::whereBetween('created_at', [$currentDate->startOfWeek(), $currentDate->endOfWeek()])
                        ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get();

        $creditSales = Credit::whereBetween('created_at', [$currentDate->startOfWeek(), $currentDate->endOfWeek()])
                        ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get();

        // Labels for the days of the current week
        $labels = [];
        $startOfWeek = $currentDate->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $labels[] = $startOfWeek->copy()->addDays($i)->format('Y-m-d');
        }

        return response()->json([
            'cash' => $cashSales,
            'credit' => $creditSales,
            'labels' => $labels
        ]);
    }

    // Fetch daily sales for the current month (grouped by day)
    public function getMonthlySales()
    {
        $currentDate = Carbon::now();

        // Group by day within the current month
        $cashSales = Cash::whereYear('created_at', $currentDate->year)
                        ->whereMonth('created_at', $currentDate->month)
                        ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get();

        $creditSales = Credit::whereYear('created_at', $currentDate->year)
                        ->whereMonth('created_at', $currentDate->month)
                        ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get();

        // Get all the days in the current month
        $labels = [];
        $startOfMonth = $currentDate->startOfMonth();
        $daysInMonth = $currentDate->daysInMonth;
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $labels[] = $startOfMonth->copy()->addDays($i - 1)->format('Y-m-d');
        }

        return response()->json([
            'cash' => $cashSales,
            'credit' => $creditSales,
            'labels' => $labels
        ]);
    }

    // Fetch daily sales for the current year (grouped by month, but we'll show daily labels)
    public function getYearlySales()
    {
        $currentDate = Carbon::now();

        // Group by day within the current year
        $cashSales = Cash::whereYear('created_at', $currentDate->year)
                        ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get();

        $creditSales = Credit::whereYear('created_at', $currentDate->year)
                        ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get();

        // Generate labels for each day of the current year up to today
        $labels = [];
        $startOfYear = $currentDate->startOfYear();
        $daysInYear = $currentDate->dayOfYear;
        for ($i = 1; $i <= $daysInYear; $i++) {
            $labels[] = $startOfYear->copy()->addDays($i - 1)->format('Y-m-d');
        }

        return response()->json([
            'cash' => $cashSales,
            'credit' => $creditSales,
            'labels' => $labels
        ]);
    }





    public function salesChart(Request $request)
    {
        // Retrieve time range from request, default to 1 day
        $timeRange = $request->get('time_range', '1_day');

        // Define time ranges
        $timeRanges = [
            '1_day' => Carbon::now()->subDay(),
            '3_days' => Carbon::now()->subDays(3),
            '1_week' => Carbon::now()->subWeek(),
            '2_weeks' => Carbon::now()->subWeeks(2),
            '1_month' => Carbon::now()->subMonth(),
            '2_months' => Carbon::now()->subMonths(2),
            '1_year' => Carbon::now()->subYear(),
        ];

        // Get the start date based on the selected time range
        $startDate = $timeRanges[$timeRange];

        // Fetch cash sales data within the time range
        $cashSales = CashDetails::whereHas('cash', function ($query) use ($startDate) {
            $query->where('creation_date', '>=', $startDate);
        })->get();

        // Fetch credit sales data within the time range
        $creditSales = CreditDetails::whereHas('credit', function ($query) use ($startDate) {
            $query->where('creation_date', '>=', $startDate);
        })->get();

        // Group the sales data by item
        $cashItems = $cashSales->groupBy('item_id')->map(function ($itemGroup) {
            return $itemGroup->sum('quantity');
        });

        $creditItems = $creditSales->groupBy('item_id')->map(function ($itemGroup) {
            return $itemGroup->sum('quantity');
        });

        // Prepare data for the chart
        $items = $cashItems->keys()->merge($creditItems->keys())->unique(); // Get unique item IDs

        // Fetch item names using the unique IDs
        $itemNames = Item::whereIn('id', $items)->pluck('item_name', 'id'); // Get item names mapped by ID

        // Prepare cash and credit data
        $cashData = $items->map(fn($itemId) => $cashItems->get($itemId, 0));
        $creditData = $items->map(fn($itemId) => $creditItems->get($itemId, 0));

        // Return JSON response for AJAX requests
        return response()->json([
            'itemNames' => $itemNames, // Include item names in the response
            'cashData' => $cashData,
            'creditData' => $creditData,
        ]);
    }


     // Fetch top 10 sold items in cash transactions
     public function getTopSoldItemsCash()
     {
         $topItems = CashDetails::select('item_id', \DB::raw('SUM(quantity) as total_sold'))
             ->with('item.brand')  // Load the brand relation
             ->groupBy('item_id')
             ->orderByDesc('total_sold')
             ->limit(10)
             ->get();

         return response()->json($topItems);
     }

     // Fetch top 10 sold items in credit transactions
     public function getTopSoldItemsCredit()
     {
         $topItems = CreditDetails::select('item_id', \DB::raw('SUM(quantity) as total_sold'))
             ->with('item.brand')  // Load the brand relation
             ->groupBy('item_id')
             ->orderByDesc('total_sold')
             ->limit(10)
             ->get();

         return response()->json($topItems);
     }

     // Fetch worst 10 sold items in cash transactions
     public function getWorstSoldItemsCash()
     {
         $worstItems = CashDetails::select('item_id', \DB::raw('SUM(quantity) as total_sold'))
             ->with('item.brand')  // Load the brand relation
             ->groupBy('item_id')
             ->orderBy('total_sold')
             ->limit(10)
             ->get();

         return response()->json($worstItems);
     }

     // Fetch worst 10 sold items in credit transactions
     public function getWorstSoldItemsCredit()
     {
         $worstItems = CreditDetails::select('item_id', \DB::raw('SUM(quantity) as total_sold'))
             ->with('item.brand')  // Load the brand relation
             ->groupBy('item_id')
             ->orderBy('total_sold')
             ->limit(10)
             ->get();

         return response()->json($worstItems);
     }

}
