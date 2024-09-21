<?php

namespace App\Http\Controllers;

use App\Models\Month; // Update this to your actual model for months
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MonthsExport; // Update the export class

class MonthsController extends Controller
{
    // Show the Months view
    public function index(Request $request)
    {
        $users = User::all();
        if ($request->ajax()) {
            $months = Month::with(['createdByUser:id,name', 'updatedByUser:id,name'])
                ->select(['id', 'month_name', 'created_at', 'updated_at', 'created_by', 'updated_by']);

            return DataTables::of($months)
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
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-primary edit-month">Edit</a>
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-danger delete-month">Delete</a>
                    ';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search['value'] != '') {
                        $searchValue = $request->search['value'];
                        $query->where(function($q) use ($searchValue) {
                            $q->where('month_name', 'like', "%$searchValue%")
                              ->orWhereHas('createdByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              })
                              ->orWhereHas('updatedByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              });
                        });
                    }

                    if ($request->has('month_name') && $request->month_name != '') {
                        $query->where('month_name', 'like', "%" . $request->month_name . "%");
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

        return view('settings.months', compact('users')); // Update to reflect months view
    }

    // Store new month
    public function store(Request $request)
    {
        $request->validate([
            'month_name' => 'required|string|unique:months,month_name', // Change the validation rule
        ]);

        $month = new Month(); // Update to your actual model
        $month->month_name = $request->input('month_name');
        $month->created_by = auth()->user()->id;
        $month->updated_by = auth()->user()->id;
        $month->save();

        return response()->json(['success' => true]);
    }

    // Update existing month
    public function update(Request $request, $id)
    {
        $month = Month::findOrFail($id); // Update to your actual model

        $request->validate([
            'month_name' => 'required|string|unique:months,month_name,' . $id, // Change the validation rule
        ]);

        $month->month_name = $request->input('month_name');
        $month->updated_by = auth()->user()->id;
        $month->save();

        return response()->json(['success' => true]);
    }

    // Export months data to Excel
    public function export(Request $request)
    {
        $monthsQuery = Month::with(['createdByUser:id,name', 'updatedByUser:id,name']); // Update to your actual model

        if ($request->has('month_name') && $request->month_name != '') {
            $monthsQuery->where('month_name', 'like', "%" . $request->month_name . "%");
        }
        if ($request->has('created_by') && $request->created_by != '') {
            $monthsQuery->where('created_by', $request->created_by);
        }
        if ($request->has('updated_by') && $request->updated_by != '') {
            $monthsQuery->where('updated_by', $request->updated_by);
        }
        if ($request->has('created_at') && $request->created_at != '') {
            $monthsQuery->whereDate('created_at', $request->created_at);
        }
        if ($request->has('updated_at') && $request->updated_at != '') {
            $monthsQuery->whereDate('updated_at', $request->updated_at);
        }

        $months = $monthsQuery->get();
        return Excel::download(new MonthsExport($months), 'months.xlsx'); // Update the export class
    }

    // Edit month (fetch details)
    public function edit($id)
    {
        $month = Month::findOrFail($id); // Update to your actual model
        return response()->json($month);
    }

    // Delete month
    public function destroy($id)
    {
        $month = Month::findOrFail($id); // Update to your actual model
        $month->delete();

        return response()->json(['success' => true]);
    }
}
