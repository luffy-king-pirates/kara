<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\UserAssignRole;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

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
                })
                ->make(true);
        }

        return view('security.userassigned', compact('users', 'roles'));
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
        $assignment->delete();

        return response()->json(['success' => true]);
    }
}
