<?php

namespace App\Http\Controllers;

use App\Models\Countries;  // Change the model to Countries
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CountriesExport;  // Update the export class if you have one
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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
                    $query->where('is_deleted', false);
                })
                ->make(true);
        }

        return view('settings.countries', [
            'users' => $users,
            'canEditCountry' => auth()->user()->can('update-country'),
            'canDeleteCountry' =>  auth()->user()->can('delete-country')
        ]);
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
   // Export countries data to Excel
public function export(Request $request)
{
    // Query and apply filters manually using conditional where clauses
    $countries = Countries::query()
        ->with(['createdByUser', 'updatedByUser']) // Eager load relationships
        ->when($request->id, function ($query, $id) {
            return $query->where('id', $id);
        })
        ->when($request->country_name, function ($query, $country_name) {
            return $query->where('country_name', 'like', '%' . $country_name . '%');
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
                $q->where('country_name', 'like', "%$searchValue%")
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
    if ($countries->isEmpty()) {
        return response()->streamDownload(function() {
            echo "No data available for export.";
        }, 'countries.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="countries.xlsx"',
        ]);
    }

    // Create a new Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set headers for the Excel columns
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Country Name');
    $sheet->setCellValue('C1', 'Created At');
    $sheet->setCellValue('D1', 'Updated At');
    $sheet->setCellValue('E1', 'Created By');
    $sheet->setCellValue('F1', 'Updated By');

    // Insert data from the filtered countries model
    $row = 2; // Starting from row 2 as row 1 has headers
    foreach ($countries as $country) {
        $sheet->setCellValue('A' . $row, $country->id);
        $sheet->setCellValue('B' . $row, $country->country_name);
        $sheet->setCellValue('C' . $row, $country->created_at ? Carbon::parse($country->created_at)->format('M d, Y h:i A') : 'N/A');
        $sheet->setCellValue('D' . $row, $country->updated_at ? Carbon::parse($country->updated_at)->format('M d, Y h:i A') : 'Not updated');
        $sheet->setCellValue('E' . $row, $country->createdByUser ? $country->createdByUser->name : 'Unknown');
        $sheet->setCellValue('F' . $row, $country->updatedByUser ? $country->updatedByUser->name : 'Not updated');

        $row++;
    }

    // Write the spreadsheet to a file (in memory)
    $writer = new Xlsx($spreadsheet);
    $fileName = 'countries_' . now()->format('Y-m-d') . '.xlsx'; // Custom file name

    // Prepare the response for download
    return response()->streamDownload(function() use ($writer) {
        $writer->save('php://output');
    }, $fileName, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Cache-Control' => 'max-age=0',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
    ]);
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
        $country->is_deleted = true; // Set is_deleted to true
        $country->save(); // Save the change to the database

        return response()->json(['success' => true]);
    }
}
