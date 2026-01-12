<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Appointment;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user has any of the required roles
        foreach ($roles as $role) {
            if ($user->role === $role) {
                // Apply role-specific restrictions
                return $this->handleRoleRestrictions($request, $next, $user);
            }
        }

        // If no roles matched and user is admin, allow access
        if ($user->isAdmin()) {
            return $next($request);
        }

        abort(403, 'You do not have permission to access this page.');
    }

    /**
     * Handle role-specific restrictions
     */
    private function handleRoleRestrictions(Request $request, Closure $next, $user)
    {
        if ($user->isStaff()) {
            return $this->handleStaffRestrictions($request, $next);
        }

        if ($user->isCustomer()) {
            return $this->handleCustomerRestrictions($request, $next, $user);
        }

        return $next($request);
    }

    /**
     * Handle staff-specific restrictions
     */
    private function handleStaffRestrictions(Request $request, Closure $next): Response
    {
        $staff = auth()->user()->staff;
        
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
            $appointmentId = $request->route('appointment');
            if ($appointmentId) {
                $appointment = Appointment::findOrFail($appointmentId);
                if ($appointment->staff_id !== $staff->id) {
                    abort(403, 'You can only modify your own appointments.');
                }
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