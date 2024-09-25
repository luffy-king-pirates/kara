<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ManagePermissionController extends Controller
{
    public function index(Request $request)
{
    // Fetch roles with associated permissions
    $roles = Role::with('permissions')->get();

    // Define static pages and actions
    $pages = [
        'brand' => ['create', 'read', 'update', 'delete','export'],
        'category' => ['create', 'read', 'update', 'delete','export'],
        'country' => ['create', 'read', 'update', 'delete','export'],
        'currency' => ['create', 'read', 'update', 'delete','export'],
        'customer' => ['create', 'read', 'update', 'delete','export'],
        'month' => ['create', 'read', 'update', 'delete','export'],
        'role' => ['create', 'read', 'update', 'delete','export'],
        'stock-type' => ['create', 'read', 'update', 'delete','export'],
        'supplier' => ['create', 'read', 'update', 'delete','export'],
        'unit' => ['create', 'read', 'update', 'delete','export'],
        'user' => ['create', 'read', 'update', 'delete','export'],
        'year' => ['create', 'read', 'update', 'delete','export'],
        'user-assigned-role' => ['create', 'read', 'update', 'delete','export'],
    ];

    // Prepare data for DataTables
    $data = [];
    foreach ($roles as $role) {
        foreach ($pages as $page => $actions) {
            // Get existing permissions for this role and page
            $existingPermissions = $role->permissions->where('page', $page)->pluck('action')->toArray();

            // Add the role's permissions and related data to the array
            $data[] = [
                'id' => $role->id, // Include role ID
                'role_name' => $role->role_name,
                'page' => $page,
                'permissions' => $existingPermissions, // Store permissions
                'actions' => '', // Actions will be handled by DataTable's render function
                'manage' => '<button class="btn btn-primary save-permissions" data-role-id="' . $role->id . '">Save</button>',
            ];
        }
    }

    // Check if the request is an AJAX request
    if ($request->ajax()) {
        // Apply filters for Role Name, Page, and Action
        $roleNameFilter = $request->get('role_name');
        $pageFilter = $request->get('page');
        $actionFilter = $request->get('action');

        // Filter the data array based on the filters from the DataTables UI
        if ($roleNameFilter) {
            $data = array_filter($data, function($row) use ($roleNameFilter) {
                return stripos($row['role_name'], $roleNameFilter) !== false;
            });
        }

        if ($pageFilter) {
            $data = array_filter($data, function($row) use ($pageFilter) {
                return stripos($row['page'], $pageFilter) !== false;
            });
        }

        if ($actionFilter) {
            $data = array_filter($data, function($row) use ($actionFilter) {
                return in_array($actionFilter, $row['permissions']);
            });
        }

        // Return filtered data as JSON for DataTables
        return DataTables::of($data)->make(true);
    }

    // Render the view for managing permissions
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
