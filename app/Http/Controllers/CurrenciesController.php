<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CurrenciesExport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class CurrenciesController extends Controller
{
    // Show the Currencies view
    public function index(Request $request)
    {
        $users = User::all();
        if ($request->ajax()) {
            $currencies = Currency::with(['createdByUser:id,name', 'updatedByUser:id,name'])
                ->select(['id', 'currencie_name', 'currencie_value', 'created_at', 'updated_at', 'created_by', 'updated_by']);

            return DataTables::of($currencies)
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
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-primary edit-currency">Edit</a>
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-danger delete-currency">Delete</a>
                    ';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search['value'] != '') {
                        $searchValue = $request->search['value'];
                        $query->where(function($q) use ($searchValue) {
                            $q->where('currencie_name', 'like', "%$searchValue%")
                              ->orWhereHas('createdByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              })
                              ->orWhereHas('updatedByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              });
                        });
                    }

                    if ($request->has('currencie_name') && $request->currencie_name != '') {
                        $query->where('currencie_name', 'like', "%" . $request->currencie_name . "%");
                    }
                    if ($request->has('currencie_value') && $request->currencie_value != '') {
                        $query->where('currencie_value', $request->currencie_value);
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

        return view('settings.currencies', [
            'users' => $users,
            'canEditCurrency' => auth()->user()->can('update-currency'),
            'canDeleteCurrency' => auth()->user()->can('delete-currency')
        ]);
    }

    // Store new currency
    public function store(Request $request)
    {
        $request->validate([
            'currencie_name' => 'required|string|max:50|unique:currencies,currencie_name',
            'currencie_value' => 'required|integer',
        ]);

        $currency = new Currency();
        $currency->currencie_name = $request->input('currencie_name');
        $currency->currencie_value = $request->input('currencie_value');
        $currency->created_by = auth()->user()->id;
        $currency->updated_by = auth()->user()->id;
        $currency->save();

        return response()->json(['success' => true]);
    }

    // Update existing currency
    public function update(Request $request, $id)
    {
        $currency = Currency::findOrFail($id);

        $request->validate([
            'currencie_name' => 'required|string|max:50|unique:currencies,currencie_name,' . $id,
            'currencie_value' => 'required|integer',
        ]);

        $currency->currencie_name = $request->input('currencie_name');
        $currency->currencie_value = $request->input('currencie_value');
        $currency->updated_by = auth()->user()->id;
        $currency->save();

        return response()->json(['success' => true]);
    }

    // Export currencies data to Excel
    public function export(Request $request)
    {
        // Query and apply filters manually using conditional where clauses for currencies
        $currencies = Currency::query()
            ->with(['createdByUser', 'updatedByUser']) // Eager load relationships for created/updated users
            ->when($request->currencie_name, function ($query, $currencie_name) {
                return $query->where('currencie_name', 'like', '%' . $currencie_name . '%');
            })
            ->when($request->currencie_value, function ($query, $currencie_value) {
                return $query->where('currencie_value', $currencie_value);
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
                    $q->where('currencie_name', 'like', "%$searchValue%")
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
        if ($currencies->isEmpty()) {
            return response()->streamDownload(function() {
                echo "No data available for export.";
            }, 'currencies.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
                'Content-Disposition' => 'attachment; filename="currencies.xlsx"',
            ]);
        }

        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers for the Excel columns
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Currency Name');
        $sheet->setCellValue('C1', 'Currency Value');
        $sheet->setCellValue('D1', 'Created At');
        $sheet->setCellValue('E1', 'Updated At');
        $sheet->setCellValue('F1', 'Created By');
        $sheet->setCellValue('G1', 'Updated By');

        // Insert data from the filtered currencies model
        $row = 2; // Starting from row 2 as row 1 has headers
        foreach ($currencies as $currency) {
            $sheet->setCellValue('A' . $row, $currency->id);
            $sheet->setCellValue('B' . $row, $currency->currencie_name);
            $sheet->setCellValue('C' . $row, $currency->currencie_value);
            $sheet->setCellValue('D' . $row, $currency->created_at ? Carbon::parse($currency->created_at)->format('M d, Y h:i A') : 'N/A');
            $sheet->setCellValue('E' . $row, $currency->updated_at ? Carbon::parse($currency->updated_at)->format('M d, Y h:i A') : 'Not updated');
            $sheet->setCellValue('F' . $row, $currency->createdByUser ? $currency->createdByUser->name : 'Unknown');
            $sheet->setCellValue('G' . $row, $currency->updatedByUser ? $currency->updatedByUser->name : 'Not updated');

            $row++;
        }

        // Write the spreadsheet to a file (in memory)
        $writer = new Xlsx($spreadsheet);
        $fileName = 'currencies_' . now()->format('Y-m-d') . '.xlsx'; // Custom file name

        // Prepare the response for download
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }


    // Edit currency (fetch details)
    public function edit($id)
    {
        $currency = Currency::findOrFail($id);
        return response()->json($currency);
    }

    // Delete currency
    public function destroy($id)
    {
        $currency = Currency::findOrFail($id);
        $currency->delete();

        return response()->json(['success' => true]);
    }
}
