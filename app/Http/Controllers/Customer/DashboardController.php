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
}