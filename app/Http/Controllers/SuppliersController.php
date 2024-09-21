<?php

namespace App\Http\Controllers;

use App\Models\Suppliers;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SuppliersExport;

class SuppliersController extends Controller
{
    // Show the Suppliers view
    public function index(Request $request)
    {
        $users = User::all();
        if ($request->ajax()) {
            $suppliers = Suppliers::with(['createdByUser:id,name', 'updatedByUser:id,name'])
                ->select(['id', 'supplier_name', 'supplier_location', 'supplier_contact', 'supplier_reference', 'created_at', 'updated_at', 'created_by', 'updated_by']);

            return DataTables::of($suppliers)
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
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-primary edit-supplier">Edit</a>
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-danger delete-supplier">Delete</a>
                    ';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search['value'] != '') {
                        $searchValue = $request->search['value'];
                        $query->where(function($q) use ($searchValue) {
                            $q->where('supplier_name', 'like', "%$searchValue%")
                              ->orWhereHas('createdByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              })
                              ->orWhereHas('updatedByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              });
                        });
                    }

                    if ($request->has('supplier_name') && $request->supplier_name != '') {
                        $query->where('supplier_name', 'like', "%" . $request->supplier_name . "%");
                    }
                    if ($request->has('supplier_location') && $request->supplier_location != '') {
                        $query->where('supplier_location', 'like', "%" . $request->supplier_location . "%");
                    }
                    if ($request->has('supplier_contact') && $request->supplier_contact != '') {
                        $query->where('supplier_contact', 'like', "%" . $request->supplier_contact . "%");
                    }
                    if ($request->has('supplier_reference') && $request->supplier_reference != '') {
                        $query->where('supplier_reference', 'like', "%" . $request->supplier_reference . "%");
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

        return view('settings.suppliers', compact('users'));
    }

    // Store new supplier
    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:50|unique:suppliers,supplier_name',
            'supplier_location' => 'required|string|max:50',
            'supplier_contact' => 'required|string|max:50',
            'supplier_reference' => 'required|string|max:50',
        ]);

        $supplier = new Suppliers();
        $supplier->supplier_name = $request->input('supplier_name');
        $supplier->supplier_location = $request->input('supplier_location');
        $supplier->supplier_contact = $request->input('supplier_contact');
        $supplier->supplier_reference = $request->input('supplier_reference');
        $supplier->created_by = auth()->user()->id;
        $supplier->updated_by = auth()->user()->id;
        $supplier->save();

        return response()->json(['success' => true]);
    }

    // Update existing supplier
    public function update(Request $request, $id)
    {
        $supplier = Suppliers::findOrFail($id);

        $request->validate([
            'supplier_name' => 'required|string|max:50|unique:suppliers,supplier_name,' . $id,
            'supplier_location' => 'required|string|max:50',
            'supplier_contact' => 'required|string|max:50',
            'supplier_reference' => 'required|string|max:50',
        ]);

        $supplier->supplier_name = $request->input('supplier_name');
        $supplier->supplier_location = $request->input('supplier_location');
        $supplier->supplier_contact = $request->input('supplier_contact');
        $supplier->supplier_reference = $request->input('supplier_reference');
        $supplier->updated_by = auth()->user()->id;
        $supplier->save();

        return response()->json(['success' => true]);
    }

    // Export suppliers data to Excel
    public function export(Request $request)
    {
        $suppliersQuery = Suppliers::with(['createdByUser:id,name', 'updatedByUser:id,name']);

        if ($request->has('supplier_name') && $request->supplier_name != '') {
            $suppliersQuery->where('supplier_name', 'like', "%" . $request->supplier_name . "%");
        }
        if ($request->has('supplier_location') && $request->supplier_location != '') {
            $suppliersQuery->where('supplier_location', 'like', "%" . $request->supplier_location . "%");
        }
        if ($request->has('supplier_contact') && $request->supplier_contact != '') {
            $suppliersQuery->where('supplier_contact', 'like', "%" . $request->supplier_contact . "%");
        }
        if ($request->has('supplier_reference') && $request->supplier_reference != '') {
            $suppliersQuery->where('supplier_reference', 'like', "%" . $request->supplier_reference . "%");
        }
        if ($request->has('created_by') && $request->created_by != '') {
            $suppliersQuery->where('created_by', $request->created_by );
        }
        if ($request->has('updated_by') && $request->updated_by != '') {
            $suppliersQuery->where('updated_by', $request->updated_by);
        }
        if ($request->has('created_at') && $request->created_at != '') {
            $suppliersQuery->whereDate('created_at', $request->created_at);
        }
        if ($request->has('updated_at') && $request->updated_at != '') {
            $suppliersQuery->whereDate('updated_at', $request->updated_at);
        }

        $suppliers = $suppliersQuery->get();
        return Excel::download(new SuppliersExport($suppliers), 'suppliers.xlsx');
    }

    // Edit supplier (fetch details)
    public function edit($id)
    {
        $supplier = Suppliers::findOrFail($id);
        return response()->json($supplier);
    }

    // Delete supplier
    public function destroy($id)
    {
        $supplier = Suppliers::findOrFail($id);
        $supplier->delete();

        return response()->json(['success' => true]);
    }
}
