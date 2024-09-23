<?php

namespace App\Http\Controllers;

use App\Models\Brand; // Update this to your actual model for brands
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BrandsExport; // Update the export class
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class BrandsController extends Controller
{
    // Show the Brands view
    public function index(Request $request)
    {
        $users = User::all();
        if ($request->ajax()) {
            $brands = Brand::with(['createdByUser:id,name', 'updatedByUser:id,name'])
                ->select(['id', 'brand_name', 'created_at', 'updated_at', 'created_by', 'updated_by']);

            return DataTables::of($brands)
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
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-primary edit-brand">Edit</a>
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-danger delete-brand">Delete</a>
                    ';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search['value'] != '') {
                        $searchValue = $request->search['value'];
                        $query->where(function($q) use ($searchValue) {
                            $q->where('brand_name', 'like', "%$searchValue%")
                              ->orWhereHas('createdByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              })
                              ->orWhereHas('updatedByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              });
                        });
                    }

                    if ($request->has('brand_name') && $request->brand_name != '') {
                        $query->where('brand_name', 'like', "%" . $request->brand_name . "%");
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

        return view('settings.brands', compact('users')); // Update to reflect brands view
    }

    // Store new brand
    public function store(Request $request)
    {
        $request->validate([
            'brand_name' => 'required|string|unique:brands,brand_name', // Change the validation rule
        ]);

        $brand = new Brand(); // Update to your actual model
        $brand->brand_name = $request->input('brand_name');
        $brand->created_by = auth()->user()->id;
        $brand->updated_by = auth()->user()->id;
        $brand->save();

        return response()->json(['success' => true]);
    }

    // Update existing brand
    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id); // Update to your actual model

        $request->validate([
            'brand_name' => 'required|string|unique:brands,brand_name,' . $id, // Change the validation rule
        ]);

        $brand->brand_name = $request->input('brand_name');
        $brand->updated_by = auth()->user()->id;
        $brand->save();

        return response()->json(['success' => true]);
    }

    // Export brands data to Excel

public function export(Request $request)
{
    // Query and apply filters manually using conditional where clauses
    $brands = Brand::query()
        ->with(['createdByUser', 'updatedByUser']) // Eager load relationships
        ->when($request->brand_name, function ($query, $brand_name) {
            return $query->where('brand_name', 'like', '%' . $brand_name . '%');
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
                $q->where('brand_name', 'like', "%$searchValue%")
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
    $sheet->setCellValue('B1', 'Brand Name');
    $sheet->setCellValue('C1', 'Created At');
    $sheet->setCellValue('D1', 'Updated At');
    $sheet->setCellValue('E1', 'Created By');
    $sheet->setCellValue('F1', 'Updated By');

    // Insert data from the filtered Brand model
    $row = 2; // Starting from row 2 as row 1 has headers
    foreach ($brands as $brand) {
        $sheet->setCellValue('A' . $row, $brand->id);
        $sheet->setCellValue('B' . $row, $brand->brand_name);

        // Format dates for Created At and Updated At
        $sheet->setCellValue('C' . $row, $brand->created_at ? Carbon::parse($brand->created_at)->format('M d, Y h:i A') : 'N/A');
        $sheet->setCellValue('D' . $row, $brand->updated_at ? Carbon::parse($brand->updated_at)->format('M d, Y h:i A') : 'Not updated');

        // Add user information for Created By and Updated By
        $sheet->setCellValue('E' . $row, $brand->createdByUser ? $brand->createdByUser->name : 'Unknown');
        $sheet->setCellValue('F' . $row, $brand->updatedByUser ? $brand->updatedByUser->name : 'Not updated');

        $row++;
    }

    // Write the spreadsheet to a file (in memory)
    $writer = new Xlsx($spreadsheet);
    $fileName = 'brands.xlsx';

    // Prepare the response for download
    return response()->streamDownload(function() use ($writer) {
        $writer->save('php://output');
    }, $fileName, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Cache-Control' => 'max-age=0',
        'Content-Disposition' => 'attachment; filename="brands.xlsx"'
    ]);
}


    // Edit brand (fetch details)
    public function edit($id)
    {
        $brand = Brand::findOrFail($id); // Update to your actual model
        return response()->json($brand);
    }

    // Delete brand
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id); // Update to your actual model
        $brand->delete();

        return response()->json(['success' => true]);
    }
}
