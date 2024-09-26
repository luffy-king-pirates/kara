<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\UserAssignRole;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class UserAssignRoleController extends Controller
{
    // Show the User Assign Role view
    public function index(Request $request)
    {
        $users = User::all();
        $roles = Role::all();

        if ($request->ajax()) {
            $assignments = UserAssignRole::with(['user:id,name', 'role:id,role_name'])
                ->select(['id', 'user_id', 'role_id', 'created_at', 'updated_at']);

            return DataTables::of($assignments)
                ->addColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('M d, Y h:i A');
                })
                ->addColumn('updated_at', function ($row) {
                    return $row->updated_at ? Carbon::parse($row->updated_at)->format('M d, Y h:i A') : 'Not updated';
                })
                ->addColumn('user', function ($row) {
                    return $row->user ? $row->user->name : 'Unknown';
                })
                ->addColumn('role', function ($row) {
                    return $row->role ? $row->role->role_name : 'Unknown';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-primary edit-role">Edit</a>
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-danger delete-role">Delete</a>
                    ';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search['value'] != '') {
                        $searchValue = $request->search['value'];
                        $query->where(function($q) use ($searchValue) {
                            $q->whereHas('user', function($q) use ($searchValue) {
                                $q->where('name', 'like', "%$searchValue%");
                            })->orWhereHas('role', function($q) use ($searchValue) {
                                $q->where('role_name', 'like', "%$searchValue%");
                            });
                        });
                    }
                    $query->where('is_deleted', false);
                })
                ->make(true);
        }

        return view('security.userassigned', [
            'users' => $users,
            'roles' => $roles,
            'canEditUserAssignedRole' => auth()->user()->can('update-user-assigned-role'),
            'canDeleteUserAssignedRole' => auth()->user()->can('delete-user-assigned-role')
        ]);
    }

    // Store or update role assignment
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        UserAssignRole::updateOrCreate(
            ['user_id' => $request->user_id, 'role_id' => $request->role_id],
            ['user_id' => $request->user_id, 'role_id' => $request->role_id]
        );

        return response()->json(['success' => true]);
    }

    // Edit role assignment (fetch details)
    public function edit($id)
    {
        $assignment = UserAssignRole::findOrFail($id);
        return response()->json($assignment);
    }

    // Delete role assignment
    public function destroy($id)
    {
        $assignment = UserAssignRole::findOrFail($id);
        $assignment->is_deleted = true; // Set is_deleted to true
        $assignment->save(); // Save the change to the database

        return response()->json(['success' => true]);
    }
    public function export(Request $request)
    {
        // Start building the query for UserAssignRole model
        $query = UserAssignRole::query()
            ->with(['user:id,name', 'role:id,role_name']); // Eager load relationships for user and role

        // Check if the search parameter is present
        if ($request->has('search') && $request->search['value'] != '') {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                // Filter by user's name
                $q->whereHas('user', function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%$searchValue%");
                })
                // Filter by role's name
                ->orWhereHas('role', function ($q) use ($searchValue) {
                    $q->where('role_name', 'like', "%$searchValue%");
                });
            });
        }

        // Execute the query and get the results
        $results = $query->get();

        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers for the Excel columns
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'User Name');
        $sheet->setCellValue('C1', 'Role');

        // Insert data from the filtered results
        $row = 2; // Starting from row 2 as row 1 has headers
        foreach ($results as $item) {
            $sheet->setCellValue('A' . $row, $item->id);
            $sheet->setCellValue('B' . $row, $item->user ? $item->user->name : 'N/A');
            $sheet->setCellValue('C' . $row, $item->role ? $item->role->role_name : 'N/A');
            $row++;
        }

        // Write the spreadsheet to a file (in memory)
        $writer = new Xlsx($spreadsheet);
        $fileName = 'user_assigned_role.xlsx';

        // Prepare the response for download
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="exported_data.xlsx"',
        ]);
    }


}
