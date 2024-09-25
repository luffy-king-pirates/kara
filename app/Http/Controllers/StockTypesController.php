<?php

namespace App\Http\Controllers;

use App\Models\StockTypes; // Assuming you've renamed the model accordingly
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockTypesExport; // Assuming you've updated the export class
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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

        return view('settings.type', [
            'users' => $users,
            'canEditStockType' => auth()->user()->can('update-stock-type'),
            'canDeleteStockType' => auth()->user()->can('delete-stock-type')
        ]);

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
// Export stock types data to Excel
public function export(Request $request)
{
    // Query and apply filters manually using conditional where clauses
    $stockTypes = StockTypes::query()
        ->with(['createdByUser', 'updatedByUser']) // Eager load relationships
        ->when($request->stock_type_name, function ($query, $stock_type_name) {
            return $query->where('stock_type_name', 'like', '%' . $stock_type_name . '%');
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
        ->when($request->search['value'] ?? null, function ($query, $searchValue) {
            return $query->where(function($q) use ($searchValue) {
                $q->where('stock_type_name', 'like', "%$searchValue%")
                  ->orWhereHas('createdByUser', function($q) use ($searchValue) {
                      $q->where('name', 'like', "%$searchValue%");
                  })
                  ->orWhereHas('updatedByUser', function($q) use ($searchValue) {
                      $q->where('name', 'like', "%$searchValue%");
                  });
            });
        })
        ->get();

    // Create a new Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set headers for the Excel columns
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Stock Type Name');
    $sheet->setCellValue('C1', 'Created At');
    $sheet->setCellValue('D1', 'Updated At');
    $sheet->setCellValue('E1', 'Created By');
    $sheet->setCellValue('F1', 'Updated By');

    // Insert data from the filtered stock types model
    $row = 2; // Starting from row 2 as row 1 has headers
    foreach ($stockTypes as $stockType) {
        $sheet->setCellValue('A' . $row, $stockType->id);
        $sheet->setCellValue('B' . $row, $stockType->stock_type_name);

        // Format dates for Created At and Updated At
        $sheet->setCellValue('C' . $row, $stockType->created_at ? Carbon::parse($stockType->created_at)->format('M d, Y h:i A') : 'N/A');
        $sheet->setCellValue('D' . $row, $stockType->updated_at ? Carbon::parse($stockType->updated_at)->format('M d, Y h:i A') : 'Not updated');

        // Add user information for Created By and Updated By
        $sheet->setCellValue('E' . $row, $stockType->createdByUser ? $stockType->createdByUser->name : 'Unknown');
        $sheet->setCellValue('F' . $row, $stockType->updatedByUser ? $stockType->updatedByUser->name : 'Not updated');

        $row++;
    }

    // Write the spreadsheet to a file (in memory)
    $writer = new Xlsx($spreadsheet);
    $fileName = 'stock_types.xlsx';

    // Prepare the response for download
    return response()->streamDownload(function() use ($writer) {
        $writer->save('php://output');
    }, $fileName, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Cache-Control' => 'max-age=0',
        'Content-Disposition' => 'attachment; filename="stock_types.xlsx"',
    ]);
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
