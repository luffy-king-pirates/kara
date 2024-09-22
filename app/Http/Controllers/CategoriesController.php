<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CategoriesExport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class CategoriesController extends Controller
{
    // Show the Categories view
    public function index(Request $request)
    {
        $users = User::all();
        if ($request->ajax()) {
            $categories = Categories::with(['createdByUser:id,name', 'updatedByUser:id,name'])
                ->select(['id', 'categorie_name', 'created_at', 'updated_at', 'created_by', 'updated_by']);

            return DataTables::of($categories)
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
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-primary edit-category">Edit</a>
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-danger delete-category">Delete</a>
                    ';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search['value'] != '') {
                        $searchValue = $request->search['value'];
                        $query->where(function($q) use ($searchValue) {
                            $q->where('categorie_name', 'like', "%$searchValue%")
                              ->orWhereHas('createdByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              })
                              ->orWhereHas('updatedByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              });
                        });
                    }

                    if ($request->has('categorie_name') && $request->categorie_name != '') {
                        $query->where('categorie_name', 'like', "%" . $request->categorie_name . "%");
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

        return view('settings.categories', compact('users'));
    }

    // Store new category
    public function store(Request $request)
    {
        $request->validate([
            'categorie_name' => 'required|string|max:50|unique:categories,categorie_name',
        ]);

        $category = new Categories();
        $category->categorie_name = $request->input('categorie_name');
        $category->created_by = auth()->user()->id;
        $category->updated_by = auth()->user()->id;
        $category->save();

        return response()->json(['success' => true]);
    }

    // Update existing category
    public function update(Request $request, $id)
    {
        $category = Categories::findOrFail($id);

        $request->validate([
            'categorie_name' => 'required|string|max:50|unique:categories,categorie_name,' . $id,
        ]);

        $category->categorie_name = $request->input('categorie_name');
        $category->updated_by = auth()->user()->id;
        $category->save();

        return response()->json(['success' => true]);
    }

    // Export categories data to Excel
   // Export categories data to Excel
public function export(Request $request)
{
    // Query and apply filters manually using conditional where clauses
    $categories = Categories::query()
        ->with(['createdByUser', 'updatedByUser']) // Eager load relationships
        ->when($request->category_name, function ($query, $category_name) {
            return $query->where('category_name', 'like', '%' . $category_name . '%');
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
                $q->where('category_name', 'like', "%$searchValue%")
                  ->orWhereHas('createdByUser', function($q) use ($searchValue) {
                      $q->where('name', 'like', "%$searchValue%");
                  })
                  ->orWhereHas('updatedByUser', function($q) use ($searchValue) {
                      $q->where('name', 'like', "%$searchValue%");
                  });
            });
        })
        ->get();

    // Check if no results found
    if ($categories->isEmpty()) {
        return response()->streamDownload(function() {
            echo "No data available for export.";
        }, 'categories.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="categories.xlsx"',
        ]);
    }

    // Create a new Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set headers for the Excel columns
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Category Name');
    $sheet->setCellValue('C1', 'Created At');
    $sheet->setCellValue('D1', 'Updated At');
    $sheet->setCellValue('E1', 'Created By');
    $sheet->setCellValue('F1', 'Updated By');

    // Insert data from the filtered categories model
    $row = 2; // Starting from row 2 as row 1 has headers
    foreach ($categories as $category) {
        $sheet->setCellValue('A' . $row, $category->id);
        $sheet->setCellValue('B' . $row, $category->category_name);
        $sheet->setCellValue('C' . $row, $category->created_at ? Carbon::parse($category->created_at)->format('M d, Y h:i A') : 'N/A');
        $sheet->setCellValue('D' . $row, $category->updated_at ? Carbon::parse($category->updated_at)->format('M d, Y h:i A') : 'Not updated');
        $sheet->setCellValue('E' . $row, $category->createdByUser ? $category->createdByUser->name : 'Unknown');
        $sheet->setCellValue('F' . $row, $category->updatedByUser ? $category->updatedByUser->name : 'Not updated');

        $row++;
    }

    // Write the spreadsheet to a file (in memory)
    $writer = new Xlsx($spreadsheet);
    $fileName = 'categories_' . now()->format('Y-m-d') . '.xlsx'; // Custom file name

    // Prepare the response for download
    return response()->streamDownload(function() use ($writer) {
        $writer->save('php://output');
    }, $fileName, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Cache-Control' => 'max-age=0',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
    ]);
}


    // Edit category (fetch details)
    public function edit($id)
    {
        $category = Categories::findOrFail($id);
        return response()->json($category);
    }

    // Delete category
    public function destroy($id)
    {
        $category = Categories::findOrFail($id);
        $category->delete();

        return response()->json(['success' => true]);
    }
}
