<?php

namespace App\Http\Controllers;

use App\Models\Brand; // Update this to your actual model for brands
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BrandsExport; // Update the export class

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
        $brandsQuery = Brand::with(['createdByUser:id,name', 'updatedByUser:id,name']); // Update to your actual model

        if ($request->has('brand_name') && $request->brand_name != '') {
            $brandsQuery->where('brand_name', 'like', "%" . $request->brand_name . "%");
        }
        if ($request->has('created_by') && $request->created_by != '') {
            $brandsQuery->where('created_by', $request->created_by);
        }
        if ($request->has('updated_by') && $request->updated_by != '') {
            $brandsQuery->where('updated_by', $request->updated_by);
        }
        if ($request->has('created_at') && $request->created_at != '') {
            $brandsQuery->whereDate('created_at', $request->created_at);
        }
        if ($request->has('updated_at') && $request->updated_at != '') {
            $brandsQuery->whereDate('updated_at', $request->updated_at);
        }

        $brands = $brandsQuery->get();
        return Excel::download(new BrandsExport($brands), 'brands.xlsx'); // Update the export class
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
