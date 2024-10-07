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
use Illuminate\Support\Facades\DB;
use App\Models\Cash;
use App\Models\Credit;
use App\Models\Customers;
use App\Models\Brand;



class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::all();
        $customers = Customers::all();
        $brands = Brand::all();

        return view('dashboard.dashboard', compact('items', 'customers', 'brands'));
    }

    public function getSalesData(Request $request)
    {
        // Retrieve input parameters or use defaults
        $fromDate = $request->input('from_date', Carbon::today());
        $toDate = $request->input('to_date', Carbon::today());
        $itemId = $request->input('item_id', 'all');
        $customerId = $request->input('customer_id', 'all');
        $brandId = $request->input('brand_id', 'all');

        // Filter sales by the provided inputs for cash and credit sales
        $cashSalesQuery = CashDetails::whereHas('cash', function($query) use ($fromDate, $toDate) {
            $query->whereBetween('creation_date', [$fromDate, $toDate]);
        });

        $creditSalesQuery = CreditDetails::whereHas('credit', function($query) use ($fromDate, $toDate) {
            $query->whereBetween('creation_date', [$fromDate, $toDate]);
        });

        // Apply filters based on item, customer, and brand
        if ($itemId !== 'all') {
            $cashSalesQuery->where('item_id', $itemId);
            $creditSalesQuery->where('item_id', $itemId);
        }

        if ($customerId !== 'all') {
            $cashSalesQuery->whereHas('cash.customer', function($query) use ($customerId) {
                $query->where('id', $customerId);
            });
            $creditSalesQuery->whereHas('credit.customer', function($query) use ($customerId) {
                $query->where('id', $customerId);
            });
        }

        if ($brandId !== 'all') {
            $cashSalesQuery->whereHas('item.brand', function($query) use ($brandId) {
                $query->where('id', $brandId);
            });
            $creditSalesQuery->whereHas('item.brand', function($query) use ($brandId) {
                $query->where('id', $brandId);
            });
        }

        // Sum quantities for cash and credit sales
        $cashSales = $cashSalesQuery->sum('quantity');
        $creditSales = $creditSalesQuery->sum('quantity');

        // Fetch top sold items for cash sales
        $topSoldItemsCash = $cashSalesQuery->select('item_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('item_id')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get()
            ->map(function($sale) {
                $item = Item::with('brand')->find($sale->item_id);
                $cash = CashDetails::with('cash.customer')->where('item_id', $sale->item_id)->first();

                // Map item, brand, and customer names
                $sale->item_name = $item->item_name;
                $sale->brand_name = $item->brand ? $item->brand->brand_name : 'Unknown Brand';
                $sale->customer_name = $cash->cash->customer ? $cash->cash->customer->customer_name : 'Unknown Customer';

                return $sale;
            });

        // Fetch top sold items for credit sales
        $topSoldItemsCredit = $creditSalesQuery->select('item_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('item_id')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get()
            ->map(function($sale) {
                $item = Item::with('brand')->find($sale->item_id);
                $credit = CreditDetails::with('credit.customer')->where('item_id', $sale->item_id)->first();

                // Map item, brand, and customer names
                $sale->item_name = $item->item_name;
                $sale->brand_name = $item->brand ? $item->brand->brand_name : 'Unknown Brand';
                $sale->customer_name = $credit->credit->customer ? $credit->credit->customer->customer_name : 'Unknown Customer';

                return $sale;
            });

        // Fetch worst sold items for cash sales
        $worstSoldItemsCash = $cashSalesQuery->select('item_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('item_id')
            ->orderBy('total_sold')
            ->limit(10)
            ->get()
            ->map(function($sale) {
                $item = Item::with('brand')->find($sale->item_id);
                $cash = CashDetails::with('cash.customer')->where('item_id', $sale->item_id)->first();

                // Map item, brand, and customer names
                $sale->item_name = $item->item_name;
                $sale->brand_name = $item->brand ? $item->brand->brand_name : 'Unknown Brand';
                $sale->customer_name = $cash->cash->customer ? $cash->cash->customer->customer_name : 'Unknown Customer';

                return $sale;
            });

        // Fetch worst sold items for credit sales
        $worstSoldItemsCredit = $creditSalesQuery->select('item_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('item_id')
            ->orderBy('total_sold')
            ->limit(10)
            ->get()
            ->map(function($sale) {
                $item = Item::with('brand')->find($sale->item_id);
                $credit = CreditDetails::with('credit.customer')->where('item_id', $sale->item_id)->first();

                // Map item, brand, and customer names
                $sale->item_name = $item->item_name;
                $sale->brand_name = $item->brand ? $item->brand->brand_name : 'Unknown Brand';
                $sale->customer_name = $credit->credit->customer ? $credit->credit->customer->customer_name : 'Unknown Customer';

                return $sale;
            });

        // Return the sales data in JSON format
        return response()->json([
            'cashSales' => $cashSales,
            'creditSales' => $creditSales,
            'topSoldItemsCash' => $topSoldItemsCash,
            'topSoldItemsCredit' => $topSoldItemsCredit,
            'worstSoldItemsCash' => $worstSoldItemsCash,
            'worstSoldItemsCredit' => $worstSoldItemsCredit,
            'labels' => ['Sales'], // Optional: labels for charts
        ]);
    }




}
