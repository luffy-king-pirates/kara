<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ManagePermissionController extends Controller
{
    public function index(Request $request)
{
    $roles = Role::with('permissions')->get();

    // Define static pages and actions
    $pages = [
        'Dashboard' => ['create', 'read', 'update', 'delete'],
        'Users' => ['create', 'read', 'update', 'delete'],
        'Products' => ['create', 'read', 'update', 'delete'],
    ];

    // Prepare the data for DataTables
    $data = [];
    foreach ($roles as $role) {
        foreach ($pages as $page => $actions) {
            // Get existing permissions for this role and page
            $existingPermissions = $role->permissions->where('page', $page)->pluck('action')->toArray();

            // Store permissions in the row for easy access
            $data[] = [
                'id' => $role->id, // Include role ID
                'role_name' => $role->role_name,
                'page' => $page,
                'permissions' => $existingPermissions, // Add permissions to the data
                'actions' => '', // Leave this empty; we'll handle it in the DataTable render
                'manage' => '<button class="btn btn-primary save-permissions" data-role-id="' . $role->id . '">Save</button>'
            ];
        }
    }

    if ($request->ajax()) {
        return DataTables::of($data)->make(true);
    }

    return view('security.managePermissions', compact('roles', 'pages'));
}


    public function savePermissions(Request $request)
    {
        $roleId = $request->input('role_id');
        $permissions = $request->input('permissions', []); // permissions should now be an associative array with pages as keys

        // Fetch the role
        $role = Role::findOrFail($roleId);

        // Clear existing permissions for the role
        $role->permissions()->delete();

        // Assign new permissions
        foreach ($permissions as $page => $actions) {
            foreach ($actions as $action) {
                $role->permissions()->create(['action' => $action, 'page' => $page]); // Use the dynamic page
            }
        }

        return response()->json(['message' => 'Permissions updated successfully']);
    }
}
