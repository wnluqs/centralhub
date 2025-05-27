<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth; // Make sure this is at the top if not already 20th may 2025
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::all();
        return view('settings.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        // Get all roles (for role selection)
        $roles = Role::all(); // Optionally exclude Admin
        return view('settings.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        // Validate the request (you can extract these rules to a FormRequest)
        $request->validate([
            'name'     => 'required|string|max:255',
            'branch' => 'required|string|in:Kuantan,Kuala Terengganu,Machang',// ✅ Add validation for branch
            'email'    => 'required|string|email|max:255|unique:users',
            'staff_id' => 'nullable|string|unique:users,staff_id',
            'password' => 'required|string|min:6|confirmed',
            'roles'    => 'nullable|array',
            'roles.*'  => 'string|exists:roles,name'
        ]);

        // Create the user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'staff_id' => $request->staff_id, // ✅ Add this line
            'branch'   => $request->branch, // ✅ Add this
            'password' => Hash::make($request->password),
        ]);

        // Assign selected roles (if any)
        if ($request->filled('roles')) {
            $user->assignRole($request->roles);
        }

        return redirect()->route('admin.users.index')

            ->with('success', 'User created successfully!');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // Get all roles (for role selection)
        $roles = Role::where('name', '!=', 'Admin')->get(); // Optionally exclude Admin
        return view('settings.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // Validate input
        $request->validate([
            'name'     => 'required|string|max:255',
            'branch'   => 'required|string|in:Kuantan,Kuala Terengganu,Machang',
            'email'    => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'staff_id' => ['nullable', 'string', Rule::unique('users', 'staff_id')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'roles'    => 'nullable|array',
            'roles.*'  => 'string|exists:roles,name',
        ]);


        // Update user details
        $user->name  = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        // Sync roles: remove unselected roles and assign new ones
        if ($request->filled('roles')) {
            $user->syncRoles($request->roles);
        } else {
            $user->syncRoles([]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Optionally prevent deletion of self or Admin user
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }

    public function me(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'staff_id' => $user->staff_id,
            'branch' => $user->branch,
            'roles' => $user->getRoleNames(), // Only if using Spatie
        ]);
    }
}
