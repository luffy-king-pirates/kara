<?php


namespace App\Http\Controllers;

use App\Models\Years; // Change this to your actual model for years
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\YearsExport; // Update the export class
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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

        return view('settings.years', [
            'users' => $users,
            'canEditYear' => auth()->user()->can('update-year'),
            'canDeleteYear' => auth()->user()->can('delete-year')
        ]);
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
        // Query and apply filters manually using conditional where clauses
        $years = Years::query()
            ->with(['createdByUser', 'updatedByUser']) // Eager load relationships
            ->when($request->year_name, function ($query, $year_name) {
                return $query->where('year_name', 'like', '%' . $year_name . '%');
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
        $sheet->setCellValue('B1', 'Year Name');
        $sheet->setCellValue('C1', 'Created At');
        $sheet->setCellValue('D1', 'Updated At');
        $sheet->setCellValue('E1', 'Created By');
        $sheet->setCellValue('F1', 'Updated By');

        // Insert data from the filtered Years model
        $row = 2; // Starting from row 2 as row 1 has headers
        foreach ($years as $year) {
            $sheet->setCellValue('A' . $row, $year->id);
            $sheet->setCellValue('B' . $row, $year->year_name);

            // Format dates for Created At and Updated At
            $sheet->setCellValue('C' . $row, $year->created_at ? Carbon::parse($year->created_at)->format('M d, Y h:i A') : 'N/A');
            $sheet->setCellValue('D' . $row, $year->updated_at ? Carbon::parse($year->updated_at)->format('M d, Y h:i A') : 'Not updated');

            // Add user information for Created By and Updated By
            $sheet->setCellValue('E' . $row, $year->createdByUser ? $year->createdByUser->name : 'Unknown');
            $sheet->setCellValue('F' . $row, $year->updatedByUser ? $year->updatedByUser->name : 'Not updated');

            $row++;
        }

        // Write the spreadsheet to a file (in memory)
        $writer = new Xlsx($spreadsheet);
        $fileName = 'years.xlsx';

        // Prepare the response for download
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="years.xlsx"',
        ]);
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
