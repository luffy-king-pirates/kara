<?php

namespace App\Http\Controllers;

use App\Models\Suppliers;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SuppliersExport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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
                    $query->where('is_deleted', false);
                })
                ->make(true);
        }


        return view('settings.suppliers', [
            'users' => $users,
            'canEditSupplier' => auth()->user()->can('update-supplier'),
            'canDeleteSupplier' => auth()->user()->can('delete-supplier')
        ]);
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
        // Query and apply filters manually using conditional where clauses
        $suppliers = Suppliers::query()
            ->with(['createdByUser', 'updatedByUser']) // Eager load relationships
            ->when($request->supplier_name, function ($query, $supplier_name) {
                return $query->where('supplier_name', 'like', '%' . $supplier_name . '%');
            })
            ->when($request->supplier_location, function ($query, $supplier_location) {
                return $query->where('supplier_location', 'like', '%' . $supplier_location . '%');
            })
            ->when($request->supplier_contact, function ($query, $supplier_contact) {
                return $query->where('supplier_contact', 'like', '%' . $supplier_contact . '%');
            })
            ->when($request->supplier_reference, function ($query, $supplier_reference) {
                return $query->where('supplier_reference', 'like', '%' . $supplier_reference . '%');
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
                    $q->where('supplier_name', 'like', "%$searchValue%")
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
        $sheet->setCellValue('B1', 'Supplier Name');
        $sheet->setCellValue('C1', 'Location');
        $sheet->setCellValue('D1', 'Contact');
        $sheet->setCellValue('E1', 'Reference');
        $sheet->setCellValue('F1', 'Created At');
        $sheet->setCellValue('G1', 'Updated At');
        $sheet->setCellValue('H1', 'Created By');
        $sheet->setCellValue('I1', 'Updated By');

        // Insert data from the filtered suppliers model
        $row = 2; // Starting from row 2 as row 1 has headers
        foreach ($suppliers as $supplier) {
            $sheet->setCellValue('A' . $row, $supplier->id);
            $sheet->setCellValue('B' . $row, $supplier->supplier_name);
            $sheet->setCellValue('C' . $row, $supplier->supplier_location);
            $sheet->setCellValue('D' . $row, $supplier->supplier_contact);
            $sheet->setCellValue('E' . $row, $supplier->supplier_reference);

            // Format dates for Created At and Updated At
            $sheet->setCellValue('F' . $row, $supplier->created_at ? Carbon::parse($supplier->created_at)->format('M d, Y h:i A') : 'N/A');
            $sheet->setCellValue('G' . $row, $supplier->updated_at ? Carbon::parse($supplier->updated_at)->format('M d, Y h:i A') : 'Not updated');

            // Add user information for Created By and Updated By
            $sheet->setCellValue('H' . $row, $supplier->createdByUser ? $supplier->createdByUser->name : 'Unknown');
            $sheet->setCellValue('I' . $row, $supplier->updatedByUser ? $supplier->updatedByUser->name : 'Not updated');

            $row++;
        }

        // Write the spreadsheet to a file (in memory)
        $writer = new Xlsx($spreadsheet);
        $fileName = 'suppliers.xlsx';

        // Prepare the response for download
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="suppliers.xlsx"',
        ]);
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
        $supplier->is_deleted = true; // Set is_deleted to true
        $supplier->save(); // Save the change to the database

        return response()->json(['success' => true]);
    }
}
