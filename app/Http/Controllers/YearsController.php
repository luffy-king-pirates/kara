<?php


namespace App\Http\Controllers;

use App\Models\Years; // Change this to your actual model for years
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\YearsExport; // Update the export class

class YearsController extends Controller
{
    // Show the Years view
    public function index(Request $request)
    {
        $users = User::all();
        if ($request->ajax()) {
            $years = Years::with(['createdByUser:id,name', 'updatedByUser:id,name'])
                ->select(['id', 'year_name', 'created_at', 'updated_at', 'created_by', 'updated_by']);

            return DataTables::of($years)
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
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-primary edit-year">Edit</a>
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-danger delete-year">Delete</a>
                    ';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search['value'] != '') {
                        $searchValue = $request->search['value'];
                        $query->where(function($q) use ($searchValue) {
                            $q->where('year_name', 'like', "%$searchValue%")
                              ->orWhereHas('createdByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              })
                              ->orWhereHas('updatedByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              });
                        });
                    }

                    if ($request->has('year_name') && $request->year_name != '') {
                        $query->where('year_name', 'like', "%" . $request->year_name . "%");
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

        return view('settings.years', compact('users')); // Update to reflect years view
    }

    // Store new year
    public function store(Request $request)
    {
        $request->validate([
            'year_name' => 'required|integer|unique:years,year_name', // Change the validation rule
        ]);

        $year = new Years(); // Update to your actual model
        $year->year_name = $request->input('year_name');
        $year->created_by = auth()->user()->id;
        $year->updated_by = auth()->user()->id;
        $year->save();

        return response()->json(['success' => true]);
    }

    // Update existing year
    public function update(Request $request, $id)
    {
        $year = Years::findOrFail($id); // Update to your actual model

        $request->validate([
            'year_name' => 'required|integer|unique:years,year_name,' . $id, // Change the validation rule
        ]);

        $year->year_name = $request->input('year_name');
        $year->updated_by = auth()->user()->id;
        $year->save();

        return response()->json(['success' => true]);
    }

    // Export years data to Excel
    public function export(Request $request)
    {
        $yearsQuery = Years::with(['createdByUser:id,name', 'updatedByUser:id,name']); // Update to your actual model

        if ($request->has('year_name') && $request->year_name != '') {
            $yearsQuery->where('year_name', 'like', "%" . $request->year_name . "%");
        }
        if ($request->has('created_by') && $request->created_by != '') {
            $yearsQuery->where('created_by', $request->created_by);
        }
        if ($request->has('updated_by') && $request->updated_by != '') {
            $yearsQuery->where('updated_by', $request->updated_by);
        }
        if ($request->has('created_at') && $request->created_at != '') {
            $yearsQuery->whereDate('created_at', $request->created_at);
        }
        if ($request->has('updated_at') && $request->updated_at != '') {
            $yearsQuery->whereDate('updated_at', $request->updated_at);
        }

        $years = $yearsQuery->get();
        return Excel::download(new YearsExport($years), 'years.xlsx'); // Update the export class
    }

    // Edit year (fetch details)
    public function edit($id)
    {
        $year = Years::findOrFail($id); // Update to your actual model
        return response()->json($year);
    }

    // Delete year
    public function destroy($id)
    {
        $year = Years::findOrFail($id); // Update to your actual model
        $year->delete();

        return response()->json(['success' => true]);
    }
}
