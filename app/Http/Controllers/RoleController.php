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

class RoleController extends Controller
{
    // Show the Roles view
    public function index(Request $request)
    {
        $users = User::all();
        Log::info($request->ajax());
        if ($request->ajax()) {
            $roles = Role::with(['createdByUser:id,name', 'updatedByUser:id,name'])
                ->select(['id', 'role_name', 'created_at', 'updated_at', 'created_by', 'updated_by']);

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

        return view('security.role', compact('users')); // Adjusted to point to the roles view
    }

    // Store new role
    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:50|unique:roles,role_name', // Changed units to roles
        ]);

        $role = new Role(); // Change from Units to Role
        $role->role_name = $request->input('role_name');
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
        $role->updated_by = auth()->user()->id;
        $role->save();

        return response()->json(['success' => true]);
    }

    // Export roles data to Excel
    public function export(Request $request)
    {
        $rolesQuery = Role::with(['createdByUser:id,name', 'updatedByUser:id,name']);

        if ($request->has('role_name') && $request->role_name != '') {
            $rolesQuery->where('role_name', 'like', "%" . $request->role_name . "%");
        }
        if ($request->has('created_by') && $request->created_by != '') {
            $rolesQuery->where('created_by', $request->created_by);
        }
        if ($request->has('updated_by') && $request->updated_by != '') {
            $rolesQuery->where('updated_by', $request->updated_by);
        }
        if ($request->has('created_at') && $request->created_at != '') {
            $rolesQuery->whereDate('created_at', $request->created_at);
        }
        if ($request->has('updated_at') && $request->updated_at != '') {
            $rolesQuery->whereDate('updated_at', $request->updated_at);
        }

        $roles = $rolesQuery->get();
        return Excel::download(new RolesExport($roles), 'roles.xlsx'); // Ensure this export class exists
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
        $role->delete();

        return response()->json(['success' => true]);
    }
}
