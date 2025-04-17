<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RoleManagementController extends Controller
{
    public function index()
    {
        // dd();
        // 1) Get all users, or optionally exclude the Admin user from the list
        $users = User::all();
        // If you want to exclude Admin accounts, uncomment:
        // $users = User::whereDoesntHave('roles', fn($q) => $q->where('name', 'Admin'))->get();

        // 2) Get all roles except Admin (so we don't accidentally remove Admin from ourselves)
        $roles = Role::where('name', '!=', 'Admin')->get();

        return view('settings.index', compact('users', 'roles'));
    }

    public function assignRoles(Request $request)
    {
        // 'roles' is expected to be an array of userId => [roles[]]
        $data = $request->input('roles', []);

        foreach ($data as $userId => $roleNames) {
            $user = User::findOrFail($userId);

            // Sync the roles: remove any not in the array, add any that are
            $user->syncRoles($roleNames);
        }

        return redirect()->route('admin.settings')->with('success', 'Roles assigned successfully!');
    }
}
