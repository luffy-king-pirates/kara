<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class UsersController extends Controller
{
    // Show the Users view
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::select(['id', 'first_name', 'middle_name', 'last_name', 'phone', 'email', 'name', 'profile_picture']);

            return DataTables::of($users)

                ->addColumn('profile_picture', function ($row) {
                    return $row->profile_picture
                        ?  Storage::url($row->profile_picture)
                        :  "https://res.cloudinary.com/dwzht4utm/image/upload/v1727019534/images_b5ws3b.jpg" ;
                        ;
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-primary edit-user">Edit</a>
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-danger delete-user">Delete</a>
                    ';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search['value'] != '') {
                        $searchValue = $request->search['value'];
                        $query->where(function($q) use ($searchValue) {
                            $q->where('first_name', 'like', "%$searchValue%")
                              ->orWhere('middle_name', 'like', "%$searchValue%")
                              ->orWhere('last_name', 'like', "%$searchValue%")
                              ->orWhere('phone', 'like', "%$searchValue%")
                              ->orWhere('email', 'like', "%$searchValue%")
                            ;
                        });
                    }

                    if ($request->has('first_name') && $request->first_name != '') {
                        $query->where('first_name', 'like', "%" . $request->first_name . "%");
                    }
                    $query->where('is_deleted', false);

                })
                ->make(true);

        }

        return view('users', [
            'canEditUser' => auth()->user()->can('update-user'),
            'canDeleteUser' => auth()->user()->can('delete-user')
        ]);
    }

    // Store new user
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:50|unique:users,name',
            'password' => 'required|string|min:8',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = new User();
        $user->first_name = $request->input('first_name');
        $user->middle_name = $request->input('middle_name');
        $user->last_name = $request->input('last_name');
        $user->phone = $request->input('phone');
        $user->email = $request->input('email');
        $user->name = $request->input('name');
        $user->password = Hash::make($request->input('password'));

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        $user->save();

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        // Find the user or fail
        $user = User::findOrFail($id);
        Log::info("request");
        Log::info($request->all());
        // Validate the request
        $request->validate([
            'first_name' => 'nullable|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'name' => 'nullable|string|max:50|unique:users,name,' . $id,
            'password' => 'nullable|string|min:8',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update user attributes
        $user->first_name = $request->input('first_name');
        $user->middle_name = $request->input('middle_name');
        $user->last_name = $request->input('last_name');
        $user->phone = $request->input('phone');
        $user->email = $request->input('email');
        $user->name = $request->input('name');

        // Hash password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::delete('public/' . $user->profile_picture);
            }

            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }else {
            // Set profile picture to null if no file is uploaded
            $user->profile_picture = null;
        }

        // Save the updated user
        $user->save();

        return response()->json(['success' => true]);
    }

    // Export users data to Excel
    public function export(Request $request)
    {
        // Start building the query for your model (replace YourModel with the actual model)
        $query = User::query(); // Replace YourModel with the actual model name
         // Eager load relationships if necessary

        // Apply search filters
        if ($request->has('search') && $request->search['value'] != '') {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('first_name', 'like', "%$searchValue%")
                  ->orWhere('middle_name', 'like', "%$searchValue%")
                  ->orWhere('last_name', 'like', "%$searchValue%")
                  ->orWhere('phone', 'like', "%$searchValue%")
                  ->orWhere('email', 'like', "%$searchValue%");
            });
        }

        if ($request->has('first_name') && $request->first_name != '') {
            $query->where('first_name', 'like', "%" . $request->first_name . "%");
        }

        // Execute the query and get the results
        $results = $query->get();

        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers for the Excel columns
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'First Name');
        $sheet->setCellValue('C1', 'Middle Name');
        $sheet->setCellValue('D1', 'Last Name');
        $sheet->setCellValue('E1', 'Phone');
        $sheet->setCellValue('F1', 'Email');
        $sheet->setCellValue('G1', 'Profile Image');

        // Insert data from the filtered results
        $row = 2; // Starting from row 2 as row 1 has headers
        foreach ($results as $item) {
            $sheet->setCellValue('A' . $row, $item->id);
            $sheet->setCellValue('B' . $row, $item->first_name);
            $sheet->setCellValue('C' . $row, $item->middle_name);
            $sheet->setCellValue('D' . $row, $item->last_name);
            $sheet->setCellValue('E' . $row, $item->phone);
            $sheet->setCellValue('F' . $row, $item->email);

            // Add the profile image if it exists
            if ($item->profile_picture) {

                    $sheet->setCellValue('G' . $row, $item->profile_picture);

            } else {
                $sheet->setCellValue('G' . $row, 'No Image'); // Placeholder if no image exists
            }

            $row++;
        }

        // Write the spreadsheet to a file (in memory)
        $writer = new Xlsx($spreadsheet);
        $fileName = 'users.xlsx';

        // Prepare the response for download
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    // Edit user (fetch details)
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    // Delete user
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Delete profile picture if exists
        if ($user->profile_picture) {
            Storage::delete('public/' . $user->profile_picture);
        }

        $user->is_deleted = true; // Set is_deleted to true
        $user->save(); // Save the change to the database

        return response()->json(['success' => true]);

    }
}
