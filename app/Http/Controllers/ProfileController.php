<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load(['customer', 'staff']);
        
        // Check if this is a customer route - use customer profile view with layouts.app
        if (request()->routeIs('customer.*')) {
            return view('customer.profile.edit', [
                'user' => $user,
                'customer' => $user->customer,
                'staff' => $user->staff
            ]);
        }
        
        // Admin/staff use the standard profile view with x-app-layout
        return view('profile.edit', [
            'user' => $user,
            'customer' => $user->customer,
            'staff' => $user->staff
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        DB::transaction(function () use ($request, $user) {
            // Get validated data
            $data = $request->validated();
            
            // Update user data
            $userData = [
                'full_name' => $data['full_name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'gender' => $data['gender'] ?? null,
            ];
            
            $user->fill($userData);
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
            $user->save();
            
            // Update customer data if user is a customer
            if ($user->isCustomer() && $user->customer) {
                $customerData = [
                    'full_name' => $data['full_name'],
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? $user->customer->phone,
                    'gender' => $data['gender'] ?? $user->customer->gender,
                    'birth_date' => $data['birth_date'] ?? $user->customer->birth_date,
                    'notes' => $data['notes'] ?? $user->customer->notes,
                ];
                $user->customer->update($customerData);
            }
            
            // Update staff data if user is staff
            if ($user->isStaff() && $user->staff) {
                $staffData = [];
                if (isset($data['color_code'])) {
                    $staffData['color_code'] = $data['color_code'];
                }
                // Note: specialty is managed by admin only
                if (!empty($staffData)) {
                    $user->staff->update($staffData);
                }
            }
        });
        
        // Determine redirect route based on user role
        if ($user->isCustomer()) {
            return Redirect::route('customer.profile.edit')->with('status', 'profile-updated');
        }
        
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
