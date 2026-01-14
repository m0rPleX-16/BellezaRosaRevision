<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Appointment;
use App\Models\User;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var User $user */
        $user = Auth::user();

        // Parse roles from comma-separated string if needed
        $allowedRoles = [];
        foreach ($roles as $role) {
            // Handle comma-separated roles like "admin,staff"
            $parsedRoles = array_map('trim', explode(',', $role));
            $allowedRoles = array_merge($allowedRoles, $parsedRoles);
        }
        $allowedRoles = array_unique($allowedRoles);

        // Normalize user role (trim and lowercase for comparison)
        $userRole = trim(strtolower($user->role ?? ''));
        $normalizedAllowedRoles = array_map(function($role) {
            return trim(strtolower($role));
        }, $allowedRoles);

        // Check if user has any of the required roles
        if (!in_array($userRole, $normalizedAllowedRoles)) {
            // If customer is trying to access dashboard/appointments, redirect them to customer appointments
            if ($user->isCustomer() && str_starts_with($request->path(), 'dashboard/appointments')) {
                return redirect()->route('customer.appointments.index');
            }
            
            abort(403, 'Unauthorized access. User role: "' . $user->role . '", Required roles: ' . implode(', ', $allowedRoles));
        }

        // Admin users can always proceed without staff profile check
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Staff users need staff profile and additional restrictions
        if ($user->isStaff()) {
            return $this->handleStaffRestrictions($request, $next);
        }

        // Handle customer-specific restrictions
        if ($user->isCustomer()) {
            return $this->handleCustomerRestrictions($request, $next, $user);
        }

        // For other roles, allow access if role matches
        return $next($request);
    }

    /**
     * Handle staff-specific restrictions
     */
    private function handleStaffRestrictions(Request $request, Closure $next): Response
    {
        /** @var User $user */
        $user = Auth::user();
        $staff = $user->staff;
        
        if (!$staff) {
            abort(403, 'Staff profile not found.');
        }

        // Apply staff filters
        $this->applyStaffFilters($request, $staff);
        
        return $next($request);
    }

    /**
     * Handle customer-specific restrictions
     */
    private function handleCustomerRestrictions(Request $request, Closure $next, $user): Response
    {
        // Customers can only access their own data
        if ($request->routeIs('customer.dashboard') || 
            $request->routeIs('appointments.*') ||
            $request->routeIs('profile.*')) {
            
            // For appointment-related routes, ensure customer can only access their own appointments
            if ($request->routeIs('appointments.*')) {
                $this->filterCustomerAppointments($request, $user);
            }
            
            return $next($request);
        }

        // Allow access to customer-specific routes
        if ($request->routeIs('customer.*')) {
            return $next($request);
        }

        // Redirect customers trying to access dashboard appointments to their own appointments page
        if (str_starts_with($request->path(), 'dashboard/appointments')) {
            return redirect()->route('customer.appointments.index');
        }

        // Deny access to admin routes
        if (str_starts_with($request->path(), 'dashboard') && 
            !$request->routeIs('dashboard.index')) {
            abort(403, 'Customers do not have access to the admin dashboard.');
        }

        return $next($request);
    }

    /**
     * Apply staff-specific filters to the request
     */
    private function applyStaffFilters(Request $request, $staff): void
    {
        // Store staff ID for later use
        $request->attributes->set('staff_id', $staff->id);
        
        // For appointment-related routes, staff can only see their own appointments
        if ($request->routeIs('dashboard.appointments.*') || 
            $request->routeIs('appointments.*')) {
            $this->filterStaffAppointments($request, $staff);
        }
        
        // Staff cannot access user management
        if ($request->routeIs('dashboard.users.*')) {
            abort(403, 'Staff cannot access user management.');
        }
        
        // Staff cannot access certain reports
        if ($request->routeIs('dashboard.reports.*')) {
            $allowedReports = ['appointments', 'revenue'];
            $currentRoute = $request->route()->getName();
            
            foreach ($allowedReports as $allowed) {
                if (str_contains($currentRoute, $allowed)) {
                    return; // Allow access
                }
            }
            
            abort(403, 'Staff cannot access this report.');
        }
    }

    /**
     * Filter appointments for staff
     */
    private function filterStaffAppointments(Request $request, $staff): void
    {
        // For index/show actions, staff can only see their own appointments
        if ($request->routeIs('*.index') || $request->routeIs('*.show')) {
            $request->merge(['staff_id' => $staff->id]);
        }
        
        // For update/delete actions, check if appointment belongs to staff
        if ($request->routeIs('*.update') || $request->routeIs('*.destroy') || 
            $request->routeIs('*.status')) {
            $appointmentParam = $request->route('appointment');
            
            // Handle both model binding (Appointment instance) and ID (string/int)
            if ($appointmentParam instanceof Appointment) {
                $appointment = $appointmentParam;
            } elseif ($appointmentParam) {
                // Try to find appointment by ID
                try {
                    $appointment = Appointment::findOrFail($appointmentParam);
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    // If appointment not found, let route model binding handle the 404
                    // Don't interfere here
                    $appointment = null;
                }
            } else {
                $appointment = null;
            }
            
            // Only check ownership if we have a valid appointment
            if ($appointment && $appointment->staff_id !== $staff->id) {
                abort(403, 'You can only modify your own appointments.');
            }
        }
    }

    /**
     * Filter appointments for customers
     */
    private function filterCustomerAppointments(Request $request, $user): void
    {
        // Ensure the customer has a customer record
        if (!$user->customer) {
            abort(403, 'Customer profile not found.');
        }

        // For index/show actions, customer can only see their own appointments
        if ($request->routeIs('appointments.index') || $request->routeIs('appointments.show')) {
            $request->merge(['customer_id' => $user->customer->id]);
        }
        
        // For update/cancel actions, check if appointment belongs to customer
        if ($request->routeIs('appointments.update') || 
            $request->routeIs('appointments.destroy') || 
            $request->routeIs('appointments.cancel')) {
            
            $appointmentId = $request->route('appointment');
            if ($appointmentId) {
                $appointment = Appointment::findOrFail($appointmentId);
                if ($appointment->customer_id !== $user->customer->id) {
                    abort(403, 'You can only modify your own appointments.');
                }
            }
        }
    }
}