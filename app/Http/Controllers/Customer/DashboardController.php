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
        // Get all active staff members with their user information, active schedules, and schedule details
        $staffMembers = Staff::with(['user', 'schedules' => function($query) {
                $query->where('is_active', true)
                      ->orderBy('day_of_week')
                      ->select('staff_id', 'day_of_week', 'start_time', 'end_time', 'max_appointments', 'is_active');
            }])
            ->whereHas('user', function($query) {
                $query->where('is_active', true);
            })
            ->whereHas('schedules', function($query) {
                $query->where('is_active', true);
            })
            ->get()
            ->map(function($staff) {
                $scheduleInfo = $staff->schedules->map(function($schedule) {
                    return [
                        'day' => $schedule->day_of_week,
                        'day_name' => ucfirst($schedule->day_of_week),
                        'start_time' => \Carbon\Carbon::parse($schedule->start_time)->format('g:i A'),
                        'end_time' => \Carbon\Carbon::parse($schedule->end_time)->format('g:i A'),
                        'max_appointments' => $schedule->max_appointments,
                    ];
                });

                return [
                    'id' => $staff->id,
                    'name' => $staff->user->full_name ?? 'Staff Member',
                    'specialty' => $staff->formatted_specialty ?? 'All Services',
                    'gender' => $staff->user->gender ? $staff->user->formatted_gender : null,
                    'color_code' => $staff->color_code ?? '#3B82F6',
                    'schedules' => $scheduleInfo,
                    'schedule_summary' => $this->generateScheduleSummary($scheduleInfo),
                ];
            })
            ->values(); // Reset array keys

        return view('customer.staff', compact('staffMembers'));
    }

    /**
     * Generate a human-readable schedule summary
     */
    private function generateScheduleSummary($schedules)
    {
        if ($schedules->isEmpty()) {
            return 'No schedule available';
        }

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $scheduleMap = [];
        
        foreach ($schedules as $schedule) {
            $scheduleMap[$schedule['day']] = $schedule;
        }

        // Find consecutive days with same hours
        $groups = [];
        $currentGroup = null;
        
        foreach ($days as $day) {
            if (isset($scheduleMap[$day])) {
                $schedule = $scheduleMap[$day];
                $timeSlot = $schedule['start_time'] . ' - ' . $schedule['end_time'];
                
                if ($currentGroup && $currentGroup['time_slot'] === $timeSlot) {
                    $currentGroup['days'][] = $schedule['day_name'];
                } else {
                    if ($currentGroup) {
                        $groups[] = $currentGroup;
                    }
                    $currentGroup = [
                        'time_slot' => $timeSlot,
                        'days' => [$schedule['day_name']]
                    ];
                }
            } else {
                if ($currentGroup) {
                    $groups[] = $currentGroup;
                    $currentGroup = null;
                }
            }
        }
        
        if ($currentGroup) {
            $groups[] = $currentGroup;
        }

        // Format the groups into readable text
        $summary = [];
        foreach ($groups as $group) {
            if (count($group['days']) === 1) {
                $summary[] = $group['days'][0] . ': ' . $group['time_slot'];
            } elseif (count($group['days']) === 2) {
                $summary[] = $group['days'][0] . ' & ' . $group['days'][1] . ': ' . $group['time_slot'];
            } else {
                $summary[] = $group['days'][0] . ' - ' . end($group['days']) . ': ' . $group['time_slot'];
            }
        }

        return implode(', ', $summary);
    }

    public function showStaff($id)
    {
        $staff = Staff::with(['user', 'schedules' => function($query) {
                $query->where('is_active', true)
                      ->orderBy('day_of_week');
            }])->findOrFail($id);
        
        if (!$staff->user || !$staff->user->is_active) {
            abort(404, 'Staff member not found.');
        }

        // Check if staff has active schedules
        if (!$staff->schedules()->where('is_active', true)->exists()) {
            abort(404, 'Staff member not available for booking.');
        }

        // Format schedules for display
        $formattedSchedules = $staff->schedules->map(function($schedule) {
            return [
                'day_name' => ucfirst($schedule->day_of_week),
                'start_time' => \Carbon\Carbon::parse($schedule->start_time)->format('g:i A'),
                'end_time' => \Carbon\Carbon::parse($schedule->end_time)->format('g:i A'),
                'max_appointments' => $schedule->max_appointments,
                'formatted_time_range' => $schedule->formatted_time_range,
                'formatted_limit' => $schedule->formatted_limit,
            ];
        })->keyBy('day_name');

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

        return view('customer.staff-show', compact('staff', 'services', 'formattedSchedules'));
    }
}