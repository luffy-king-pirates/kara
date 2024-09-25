<?php

namespace App\Http\Controllers;

use App\Models\Month; // Update this to your actual model for months
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MonthsExport; // Update the export class
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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

        return view('settings.months', [
            'users' => $users,
            'canEditMonth' => auth()->user()->can('update-month'),
            'canDeleteMonth' => auth()->user()->can('delete-month')
        ]);
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
        // Query and apply filters manually using conditional where clauses
        $months = Month::query()
            ->with(['createdByUser', 'updatedByUser']) // Eager load relationships
            ->when($request->search['value'] ?? null, function ($query, $searchValue) {
                return $query->where(function($q) use ($searchValue) {
                    $q->where('month_name', 'like', "%$searchValue%")
                      ->orWhereHas('createdByUser', function($q) use ($searchValue) {
                          $q->where('name', 'like', "%$searchValue%");
                      })
                      ->orWhereHas('updatedByUser', function($q) use ($searchValue) {
                          $q->where('name', 'like', "%$searchValue%");
                      });
                });
            })
            ->when($request->month_name, function ($query, $month_name) {
                return $query->where('month_name', 'like', '%' . $month_name . '%');
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

        // Check if no results found
        if ($months->isEmpty()) {
            return response()->streamDownload(function() {
                echo "No data available for export.";
            }, 'months.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
                'Content-Disposition' => 'attachment; filename="months.xlsx"',
            ]);
        }

        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers for the Excel columns
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Month Name');
        $sheet->setCellValue('C1', 'Created At');
        $sheet->setCellValue('D1', 'Updated At');
        $sheet->setCellValue('E1', 'Created By');
        $sheet->setCellValue('F1', 'Updated By');

        // Insert data from the filtered months model
        $row = 2; // Starting from row 2 as row 1 has headers
        foreach ($months as $month) {
            $sheet->setCellValue('A' . $row, $month->id);
            $sheet->setCellValue('B' . $row, $month->month_name);
            $sheet->setCellValue('C' . $row, $month->created_at ? Carbon::parse($month->created_at)->format('M d, Y h:i A') : 'N/A');
            $sheet->setCellValue('D' . $row, $month->updated_at ? Carbon::parse($month->updated_at)->format('M d, Y h:i A') : 'Not updated');
            $sheet->setCellValue('E' . $row, $month->createdByUser ? $month->createdByUser->name : 'Unknown');
            $sheet->setCellValue('F' . $row, $month->updatedByUser ? $month->updatedByUser->name : 'Not updated');
            $row++;
        }

        // Write the spreadsheet to a file (in memory)
        $writer = new Xlsx($spreadsheet);
        $fileName = 'months_' . now()->format('Y-m-d') . '.xlsx'; // Custom file name

        // Prepare the response for download
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
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
