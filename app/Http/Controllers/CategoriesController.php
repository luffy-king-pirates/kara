<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CategoriesExport;

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
    public function export(Request $request)
    {
        $categoriesQuery = Categories::with(['createdByUser:id,name', 'updatedByUser:id,name']);

        if ($request->has('categorie_name') && $request->categorie_name != '') {
            $categoriesQuery->where('categorie_name', 'like', "%" . $request->categorie_name . "%");
        }
        if ($request->has('created_by') && $request->created_by != '') {
            $categoriesQuery->where('created_by', $request->created_by);
        }
        if ($request->has('updated_by') && $request->updated_by != '') {
            $categoriesQuery->where('updated_by', $request->updated_by);
        }
        if ($request->has('created_at') && $request->created_at != '') {
            $categoriesQuery->whereDate('created_at', $request->created_at);
        }
        if ($request->has('updated_at') && $request->updated_at != '') {
            $categoriesQuery->whereDate('updated_at', $request->updated_at);
        }

        $categories = $categoriesQuery->get();
        return Excel::download(new CategoriesExport($categories), 'categories.xlsx');
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
