<?php

namespace App\Http\Controllers;

use App\Models\Customers; // Your actual model for customers
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomersExport; // Your export class for customers
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class CustomersController extends Controller
{
    // Show the Customers view
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $customers = Customers::select(['id', 'customer_name', 'customer_tin', 'customer_vrn', 'customer_location', 'customer_address', 'customer_mobile', 'customer_email', 'is_active', 'created_at', 'updated_at']);

            return DataTables::of($customers)
            ->addColumn('customer_name', function ($row) {
                return $row->customer_name ?: '<span class="text-danger">Not Set</span>';
            })
            ->addColumn('customer_tin', function ($row) {
                return $row->customer_tin ?: '<span class="text-danger">Not Set</span>';
            })
            ->addColumn('customer_vrn', function ($row) {
                return $row->customer_vrn ?: '<span class="text-danger">Not Set</span>';
            })
            ->addColumn('customer_location', function ($row) {
                return $row->customer_location ?: '<span class="text-danger">Not Set</span>';
            })
            ->addColumn('customer_address', function ($row) {
                return $row->customer_address ?: '<span class="text-danger">Not Set</span>';
            })
            ->addColumn('customer_mobile', function ($row) {
                return $row->customer_mobile ?: '<span class="text-danger">Not Set</span>';
            })
            ->addColumn('customer_email', function ($row) {
                return $row->customer_email ?: '<span class="text-danger">Not Set</span>';
            })
            ->addColumn('is_active', function ($row) {
                return $row->is_active ? '<i class="fa fa-check-circle text-success"></i>' : '<i class="fa fa-times-circle text-danger"></i>';
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at ? Carbon::parse($row->created_at)->format('M d, Y h:i A') : '<span class="text-danger">Not Set</span>';
            })
            ->addColumn('updated_at', function ($row) {
                return $row->updated_at ? Carbon::parse($row->updated_at)->format('M d, Y h:i A') : 'Not updated';
            })
            ->addColumn('action', function ($row) {
                return '
                    <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-primary edit-month">Edit</a>
                    <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-danger delete-month">Delete</a>
                ';
            })
                ->filter(function ($query) use ($request) {
                    if ($request->has('customer_name') && $request->customer_name != '') {
                        $query->where('customer_name', 'like', "%" . $request->customer_name . "%");
                    }
                    if ($request->has('customer_tin') && $request->customer_tin != '') {
                        $query->where('customer_tin', 'like', "%" . $request->customer_tin . "%");
                    }
                    if ($request->has('customer_vrn') && $request->customer_vrn != '') {
                        $query->where('customer_vrn', 'like', "%" . $request->customer_vrn . "%");
                    }
                    if ($request->has('customer_location') && $request->customer_location != '') {
                        $query->where('customer_location', 'like', "%" . $request->customer_location . "%");
                    }
                    if ($request->has('customer_address') && $request->customer_address != '') {
                        $query->where('customer_address', 'like', "%" . $request->customer_address . "%");
                    }
                    if ($request->has('customer_mobile') && $request->customer_mobile != '') {
                        $query->where('customer_mobile', 'like', "%" . $request->customer_mobile . "%");
                    }
                    if ($request->has('customer_email') && $request->customer_email != '') {
                        $query->where('customer_email', 'like', "%" . $request->customer_email . "%");
                    }
                    if ($request->has('is_active') && $request->is_active != '') {
                        $query->where('is_active', $request->is_active);
                    }
                    $query->where('is_deleted', false);
                })
                ->make(true);
        }

        return view('settings.customers', [

            'canEditCustomer' => auth()->user()->can('update-customer'),
            'canDeleteCustomer' => auth()->user()->can('delete-customer')
        ]);
    }

    // Store new customer
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100|unique:customers,customer_name',
            'customer_tin' => 'required|string|max:50',
            'customer_vrn' => 'required|string|max:50',
            'customer_location' => 'required|string|max:150',
            'customer_address' => 'required|string|max:200',
            'customer_mobile' => 'required|string|max:15',
            'customer_email' => 'required|email',
            'is_active' => 'required|boolean',
        ]);

        $customer = new Customers();
        $customer->customer_name = $request->input('customer_name');
        $customer->customer_tin = $request->input('customer_tin');
        $customer->customer_vrn = $request->input('customer_vrn');
        $customer->customer_location = $request->input('customer_location');
        $customer->customer_address = $request->input('customer_address');
        $customer->customer_mobile = $request->input('customer_mobile');
        $customer->customer_email = $request->input('customer_email');
        $customer->is_active = $request->input('is_active');
        $customer->save();

        return response()->json(['success' => true]);
    }

    // Update existing customer
public function update(Request $request, $id)
{
    $customer = Customers::findOrFail($id); // Corrected here

    $request->validate([
        'customer_name' => 'required|string|max:100|unique:customers,customer_name,' . $id,
        'customer_tin' => 'required|string|max:50',
        'customer_vrn' => 'required|string|max:50',
        'customer_location' => 'required|string|max:150',
        'customer_address' => 'required|string|max:200',
        'customer_mobile' => 'required|string|max:15',
        'customer_email' => 'required|email',
        'is_active' => 'required|boolean',
    ]);

    $customer->customer_name = $request->input('customer_name');
    $customer->customer_tin = $request->input('customer_tin');
    $customer->customer_vrn = $request->input('customer_vrn');
    $customer->customer_location = $request->input('customer_location');
    $customer->customer_address = $request->input('customer_address');
    $customer->customer_mobile = $request->input('customer_mobile');
    $customer->customer_email = $request->input('customer_email');
    $customer->is_active = $request->input('is_active');
    $customer->save();

    return response()->json(['success' => true]);
}


    // Export customers data to Excel
    public function export(Request $request)
    {
        // Query and apply filters manually using conditional where clauses for currencies
        $currencies = Customers::all()
            ;

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
        $fileName = 'customers_' . now()->format('Y-m-d') . '.xlsx'; // Custom file name

        // Prepare the response for download
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
    // Edit customer (fetch details)
    public function edit($id)
    {
        $customer = Customers::findOrFail($id);
        return response()->json($customer);
    }

    // Delete customer
    public function destroy($id)
    {
        $customer = Customers::findOrFail($id);
        $customer->is_deleted = true; // Set is_deleted to true
        $customer->save(); // Save the change to the database

        return response()->json(['success' => true]);
    }
}

