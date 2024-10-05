<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ManagePermissionController extends Controller
{


     public function index(Request $request)
    {
        $roles = Role::all();


        return view('security.manage.index', compact('roles'));
    }

    public function show($role_name,Request $request)
{
    // Fetch roles with associated permissions
    $roles = Role::with('permissions')->get();

    // Define static pages and actions
    $pages = [
        'brand' => ['create', 'read', 'update', 'delete','export','manage'],
        'category' => ['create', 'read', 'update', 'delete','export','manage'],
        'country' => ['create', 'read', 'update', 'delete','export','manage'],
        'currency' => ['create', 'read', 'update', 'delete','export','manage'],
        'customer' => ['create', 'read', 'update', 'delete','export','manage'],
        'month' => ['create', 'read', 'update', 'delete','export','manage'],
        'role' => ['create', 'read', 'update', 'delete','export','manage'],
        'stock-type' => ['create', 'read', 'update', 'delete','export','manage'],
        'supplier' => ['create', 'read', 'update', 'delete','export','manage'],
        'unit' => ['create', 'read', 'update', 'delete','export','manage'],
        'user' => ['create', 'read', 'update', 'delete','export','manage'],
        'year' => ['create', 'read', 'update', 'delete','export','manage'],
        'user-assigned-role' => ['create', 'read', 'update', 'delete','export','manage'],
        'items' => ['create', 'read', 'update', 'delete','export','manage'],
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
        $pageFilter = $request->get('page');
        $actionFilter = $request->get('action');

        // Filter the data array based on the filters from the DataTables UI
        if ($role_name) {
            $data = array_filter($data, function($row) use ($role_name) {
                return stripos($row['role_name'], $role_name) !== false;
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
    return view('security.manage.detail', compact('roles', 'pages'));
}


public function savePermissions(Request $request)
{
    $roleId = $request->input('role_id');
    $permissions = $request->input('permissions', []); // permissions should be an associative array with pages as keys

    // Fetch the role
    $role = Role::findOrFail($roleId);

    // Loop through the permissions for each page and update accordingly
    foreach ($permissions as $page => $actions) {
        // Fetch the current permissions for this page
        $existingPermissions = $role->permissions()->where('page', $page)->pluck('action')->toArray();

        // Determine which actions to add (checked in the request but not in the database)
        $actionsToAdd = array_diff($actions, $existingPermissions);

        // Determine which actions to delete (currently in the database but unchecked in the request)
        $actionsToDelete = array_diff($existingPermissions, $actions);

        // Add the new permissions
        foreach ($actionsToAdd as $action) {
            $role->permissions()->create([
                'page' => $page,
                'action' => $action,
            ]);
        }

        // Remove the unchecked permissions
        if (!empty($actionsToDelete)) {
            $role->permissions()
                ->where('page', $page)
                ->whereIn('action', $actionsToDelete)
                ->delete();
        }
    }

    return response()->json(['message' => 'Permissions updated successfully']);
}


}
