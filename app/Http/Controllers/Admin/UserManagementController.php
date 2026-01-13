<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    public function index()
    {
        // Check if user is admin
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // Only show admin and staff users (exclude customers)
        $users = User::whereIn('role', ['admin', 'staff'])->get();
        return view('dashboard.users.index', compact('users'));
    }

    public function show (User $user)
    {
        // Check if user is admin
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $user->load('staff');

        return view('dashboard.users.show', compact('user'));
    }

    public function updateRole(Request $request, User $user)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'role' => 'required|in:admin,staff' // Only allow admin and staff
        ]);

        // Prevent removing the last admin
        if ($user->isAdmin() && $request->role !== 'admin') {
            $adminCount = User::where('role', 'admin')->where('is_active', true)->count();
            if ($adminCount <= 1) {
                return back()->withErrors(['role' => 'Cannot change role of the last active admin. At least one admin must remain.']);
            }
        }

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
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20|regex:/^[0-9\-\+\(\)\s]+$/',
            'role' => 'required|in:admin,staff', // Only allow admin and staff
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
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // Prevent deactivating the last active admin
        if ($user->isAdmin() && $user->is_active) {
            $adminCount = User::where('role', 'admin')->where('is_active', true)->count();
            if ($adminCount <= 1) {
                return back()->withErrors(['is_active' => 'Cannot deactivate the last active admin. At least one admin must remain active.']);
            }
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "User {$status} successfully!");
    }
}