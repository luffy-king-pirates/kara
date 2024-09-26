<?php

namespace App\Http\Controllers;

use App\Models\Units;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UnitsExport;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class UnitsController extends Controller
{
    // Show the Units view
    public function index(Request $request)
    {
        $users = User::all();
        Log::info($request->ajax());
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
                    $query->where('is_deleted', false);
                })
                ->make(true);
        }


        return view('settings.units', [
            'users' => $users,
            'canEditUnit' => auth()->user()->can('update-unit'),
            'canDeleteUnit' => auth()->user()->can('delete-unit')
        ]);
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
    // public function export(Request $request)
    // {
    //     $unitsQuery = Units::with(['createdByUser:id,name', 'updatedByUser:id,name']);

    //     if ($request->has('unit_name') && $request->unit_name != '') {
    //         $unitsQuery->where('unit_name', 'like', "%" . $request->unit_name . "%");
    //     }
    //     if ($request->has('created_by') && $request->created_by != '') {
    //         $unitsQuery->where('created_by', $request->created_by);
    //     }
    //     if ($request->has('updated_by') && $request->updated_by != '') {
    //         $unitsQuery->where('updated_by', $request->updated_by);
    //     }
    //     if ($request->has('created_at') && $request->created_at != '') {
    //         $unitsQuery->whereDate('created_at', $request->created_at);
    //     }
    //     if ($request->has('updated_at') && $request->updated_at != '') {
    //         $unitsQuery->whereDate('updated_at', $request->updated_at);
    //     }

    //     $units = $unitsQuery->get();
    //     return Excel::download(new UnitsExport($units), 'units.xlsx');
    // }

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
        $unit->is_deleted = true; // Set is_deleted to true
        $unit->save(); // Save the change to the database

        return response()->json(['success' => true]);
    }

    public function export(Request $request)
    {
        // Query and apply filters manually using conditional where clauses
        $units = Units::query()
            ->with(['createdByUser', 'updatedByUser']) // Eager load relationships
            ->when($request->unit_name, function ($query, $unit_name) {
                return $query->where('unit_name', 'like', '%' . $unit_name . '%');
            })
            ->when($request->created_at, function ($query, $created_at) {
                return $query->whereDate('created_at', $created_at);
            })
            ->when($request->updated_at, function ($query, $updated_at) {
                return $query->whereDate('updated_at', $updated_at);
            })
            ->when($request->created_by, function ($query, $created_by) {
                return $query->where('created_by', $created_by);
            })
            ->when($request->updated_by, function ($query, $updated_by) {
                return $query->where('updated_by', $updated_by);
            })
            ->get();

        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers for the Excel columns
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Unit Name');
        $sheet->setCellValue('C1', 'Created At');
        $sheet->setCellValue('D1', 'Updated At');
        $sheet->setCellValue('E1', 'Created By');
        $sheet->setCellValue('F1', 'Updated By');

        // Insert data from the filtered Units model
        $row = 2; // Starting from row 2 as row 1 has headers
        foreach ($units as $unit) {
            $sheet->setCellValue('A' . $row, $unit->id);
            $sheet->setCellValue('B' . $row, $unit->unit_name);

            // Format dates for Created At and Updated At
            $sheet->setCellValue('C' . $row, $unit->created_at ? Carbon::parse($unit->created_at)->format('M d, Y h:i A') : 'N/A');
            $sheet->setCellValue('D' . $row, $unit->updated_at ? Carbon::parse($unit->updated_at)->format('M d, Y h:i A') : 'Not updated');

            // Add user information for Created By and Updated By
            $sheet->setCellValue('E' . $row, $unit->createdByUser ? $unit->createdByUser->name : 'Unknown');
            $sheet->setCellValue('F' . $row, $unit->updatedByUser ? $unit->updatedByUser->name : 'Not updated');

            $row++;
        }

        // Write the spreadsheet to a file (in memory)
        $writer = new Xlsx($spreadsheet);
        $fileName = 'units.xlsx';

        // Prepare the response for download
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="units.xlsx"',
        ]);
    }


}
