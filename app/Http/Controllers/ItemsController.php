<?php

namespace App\Http\Controllers;

use App\Models\Item; // Your item model
use App\Models\User;
use App\Models\categories;
use App\Models\Brand;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class ItemsController extends Controller
{
    // Show the Items view
    public function index(Request $request)
    {
        $users = User::all();
        $categories = categories::all();
        $brands = Brand::all();

        if ($request->ajax()) {
            $items = Item::with(['category:id,categorie_name', 'brand:id,brand_name',

           'createdByUser:id,name', 'updatedByUser:id,name'])
                ->select(['id', 'item_code', 'item_name', 'item_category', 'item_brand', 'item_size', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_active'])
                ->where('is_deleted', false);

            return DataTables::of($items)
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
                ->addColumn('category', function ($row) {
                    return $row->category ? $row->category->categorie_name : 'Unknown';
                })
                ->addColumn('brand', function ($row) {
                    return $row->brand ? $row->brand->brand_name : 'Unknown';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-primary edit-btn">Edit</a>
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-danger delete-btn">Delete</a>
                    ';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search['value'] != '') {
                        $searchValue = $request->search['value'];
                        $query->where(function($q) use ($searchValue) {
                            $q->where('item_name', 'like', "%$searchValue%")
                              ->orWhere('item_code', 'like', "%$searchValue%")
                              ->orWhereHas('creator', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              })
                              ->orWhereHas('updater', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              });
                        });
                    }

                    if ($request->has('item_name') && $request->item_name != '') {
                        $query->where('item_name', 'like', "%" . $request->item_name . "%");
                    }
                    if ($request->has('item_code') && $request->item_code != '') {
                        $query->where('item_code', 'like', "%" . $request->item_code . "%");
                    }
                    if ($request->has('item_category') && $request->item_category != '') {
                        $query->where('item_category', $request->item_category);
                    }
                    if ($request->has('item_brand') && $request->item_brand != '') {
                        $query->where('item_brand', $request->item_brand);
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
                    $query->where('is_active', true);
                })
                ->make(true);
        }

        return view('items.item', [
            'users' => $users,
            'categories' => $categories,
            'brands' => $brands,
            'canEditItem' => auth()->user()->can('update-items'),
            'canDeleteItem' => auth()->user()->can('delete-items')
        ]);
    }
// Export items data to Excel
public function export(Request $request)
{
    // Query and apply filters manually using conditional where clauses
    $items = Item::query()
        ->with(['category', 'brand', 'createdByUser', 'updatedByUser']) // Eager load relationships
        ->when($request->search['value'] ?? null, function ($query, $searchValue) {
            return $query->where(function ($q) use ($searchValue) {
                $q->where('item_code', 'like', "%$searchValue%")
                  ->orWhere('item_name', 'like', "%$searchValue%")
                  ->orWhereHas('createdByUser', function ($q) use ($searchValue) {
                      $q->where('name', 'like', "%$searchValue%");
                  })
                  ->orWhereHas('updatedByUser', function ($q) use ($searchValue) {
                      $q->where('name', 'like', "%$searchValue%");
                  });
            });
        })
        ->when($request->item_code, function ($query, $item_code) {
            return $query->where('item_code', 'like', '%' . $item_code . '%');
        })
        ->when($request->item_name, function ($query, $item_name) {
            return $query->where('item_name', 'like', '%' . $item_name . '%');
        })
        ->when($request->category_id, function ($query, $category_id) {
            return $query->where('category_id', $category_id);
        })
        ->when($request->brand_id, function ($query, $brand_id) {
            return $query->where('brand_id', $brand_id);
        })
        ->when($request->item_size, function ($query, $item_size) {
            return $query->where('item_size', 'like', '%' . $item_size . '%');
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
    if ($items->isEmpty()) {
        return response()->streamDownload(function() {
            echo "No data available for export.";
        }, 'items.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="items.xlsx"',
        ]);
    }

    // Create a new Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set headers for the Excel columns
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Item Code');
    $sheet->setCellValue('C1', 'Item Name');
    $sheet->setCellValue('D1', 'Size');
    $sheet->setCellValue('E1', 'Category');
    $sheet->setCellValue('F1', 'Brand');
    $sheet->setCellValue('G1', 'Created At');
    $sheet->setCellValue('H1', 'Updated At');
    $sheet->setCellValue('I1', 'Created By');
    $sheet->setCellValue('J1', 'Updated By');

    // Insert data from the filtered items model
    $row = 2; // Starting from row 2 as row 1 has headers
    foreach ($items as $item) {
        $sheet->setCellValue('A' . $row, $item->id);
        $sheet->setCellValue('B' . $row, $item->item_code);
        $sheet->setCellValue('C' . $row, $item->item_name);
        $sheet->setCellValue('D' . $row, $item->item_size);
        $sheet->setCellValue('E' . $row, $item->category->category_name ?? 'N/A');
        $sheet->setCellValue('F' . $row, $item->brand->brand_name ?? 'N/A');
        $sheet->setCellValue('G' . $row, $item->created_at ? Carbon::parse($item->created_at)->format('M d, Y h:i A') : 'N/A');
        $sheet->setCellValue('H' . $row, $item->updated_at ? Carbon::parse($item->updated_at)->format('M d, Y h:i A') : 'Not updated');
        $sheet->setCellValue('I' . $row, $item->createdByUser ? $item->createdByUser->name : 'Unknown');
        $sheet->setCellValue('J' . $row, $item->updatedByUser ? $item->updatedByUser->name : 'Not updated');
        $row++;
    }

    // Write the spreadsheet to a file (in memory)
    $writer = new Xlsx($spreadsheet);
    $fileName = 'items_' . now()->format('Y-m-d') . '.xlsx'; // Custom file name

    // Prepare the response for download
    return response()->streamDownload(function() use ($writer) {
        $writer->save('php://output');
    }, $fileName, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Cache-Control' => 'max-age=0',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
    ]);
}

    // Store new item
    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|unique:items,item_name',
            'item_code' => 'nullable|string|max:255|unique:items,item_code',
            'item_category' => 'required|exists:categories,id',
            'item_brand' => 'required|exists:brands,id',
            'item_size' => 'nullable|string|max:255',
        ]);

        $item = new Item();
        $item->item_code = $request->input('item_code');
        $item->item_name = $request->input('item_name');
        $item->item_category = $request->input('item_category');
        $item->item_brand = $request->input('item_brand');
        $item->item_size = $request->input('item_size');
        $item->created_by = auth()->user()->id;
        $item->updated_by = auth()->user()->id;
        $item->is_active = true;
        $item->is_deleted = false;
        $item->save();

        return response()->json(['success' => true]);
    }

    // Update existing item
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $request->validate([
            'item_name' => 'required|string|unique:items,item_name,' . $id,
            'item_code' => 'nullable|string|max:255|unique:items,item_code,' . $id,
            'item_category' => 'required|exists:item_groups,id',
            'item_brand' => 'required|exists:brands,id',
            'item_size' => 'nullable|string|max:255',
        ]);

        $item->item_code = $request->input('item_code');
        $item->item_name = $request->input('item_name');
        $item->item_category = $request->input('item_category');
        $item->item_brand = $request->input('item_brand');
        $item->item_size = $request->input('item_size');
        $item->updated_by = auth()->user()->id;
        $item->save();

        return response()->json(['success' => true]);
    }

    // Delete item (soft delete)
    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $item->is_deleted = true;
        $item->save();

        return response()->json(['success' => true]);
    }

    // Edit item (fetch details)
    public function edit($id)
    {
        $item = Item::findOrFail($id);
        return response()->json($item);
    }
}
