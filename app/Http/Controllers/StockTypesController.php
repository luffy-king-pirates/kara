<?php

namespace App\Http\Controllers;

use App\Models\StockTypes; // Assuming you've renamed the model accordingly
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockTypesExport; // Assuming you've updated the export class

class StockTypesController extends Controller
{
    // Show the Stock Types view
    public function index(Request $request)
    {
        $users = User::all();
        if ($request->ajax()) {
            $stockTypes = StockTypes::with(['createdByUser:id,name', 'updatedByUser:id,name'])
                ->select(['id', 'stock_type_name', 'created_at', 'updated_at', 'created_by', 'updated_by']);

            return DataTables::of($stockTypes)
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
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-primary edit-stock-type">Edit</a>
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-danger delete-stock-type">Delete</a>
                    ';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search['value'] != '') {
                        $searchValue = $request->search['value'];
                        $query->where(function($q) use ($searchValue) {
                            $q->where('stock_type_name', 'like', "%$searchValue%")
                              ->orWhereHas('createdByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              })
                              ->orWhereHas('updatedByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              });
                        });
                    }

                    if ($request->has('stock_type_name') && $request->stock_type_name != '') {
                        $query->where('stock_type_name', 'like', "%" . $request->stock_type_name . "%");
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

        return view('settings.type', compact('users'));
    }

    // Store new stock type
    public function store(Request $request)
    {
        $request->validate([
            'stock_type_name' => 'required|string|max:50|unique:stock_types,stock_type_name',
        ]);

        $stockType = new StockTypes();
        $stockType->stock_type_name = $request->input('stock_type_name');
        $stockType->created_by = auth()->user()->id;
        $stockType->updated_by = auth()->user()->id;
        $stockType->save();

        return response()->json(['success' => true]);
    }

    // Update existing stock type
    public function update(Request $request, $id)
    {
        $stockType = StockTypes::findOrFail($id);

        $request->validate([
            'stock_type_name' => 'required|string|max:50|unique:stock_types,stock_type_name,' . $id,
        ]);

        $stockType->stock_type_name = $request->input('stock_type_name');
        $stockType->updated_by = auth()->user()->id;
        $stockType->save();

        return response()->json(['success' => true]);
    }

    // Export stock types data to Excel
    public function export(Request $request)
    {
        $stockTypesQuery = StockTypes::with(['createdByUser:id,name', 'updatedByUser:id,name']);

        if ($request->has('stock_type_name') && $request->stock_type_name != '') {
            $stockTypesQuery->where('stock_type_name', 'like', "%" . $request->stock_type_name . "%");
        }
        if ($request->has('created_by') && $request->created_by != '') {
            $stockTypesQuery->where('created_by', $request->created_by);
        }
        if ($request->has('updated_by') && $request->updated_by != '') {
            $stockTypesQuery->where('updated_by', $request->updated_by);
        }
        if ($request->has('created_at') && $request->created_at != '') {
            $stockTypesQuery->whereDate('created_at', $request->created_at);
        }
        if ($request->has('updated_at') && $request->updated_at != '') {
            $stockTypesQuery->whereDate('updated_at', $request->updated_at);
        }

        $stockTypes = $stockTypesQuery->get();
        return Excel::download(new StockTypesExport($stockTypes), 'stock_types.xlsx');
    }

    // Edit stock type (fetch details)
    public function edit($id)
    {
        $stockType = StockTypes::findOrFail($id);
        return response()->json($stockType);
    }

    // Delete stock type
    public function destroy($id)
    {
        $stockType = StockTypes::findOrFail($id);
        $stockType->delete();

        return response()->json(['success' => true]);
    }
}
