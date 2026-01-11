<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $users = User::all();
        return view('dashboard.users.index', compact('users'));
    }

    public function show (User $user)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $user->load('staff');

        return view('dashboard.users.show', compact('user'));
    }

    public function updateRole(Request $request, User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'role' => 'required|in:admin,staff,customer'
        ]);

        $user->update([
            'role' => $request->role
        ]);

        return back()->with('success', 'User role updated successfully!');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20|regex:/^[0-9\-\+\(\)\s]+$/',
            'role' => 'required|in:admin,staff,customer',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the user
        $user = User::create([
            'full_name' => $validated['full_name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'password' => bcrypt($validated['password']),
            'is_active' => true,
        ]);

        // If the user is staff, create a staff record
        if ($validated['role'] === 'staff') {
            $staffData = [
                'specialty' => $request->input('specialty', 'General'),
                'color_code' => $request->input('color_code', '#' . substr(md5(rand()), 0, 6)),
                'is_available' => true,
            ];
            
            // Create staff record with the provided or default values
            $user->staff()->create($staffData);
        }

        return redirect()->route('dashboard.users.index')
            ->with('success', 'User created successfully!');
    }

    public function toggleActive(Request $request, User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "User {$status} successfully!");
    }
}