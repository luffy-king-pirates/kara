<?php

namespace App\Http\Controllers;

use App\Models\Customers; // Your actual model for customers
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomersExport; // Your export class for customers

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
                })
                ->make(true);
        }

        return view('settings.customers'); // Update to reflect the actual view
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
        return Excel::download(new CustomersExport, 'customers.xlsx');
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
        $customer->delete();

        return response()->json(['success' => true]);
    }
}

