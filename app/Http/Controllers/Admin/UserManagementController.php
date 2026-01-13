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

        // Prevent changing staff to admin (only one admin allowed - Nina)
        if ($user->isStaff() && $request->role === 'admin') {
            return back()->withErrors(['role' => 'Cannot change staff to admin. Only one admin is allowed in the system.']);
        }

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
            'gender' => 'required|in:male,female,other',
            'role' => 'required|in:staff', // Only allow staff (admin cannot be created through this form)
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the user
        $user = User::create([
            'full_name' => $validated['full_name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'gender' => $validated['gender'],
            'role' => $validated['role'],
            'password' => bcrypt($validated['password']),
            'is_active' => true,
        ]);

        // If the user is staff, create a staff record
        if ($validated['role'] === 'staff') {
            // Validate specialty to ensure it's one of the allowed enum values
            $specialty = $request->input('specialty', 'all');
            $allowedSpecialties = ['hair', 'nail', 'spa', 'hair_nail', 'hair_spa', 'nail_spa', 'all'];
            if (!in_array($specialty, $allowedSpecialties)) {
                $specialty = 'all'; // Default to 'all' if invalid
            }

            // Get color code from color_code_display or color_code input, or generate random
            $colorCode = $request->input('color_code_display') 
                ?: $request->input('color_code') 
                ?: '#' . substr(md5(rand()), 0, 6);

            $staffData = [
                'specialty' => $specialty,
                'color_code' => $colorCode,
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

    /**
     * Delete a user (staff only, cannot delete admin)
     */
    public function destroy(User $user)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // Prevent deleting admin users
        if ($user->isAdmin()) {
            return back()->withErrors(['delete' => 'Cannot delete admin users.']);
        }

        // Prevent deleting the last active admin (safety check)
        if ($user->isAdmin()) {
            $adminCount = User::where('role', 'admin')->where('is_active', true)->count();
            if ($adminCount <= 1) {
                return back()->withErrors(['delete' => 'Cannot delete the last active admin.']);
            }
        }

        // Check if user has appointments
        if ($user->staff && $user->staff->appointments()->count() > 0) {
            return back()->withErrors(['delete' => 'Cannot delete staff member with existing appointments. Please deactivate instead.']);
        }

        // Delete staff record if exists (cascade will handle it, but being explicit)
        if ($user->staff) {
            $user->staff->delete();
        }

        // Delete the user
        $user->delete();

        return redirect()->route('dashboard.users.index')
            ->with('success', 'Staff member deleted successfully!');
    }
}