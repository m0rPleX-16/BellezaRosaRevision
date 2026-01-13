<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Customer;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Ensure the customer record exists
        if (!$user->customer) {
            // Create a customer record if it doesn't exist
            $customer = Customer::create([
                'user_id' => $user->id,
                'full_name' => $user->full_name ?? $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '0000000000',
                'gender' => 'other',
                'birth_date' => now()->subYears(18)->format('Y-m-d'),
                'total_visits' => 0,
                'total_spent' => 0,
            ]);
            
            // Refresh the user's customer relationship
            $user->load('customer');
        }

        $appointments = $user->customer->appointments()
            ->with(['service', 'staff'])
            ->orderBy('start_datetime', 'desc')
            ->get();

        $services = Service::all();
        $staffMembers = Staff::all();

        return view('customer.dashboard', compact('appointments', 'services', 'staffMembers'));
    }

    public function staff()
    {
        // Get all active staff members with their user information
        $staffMembers = Staff::with('user')
            ->whereHas('user', function($query) {
                $query->where('is_active', true);
            })
            ->get()
            ->map(function($staff) {
                return [
                    'id' => $staff->id,
                    'name' => $staff->user->full_name ?? 'Staff Member',
                    'specialty' => $staff->formatted_specialty ?? 'All Services',
                    'gender' => $staff->user->gender ? $staff->user->formatted_gender : null,
                    'color_code' => $staff->color_code ?? '#3B82F6',
                ];
            })
            ->values(); // Reset array keys

        return view('customer.staff', compact('staffMembers'));
    }

    public function showStaff($id)
    {
        $staff = Staff::with('user')->findOrFail($id);
        
        if (!$staff->user || !$staff->user->is_active) {
            abort(404, 'Staff member not found.');
        }

        // Get services based on staff specialty
        $staffSpecialty = $staff->specialty ?? 'all';
        
        // Map staff specialty to allowed category specialties
        $allowedBySpecialty = [
            'hair' => ['hair', 'all'],
            'nail' => ['nail', 'all'],
            'spa' => ['spa', 'all'],
            'hair_nail' => ['hair', 'nail', 'all'],
            'hair_spa' => ['hair', 'spa', 'all'],
            'nail_spa' => ['nail', 'spa', 'all'],
            'all' => ['hair', 'nail', 'spa', 'all'],
        ];

        $allowed = $allowedBySpecialty[$staffSpecialty] ?? ['hair', 'nail', 'spa', 'all'];

        // Get services that match the staff's specialty
        $services = Service::where('is_active', true)
            ->whereHas('category', function ($q) use ($allowed) {
                $q->whereIn('specialty', $allowed);
            })
            ->with('category')
            ->get()
            ->groupBy(function ($service) {
                return $service->category->name ?? 'Uncategorized';
            });

        return view('customer.staff-show', compact('staff', 'services'));
    }
}