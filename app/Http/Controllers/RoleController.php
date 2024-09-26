<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RolesExport; // Ensure this export class exists
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class RoleController extends Controller
{
    // Show the Roles view
    public function index(Request $request)
    {
        $users = User::all();
        Log::info($request->ajax());
        if ($request->ajax()) {
            $roles = Role::with(['createdByUser:id,name', 'updatedByUser:id,name'])
                ->select(['id', 'role_name','description', 'created_at', 'updated_at', 'created_by', 'updated_by']);

            return DataTables::of($roles)
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
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-primary edit-role">Edit</a>
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-danger delete-role">Delete</a>
                    ';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search['value'] != '') {
                        $searchValue = $request->search['value'];
                        $query->where(function($q) use ($searchValue) {
                            $q->where('role_name', 'like', "%$searchValue%")
                              ->orWhereHas('createdByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              })
                              ->orWhereHas('updatedByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              });
                        });
                    }

                    if ($request->has('role_name') && $request->role_name != '') {
                        $query->where('role_name', 'like', "%" . $request->role_name . "%");
                    }
                    if ($request->has('description') && $request->description != '') {
                        $query->where('description', 'like', "%" . $request->description . "%");
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

        return view('security.role', [
            'users' => $users,
            'canEditRole' => auth()->user()->can('update-role'),
            'canDeleteRole' => auth()->user()->can('delete-role')
        ]);
    }

    // Store new role
    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:50|unique:roles,role_name', // Changed units to roles
        ]);

        $role = new Role(); // Change from Units to Role
        $role->role_name = $request->input('role_name');
        $role->description = $request->input('description');
        $role->created_by = auth()->user()->id;
        $role->updated_by = auth()->user()->id;
        $role->save();

        return response()->json(['success' => true]);
    }

    // Update existing role
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id); // Change from Units to Role

        $request->validate([
            'role_name' => 'required|string|max:50|unique:roles,role_name,' . $id, // Changed units to roles
        ]);

        $role->role_name = $request->input('role_name');
        $role->description = $request->input('description');
        $role->updated_by = auth()->user()->id;
        $role->save();

        return response()->json(['success' => true]);
    }

    // Export roles data to Excel
    public function export(Request $request)
    {
        // Query and apply filters manually using conditional where clauses
        $roles = Role::query()
            ->with(['createdByUser', 'updatedByUser']) // Eager load relationships
            ->when($request->search['value'] ?? null, function ($query, $searchValue) {
                return $query->where(function($q) use ($searchValue) {
                    $q->where('role_name', 'like', "%$searchValue%")
                      ->orWhereHas('createdByUser', function($q) use ($searchValue) {
                          $q->where('name', 'like', "%$searchValue%");
                      })
                      ->orWhereHas('updatedByUser', function($q) use ($searchValue) {
                          $q->where('name', 'like', "%$searchValue%");
                      });
                });
            })
            ->when($request->role_name, function ($query, $role_name) {
                return $query->where('role_name', 'like', '%' . $role_name . '%');
            })
            ->when($request->description, function ($query, $description) {
                return $query->where('description', 'like', '%' . $description . '%');
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
        if ($roles->isEmpty()) {
            return response()->streamDownload(function() {
                echo "No data available for export.";
            }, 'roles.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
                'Content-Disposition' => 'attachment; filename="roles.xlsx"',
            ]);
        }

        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers for the Excel columns
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Role Name');
        $sheet->setCellValue('C1', 'Description');


        $sheet->setCellValue('E1', 'Updated At');
        $sheet->setCellValue('D1', 'Created At');


        // Insert data from the filtered roles model
        $row = 2; // Starting from row 2 as row 1 has headers
        foreach ($roles as $role) {
            $sheet->setCellValue('A' . $row, $role->id);
            $sheet->setCellValue('B' . $row, $role->role_name);
            $sheet->setCellValue('D' . $row, $role->created_at ? Carbon::parse($role->created_at)->format('M d, Y h:i A') : 'N/A');
            $sheet->setCellValue('E' . $row, $role->updated_at ? Carbon::parse($role->updated_at)->format('M d, Y h:i A') : 'Not updated');
            $sheet->setCellValue('C' . $row, $role->description ? $role->description : 'Unknown');

            $row++;
        }

        // Write the spreadsheet to a file (in memory)
        $writer = new Xlsx($spreadsheet);
        $fileName = 'roles_' . now()->format('Y-m-d') . '.xlsx'; // Custom file name

        // Prepare the response for download
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }


    // Edit role (fetch details)
    public function edit($id)
    {
        $role = Role::findOrFail($id); // Change from Units to Role
        return response()->json($role);
    }

    // Delete role
    public function destroy($id)
    {
        $role = Role::findOrFail($id); // Change from Units to Role
        $role->is_deleted = true; // Set is_deleted to true
        $role->save(); // Save the change to the database

        return response()->json(['success' => true]);
    }
}
