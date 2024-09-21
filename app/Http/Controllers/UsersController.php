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

class UsersController extends Controller
{
    // Show the Users view
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::select(['id', 'first_name', 'middle_name', 'last_name', 'phone', 'email', 'name', 'profile_picture']);
            Log::info('New user created', ['users' => $users]);
            return DataTables::of($users)

                ->addColumn('profile_picture', function ($row) {
                    return $row->profile_picture
                        ? '<img src="'. Storage::url($row->profile_picture) .'" width="50" height="50">'
                        : 'No picture';
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
                  
                })
                ->make(true);
        }

        return view('users');
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

    // Update existing user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|unique:users,email,' . $id,
            'name' => 'required|string|max:50|unique:users,name,' . $id,
            'password' => 'nullable|string|min:8',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->first_name = $request->input('first_name');
        $user->middle_name = $request->input('middle_name');
        $user->last_name = $request->input('last_name');
        $user->phone = $request->input('phone');
        $user->email = $request->input('email');
        $user->name = $request->input('name');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture
            if ($user->profile_picture) {
                Storage::delete('public/' . $user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        $user->save();

        return response()->json(['success' => true]);
    }

    // Export users data to Excel
    public function export(Request $request)
    {
        $usersQuery = User::query();

        if ($request->has('first_name') && $request->first_name != '') {
            $usersQuery->where('first_name', 'like', "%" . $request->first_name . "%");
        }
        if ($request->has('created_at') && $request->created_at != '') {
            $usersQuery->whereDate('created_at', $request->created_at);
        }

        $users = $usersQuery->get();
        return Excel::download(new UsersExport($users), 'users.xlsx');
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

        $user->delete();

        return response()->json(['success' => true]);
    }
}
