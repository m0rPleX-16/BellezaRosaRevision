<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login-custom'); // Fixed: Use correct view name
    }

    public function showRegister()
    {
        return view('auth.register-custom'); // Fixed: Use correct view name
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find user by username
        $user = User::where('username', $request->username)->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'username' => 'The provided credentials do not match our records.',
            ])->onlyInput('username');
        }

        // Check if user is active
        if (!$user->is_active) {
            return back()->withErrors([
                'username' => 'Your account has been deactivated. Please contact an administrator.',
            ])->onlyInput('username');
        }

        // Log the user in
        Auth::login($user);
        $request->session()->regenerate();

        // Update last login timestamp
        $user->last_login_at = now();
        $user->save();

        // Redirect based on role
        if ($user->role === 'admin') {
            return redirect()->intended(route('dashboard.index'));
        }
        
        if ($user->role === 'staff') {
            return redirect()->intended(route('staff.dashboard'));
        }

        // Redirect customers to their dashboard
        return redirect()->intended(route('customer.dashboard'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'username'  => 'required|string|max:50|unique:users',
            'phone'     => 'required|string|max:20|unique:users',
            'email'     => 'nullable|email|unique:users',
            'password'  => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'username'  => $request->username,
            'phone'     => $request->phone,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'customer', // default
        ]);

        Auth::login($user);

        // Redirect customers to their dashboard, others to home
        if ($user->role === 'customer') {
            return redirect()->route('customer.dashboard')->with('success', 'Welcome! Your account has been created.');
        }

        return redirect()->route('home')->with('success', 'Welcome! Your account has been created.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}