<?php

namespace App\Http\Controllers;

use App\Models\Countries;  // Change the model to Countries
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CountriesExport;  // Update the export class if you have one

class ProductCountriesController extends Controller
{
    // Show the Countries view
    public function index(Request $request)
    {
        $users = User::all();
        if ($request->ajax()) {
            $countries = Countries::with(['createdByUser:id,name', 'updatedByUser:id,name'])
                ->select(['id', 'country_name', 'created_at', 'updated_at', 'created_by', 'updated_by']);  // Update field name

            return DataTables::of($countries)
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
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-primary edit-country">Edit</a>
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-danger delete-country">Delete</a>
                    ';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search['value'] != '') {
                        $searchValue = $request->search['value'];
                        $query->where(function($q) use ($searchValue) {
                            $q->where('country_name', 'like', "%$searchValue%")  // Update field name
                              ->orWhereHas('createdByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              })
                              ->orWhereHas('updatedByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              });
                        });
                    }

                    if ($request->has('country_name') && $request->country_name != '') {  // Update field name
                        $query->where('country_name', 'like', "%" . $request->country_name . "%");
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

        return view('settings.countries', compact('users'));  // Update the view file if necessary
    }

    // Store new country
    public function store(Request $request)
    {
        $request->validate([
            'country_name' => 'required|string|max:50|unique:countries,country_name',  // Update field name and table
        ]);

        $country = new Countries();  // Update the model
        $country->country_name = $request->input('country_name');  // Update field name
        $country->created_by = auth()->user()->id;
        $country->updated_by = auth()->user()->id;
        $country->save();

        return response()->json(['success' => true]);
    }

    // Update existing country
    public function update(Request $request, $id)
    {
        $country = Countries::findOrFail($id);  // Update the model

        $request->validate([
            'country_name' => 'required|string|max:50|unique:countries,country_name,' . $id,  // Update field name and table
        ]);

        $country->country_name = $request->input('country_name');  // Update field name
        $country->updated_by = auth()->user()->id;
        $country->save();

        return response()->json(['success' => true]);
    }

    // Export countries data to Excel
    public function export(Request $request)
    {
        $countriesQuery = Countries::with(['createdByUser:id,name', 'updatedByUser:id,name']);  // Update the model

        if ($request->has('country_name') && $request->country_name != '') {  // Update field name
            $countriesQuery->where('country_name', 'like', "%" . $request->country_name . "%");
        }
        if ($request->has('created_by') && $request->created_by != '') {
            $countriesQuery->where('created_by', $request->created_by);
        }
        if ($request->has('updated_by') && $request->updated_by != '') {
            $countriesQuery->where('updated_by', $request->updated_by);
        }
        if ($request->has('created_at') && $request->created_at != '') {
            $countriesQuery->whereDate('created_at', $request->created_at);
        }
        if ($request->has('updated_at') && $request->updated_at != '') {
            $countriesQuery->whereDate('updated_at', $request->updated_at);
        }

        $countries = $countriesQuery->get();
        return Excel::download(new CountriesExport($countries), 'countries.xlsx');  // Update export file if needed
    }

    // Edit country (fetch details)
    public function edit($id)
    {
        $country = Countries::findOrFail($id);  // Update the model
        return response()->json($country);
    }

    // Delete country
    public function destroy($id)
    {
        $country = Countries::findOrFail($id);  // Update the model
        $country->delete();

        return response()->json(['success' => true]);
    }
}
