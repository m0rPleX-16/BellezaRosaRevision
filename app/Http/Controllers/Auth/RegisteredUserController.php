<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'gender' => ['required', 'string', 'in:male,female,other,prefer_not_to_say'],
            'birth_date' => ['required', 'date', 'before:today'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        try {
            // Start a database transaction
            DB::beginTransaction();
            // Create the user
            $user = User::create([
                'name' => $validated['full_name'], // This is the name field in users table
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'customer',
                'phone' => $validated['phone'],
            ]);
            // Create the customer record
            $user->customer()->create([
                'user_id' => $user->id,
                'full_name' => $validated['full_name'], // This is full_name in customers table
                'username' => $validated['username'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'gender' => $validated['gender'],
                'birth_date' => $validated['birth_date'],
                'total_visits' => 0,
                'total_spent' => 0,
            ]);
            event(new Registered($user));
            Auth::login($user);
            // Commit the transaction
            DB::commit();
            return redirect(route('dashboard.index', absolute: false));
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            Log::error('Registration error: ' . $e->getMessage());
            return back()->with('error', 'Registration failed. Please try again.');
        }
    }
}
