<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CurrenciesExport;

class CurrenciesController extends Controller
{
    // Show the Currencies view
    public function index(Request $request)
    {
        $users = User::all();
        if ($request->ajax()) {
            $currencies = Currency::with(['createdByUser:id,name', 'updatedByUser:id,name'])
                ->select(['id', 'currencie_name', 'currencie_value', 'created_at', 'updated_at', 'created_by', 'updated_by']);

            return DataTables::of($currencies)
                ->addColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('M d, Y h:i A');
                })
                ->addColumn('updated_at', function ($row) {
                    return $row->updated_at ? Carbon::parse($row->updated_at)->format('M d, Y h:i A') : 'Not updated';
                })
                ->addColumn('created_by', function ($row) {
                    return $row->createdByUser ? $row->createdByUser->name : 'Unknown';
                })
                ->addColumn('updated_by', function ($row) {
                    return $row->updatedByUser ? $row->updatedByUser->name : 'Not updated';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-primary edit-currency">Edit</a>
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-danger delete-currency">Delete</a>
                    ';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search['value'] != '') {
                        $searchValue = $request->search['value'];
                        $query->where(function($q) use ($searchValue) {
                            $q->where('currencie_name', 'like', "%$searchValue%")
                              ->orWhereHas('createdByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              })
                              ->orWhereHas('updatedByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              });
                        });
                    }

                    if ($request->has('currencie_name') && $request->currencie_name != '') {
                        $query->where('currencie_name', 'like', "%" . $request->currencie_name . "%");
                    }
                    if ($request->has('currencie_value') && $request->currencie_value != '') {
                        $query->where('currencie_value', $request->currencie_value);
                    }
                    if ($request->has('created_at') && $request->created_at != '') {
                        $query->whereDate('created_at', $request->created_at);
                    }
                    if ($request->has('updated_at') && $request->updated_at != '') {
                        $query->whereDate('updated_at', $request->updated_at);
                    }
                    if ($request->filled('created_by')) {
                        $query->where('created_by', $request->created_by);
                    }
                    if ($request->filled('updated_by')) {
                        $query->where('updated_by', $request->updated_by);
                    }
                })
                ->make(true);
        }

        return view('settings.currencies', compact('users'));
    }

    // Store new currency
    public function store(Request $request)
    {
        $request->validate([
            'currencie_name' => 'required|string|max:50|unique:currencies,currencie_name',
            'currencie_value' => 'required|integer',
        ]);

        $currency = new Currency();
        $currency->currencie_name = $request->input('currencie_name');
        $currency->currencie_value = $request->input('currencie_value');
        $currency->created_by = auth()->user()->id;
        $currency->updated_by = auth()->user()->id;
        $currency->save();

        return response()->json(['success' => true]);
    }

    // Update existing currency
    public function update(Request $request, $id)
    {
        $currency = Currency::findOrFail($id);

        $request->validate([
            'currencie_name' => 'required|string|max:50|unique:currencies,currencie_name,' . $id,
            'currencie_value' => 'required|integer',
        ]);

        $currency->currencie_name = $request->input('currencie_name');
        $currency->currencie_value = $request->input('currencie_value');
        $currency->updated_by = auth()->user()->id;
        $currency->save();

        return response()->json(['success' => true]);
    }

    // Export currencies data to Excel
    public function export(Request $request)
    {
        $currenciesQuery = Currency::with(['createdByUser:id,name', 'updatedByUser:id,name']);

        if ($request->has('currencie_name') && $request->currencie_name != '') {
            $currenciesQuery->where('currencie_name', 'like', "%" . $request->currencie_name . "%");
        }
        if ($request->has('created_by') && $request->created_by != '') {
            $currenciesQuery->where('created_by', $request->created_by);
        }
        if ($request->has('updated_by') && $request->updated_by != '') {
            $currenciesQuery->where('updated_by', $request->updated_by);
        }
        if ($request->has('created_at') && $request->created_at != '') {
            $currenciesQuery->whereDate('created_at', $request->created_at);
        }
        if ($request->has('updated_at') && $request->updated_at != '') {
            $currenciesQuery->whereDate('updated_at', $request->updated_at);
        }

        $currencies = $currenciesQuery->get();
        return Excel::download(new CurrenciesExport($currencies), 'currencies.xlsx');
    }

    // Edit currency (fetch details)
    public function edit($id)
    {
        $currency = Currency::findOrFail($id);
        return response()->json($currency);
    }

    // Delete currency
    public function destroy($id)
    {
        $currency = Currency::findOrFail($id);
        $currency->delete();

        return response()->json(['success' => true]);
    }
}
