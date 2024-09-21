<?php

namespace App\Http\Controllers;

use App\Models\Units;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UnitsExport;

class UnitsController extends Controller
{
    // Show the Units view
    public function index(Request $request)
    {
        $users = User::all();
        if ($request->ajax()) {
            $units = Units::with(['createdByUser:id,name', 'updatedByUser:id,name'])
                ->select(['id', 'unit_name', 'created_at', 'updated_at', 'created_by', 'updated_by']);

            return DataTables::of($units)
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
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-primary edit-unit">Edit</a>
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-danger delete-unit">Delete</a>
                    ';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search['value'] != '') {
                        $searchValue = $request->search['value'];
                        $query->where(function($q) use ($searchValue) {
                            $q->where('unit_name', 'like', "%$searchValue%")
                              ->orWhereHas('createdByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              })
                              ->orWhereHas('updatedByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              });
                        });
                    }

                    if ($request->has('unit_name') && $request->unit_name != '') {
                        $query->where('unit_name', 'like', "%" . $request->unit_name . "%");
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

        return view('settings.units', compact('users'));
    }

    // Store new unit
    public function store(Request $request)
    {
        $request->validate([
            'unit_name' => 'required|string|max:50|unique:units,unit_name',
        ]);

        $unit = new Units();
        $unit->unit_name = $request->input('unit_name');
        $unit->created_by = auth()->user()->id;
        $unit->updated_by = auth()->user()->id;
        $unit->save();

        return response()->json(['success' => true]);
    }

    // Update existing unit
    public function update(Request $request, $id)
    {
        $unit = Units::findOrFail($id);

        $request->validate([
            'unit_name' => 'required|string|max:50|unique:units,unit_name,' . $id,
        ]);

        $unit->unit_name = $request->input('unit_name');
        $unit->updated_by = auth()->user()->id;
        $unit->save();

        return response()->json(['success' => true]);
    }

    // Export units data to Excel
    public function export(Request $request)
    {
        $unitsQuery = Units::with(['createdByUser:id,name', 'updatedByUser:id,name']);

        if ($request->has('unit_name') && $request->unit_name != '') {
            $unitsQuery->where('unit_name', 'like', "%" . $request->unit_name . "%");
        }
        if ($request->has('created_by') && $request->created_by != '') {
            $unitsQuery->where('created_by', $request->created_by);
        }
        if ($request->has('updated_by') && $request->updated_by != '') {
            $unitsQuery->where('updated_by', $request->updated_by);
        }
        if ($request->has('created_at') && $request->created_at != '') {
            $unitsQuery->whereDate('created_at', $request->created_at);
        }
        if ($request->has('updated_at') && $request->updated_at != '') {
            $unitsQuery->whereDate('updated_at', $request->updated_at);
        }

        $units = $unitsQuery->get();
        return Excel::download(new UnitsExport($units), 'units.xlsx');
    }

    // Edit unit (fetch details)
    public function edit($id)
    {
        $unit = Units::findOrFail($id);
        return response()->json($unit);
    }

    // Delete unit
    public function destroy($id)
    {
        $unit = Units::findOrFail($id);
        $unit->delete();

        return response()->json(['success' => true]);
    }
}
