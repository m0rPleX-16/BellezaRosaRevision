<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Payment;
use App\Models\SalonSetting;
use App\Helpers\ToastHelper;
use App\Notifications\AppointmentStatusUpdated;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class AppointmentController extends Controller
{
    /**
     * Helper method to check if a staff member is available for a given time slot.
     */
    private function isStaffAvailable($staffId, $startDateTime, $durationMinutes, $excludeAppointmentId = null)
    {
        $start = Carbon::parse($startDateTime);
        $end = $start->copy()->addMinutes($durationMinutes);

        // Check for overlapping appointments
        $query = Appointment::where('staff_id', $staffId)
            ->where(function ($q) use ($start, $end) {
                // New appointment starts during an existing one
                $q->whereBetween('start_datetime', [$start, $end->copy()->subMinute()])
                    // Existing appointment starts during the new one
                    ->orWhereBetween('end_datetime', [$start->copy()->addMinute(), $end])
                    // New appointment completely encompasses an existing one
                    ->orWhere(function ($q2) use ($start, $end) {
                    $q2->where('start_datetime', '<=', $start)
                        ->where('end_datetime', '>=', $end);
                });
            })
            ->whereNotIn('status', ['cancelled', 'no_show']); // Ignore cancelled/no-show

        if ($excludeAppointmentId) {
            $query->where('id', '!=', $excludeAppointmentId);
        }

        return !$query->exists();
    }

    /**
     * Display a listing of the customer's appointments.
     *
     * @return \Illuminate\View\View
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function customerIndex()
    {
        $customer = auth()->user()->customer;

        // Get upcoming appointments (scheduled, confirmed, in_progress - future appointments)
        $upcomingAppointments = $customer->appointments()
            ->with(['service', 'staff'])
            ->whereIn('status', ['scheduled', 'confirmed', 'in_progress'])
            ->where('start_datetime', '>=', now())
            ->orderBy('start_datetime', 'asc')
            ->get();

        // Get past appointments (completed or past dates, excluding cancelled/failed/no_show)
        $pastAppointments = $customer->appointments()
            ->with(['service', 'staff'])
            ->where(function ($query) {
                $query->where('status', 'completed')
                    ->orWhere(function ($q) {
                        // Past dates with active statuses
                        $q->where('start_datetime', '<', now())
                          ->whereIn('status', ['scheduled', 'confirmed', 'in_progress', 'completed']);
                    });
            })
            ->whereNotIn('status', ['cancelled', 'failed', 'no_show'])
            ->orderBy('start_datetime', 'desc')
            ->paginate(10);

        // Get cancelled appointments (cancelled status)
        $cancelledAppointments = $customer->appointments()
            ->with(['service', 'staff'])
            ->where('status', 'cancelled')
            ->orderBy('start_datetime', 'desc')
            ->paginate(10);

        // Get failed appointments (failed status)
        $failedAppointments = $customer->appointments()
            ->with(['service', 'staff'])
            ->where('status', 'failed')
            ->orderBy('start_datetime', 'desc')
            ->paginate(10);

        // Get no-show appointments (no_show status)
        $noShowAppointments = $customer->appointments()
            ->with(['service', 'staff'])
            ->where('status', 'no_show')
            ->orderBy('start_datetime', 'desc')
            ->paginate(10);

        return view('customer.appointments.index', compact(
            'upcomingAppointments',
            'pastAppointments',
            'cancelledAppointments',
            'failedAppointments',
            'noShowAppointments'
        ));
    }

    public function index()
    {
        // Start with the base query
        $query = Appointment::with(['customer', 'service', 'staff.user', 'payment']);

        // Staff can only view their own appointments
        if (auth()->user()->isStaff() && auth()->user()->staff) {
            $query->where('staff_id', auth()->user()->staff->id);
        }

        // 1. Search filter (customer name, phone, or service name)
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($cq) use ($search) {
                    $cq->where('full_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })
                    ->orWhereHas('service', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // 2. Status filter
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        // 3. Date filter (only the date part of start_datetime)
        if ($date = request('date')) {
            $query->whereDate('start_datetime', $date);
        }

        // Default sorting: newest first
        // Filter out appointments with missing relationships (orphaned data)
        $appointments = $query->whereHas('customer')
            ->whereHas('service')
            ->orderBy('start_datetime', 'desc')
            ->paginate(20);

        // Keep the query string in pagination links
        $appointments->appends(request()->query());

        // Calculate statistics
        $totalAppointments = Appointment::count();
        $todayAppointments = Appointment::whereDate('start_datetime', today())->count();

        // Completed appointments this month
        $completedThisMonth = Appointment::where('status', 'completed')
            ->whereMonth('start_datetime', now()->month)
            ->whereYear('start_datetime', now()->year)
            ->count();

        // Get data for modal
        $customers = Customer::all();
        $services = Service::where('is_active', true)->get();
        $staff = Staff::with('user')->get();

        // Get salon settings
        $salonSettings = SalonSetting::getSettings();

        // Use salon settings or defaults
        $openingTime = $salonSettings->opening_time;
        $closingTime = $salonSettings->closing_time;
        $maxDaysAhead = $salonSettings->max_days_book_ahead;
        $slotInterval = $salonSettings->slot_interval_minutes;

        return view('dashboard.appointments.index', compact(
            'appointments',
            'totalAppointments',
            'todayAppointments',
            'completedThisMonth',
            'customers',
            'services',
            'staff',
            'openingTime',
            'closingTime',
            'maxDaysAhead',
            'slotInterval'
        ));
    }

    public function create()
    {
        $customers = Customer::all();
        $staff = Staff::with('user')->get();
        $services = Service::where('is_active', true)->with('category')->get();
        $servicesByCategory = $services->groupBy('category.name');

        // Get pre-filled values from query parameters (for customer booking from staff page)
        $prefilledServiceId = request()->get('service_id');
        $prefilledStaffId = request()->get('staff_id');
        
        // Fetch the actual service and staff objects if IDs are provided
        $prefilledService = null;
        $prefilledStaff = null;
        
        if ($prefilledServiceId) {
            $prefilledService = Service::find($prefilledServiceId);
        }
        
        if ($prefilledStaffId) {
            $prefilledStaff = Staff::with('user')->find($prefilledStaffId);
        }

        // Check if the request is from customer or admin
        if (request()->routeIs('customer.*')) {
            return view('customer.appointments.create', compact(
                'customers',
                'services',
                'staff',
                'servicesByCategory',
                'prefilledServiceId',
                'prefilledStaffId',
                'prefilledService',
                'prefilledStaff'
            ));
        }

        // Default to admin dashboard view
        return view('dashboard.appointments.create', compact(
            'customers',
            'services',
            'staff',
            'servicesByCategory'
        ));
    }

    public function show(Appointment $appointment)
    {
        // Load relationships
        $appointment->load(['customer', 'service', 'staff.user', 'payment']);
        
        // Check if this is a customer route - ensure customer can only view their own appointments
        if (request()->routeIs('customer.*')) {
            $customer = auth()->user()->customer;
            
            if (!$customer || $appointment->customer_id !== $customer->id) {
                abort(403, 'You can only view your own appointments.');
            }
            
            return view('customer.appointments.show', compact('appointment'));
        }
        
        // For admin/staff routes, check permissions
        if (auth()->user()->isStaff() && $appointment->staff_id !== auth()->user()->staff->id) {
            abort(403, 'You can only view your own appointments.');
        }
        
        return view('dashboard.appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $customers = Customer::all();
        $staff = Staff::with('user')->get();
        $services = Service::where('is_active', true)->with('category')->get();
        $servicesByCategory = $services->groupBy('category.name');

        return view('dashboard.appointments.edit', compact(
            'appointment',
            'customers',
            'services',
            'staff',
            'servicesByCategory'
        ));
    }
    public function getServicesByStaff($staffId)
    {
        $staff = Staff::findOrFail($staffId);

        $services = Service::whereHas('category', function ($query) use ($staff) {
            $query->where('specialty', $staff->specialty)
                ->orWhere('specialty', 'both');
        })
            ->where('is_active', true)
            ->with('category')
            ->get();

        return response()->json([
            'services' => $services,
            'grouped_services' => $services->groupBy('category.name')
        ]);
    }
    public function store(Request $request)
    {
        // Handle both start_datetime (combined) and appointment_date + appointment_time (separate)
        $startDatetime = null;
        
        if ($request->has('start_datetime') && $request->start_datetime) {
            $startDatetime = $request->start_datetime;
        } elseif ($request->has('appointment_date') && $request->has('appointment_time') && $request->appointment_time) {
            // Combine date and time
            $time = $request->appointment_time;
            // Ensure time has seconds (H:i:s format)
            if (strlen($time) === 5) { // H:i format (e.g., "14:30")
                $time .= ':00'; // Add seconds
            }
            $startDatetime = $request->appointment_date . ' ' . $time;
        }
        
        $request->merge(['start_datetime' => $startDatetime]);
        
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
            'start_datetime' => 'required|date|after_or_equal:today',
        ]);

        // Get salon settings
        $salonSettings = SalonSetting::getSettings();

        // Parse start datetime
        $startDateTime = Carbon::parse($request->start_datetime);
        $startTime = $startDateTime->format('H:i:s');

        // Validate business hours
        if ($startTime < $salonSettings->opening_time || $startTime > $salonSettings->closing_time) {
            return back()->withErrors([
                'start_datetime' => 'Appointments must be within business hours: ' .
                    Carbon::createFromFormat('H:i:s', $salonSettings->opening_time)->format('g:i A') . ' - ' .
                    Carbon::createFromFormat('H:i:s', $salonSettings->closing_time)->format('g:i A')
            ]);
        }

        // Validate max days ahead
        $maxDate = now()->addDays($salonSettings->max_days_book_ahead);
        if ($startDateTime->gt($maxDate)) {
            return back()->withErrors([
                'start_datetime' => "Appointments can only be booked up to {$salonSettings->max_days_book_ahead} days in advance."
            ]);
        }

        // Get service duration
        $service = Service::findOrFail($request->service_id);
        $duration = $service->duration_minutes;

        // Minimum duration check
        if ($duration < 30) {
            return back()->withErrors([
                'service_id' => 'Service duration must be at least 30 minutes.'
            ]);
        }

        // Check staff availability (proper overlap detection)
        if (!$this->isStaffAvailable($request->staff_id, $request->start_datetime, $duration)) {
            return back()->withErrors([
                'start_datetime' => 'Staff is not available for the selected time slot. Please choose another time.'
            ]);
        }

        // Calculate end time
        $endDateTime = $startDateTime->copy()->addMinutes($duration);

        // Ensure appointment doesn't exceed closing time
        $closingTimeToday = Carbon::parse($startDateTime->format('Y-m-d') . ' ' . $salonSettings->closing_time);
        if ($endDateTime->gt($closingTimeToday)) {
            return back()->withErrors([
                'start_datetime' => 'Appointment would end after closing time. Please select an earlier time.'
            ]);
        }

        Appointment::create([
            'customer_id' => $request->customer_id,
            'service_id' => $request->service_id,
            'staff_id' => $request->staff_id,
            'start_datetime' => $request->start_datetime,
            'end_datetime' => $endDateTime,
            'total_amount' => $service->price_premium ?? $service->price_regular,
            'status' => 'scheduled',
            'notes' => $request->notes ?? null,
        ]);

        // Redirect based on the route - customer or admin/staff
        if (request()->routeIs('customer.*')) {
            return redirect()->route('customer.appointments.index')
                ->with('success', 'Appointment booked successfully!');
        }

        return redirect()->route('dashboard.appointments.index')
            ->with('success', 'Appointment created successfully!');
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
            'start_datetime' => 'required|date',
        ]);

        $service = Service::findOrFail($request->service_id);
        $duration = $service->duration_minutes;

        // Minimum duration check (30 minutes)
        if ($duration < 30) {
            return back()->withErrors([
                'service_id' => 'Service duration must be at least 30 minutes.'
            ]);
        }

        // Get salon settings for business hours validation
        $salonSettings = SalonSetting::getSettings();
        $startDateTime = Carbon::parse($request->start_datetime);
        $startTime = $startDateTime->format('H:i:s');

        // Validate business hours
        if ($startTime < $salonSettings->opening_time || $startTime > $salonSettings->closing_time) {
            return back()->withErrors([
                'start_datetime' => 'Appointments must be within business hours: ' .
                    Carbon::createFromFormat('H:i:s', $salonSettings->opening_time)->format('g:i A') . ' - ' .
                    Carbon::createFromFormat('H:i:s', $salonSettings->closing_time)->format('g:i A')
            ]);
        }

        // Check staff availability, excluding the current appointment itself
        if (!$this->isStaffAvailable($request->staff_id, $request->start_datetime, $duration, $appointment->id)) {
            return back()->withErrors([
                'start_datetime' => 'Staff is not available for the selected time slot. Please choose another time.'
            ]);
        }

        // Calculate end time and ensure it doesn't exceed closing time
        $endDateTime = $startDateTime->copy()->addMinutes($duration);
        $closingTimeToday = Carbon::parse($startDateTime->format('Y-m-d') . ' ' . $salonSettings->closing_time);
        if ($endDateTime->gt($closingTimeToday)) {
            return back()->withErrors([
                'start_datetime' => 'Appointment would end after closing time. Please select an earlier time.'
            ]);
        }

        $startDateTime = Carbon::parse($request->start_datetime);
        $endDateTime = $startDateTime->copy()->addMinutes($duration);

        $appointment->update([
            'customer_id' => $request->customer_id,
            'service_id' => $request->service_id,
            'staff_id' => $request->staff_id,
            'start_datetime' => $request->start_datetime,
            'end_datetime' => $endDateTime,
            'total_amount' => $service->price_premium ?? $service->price_regular,
        ]);

        return redirect()->route('dashboard.appointments.index')
            ->with('success', 'Appointment updated successfully!');
    }

    /**
     * Check staff availability via AJAX
     */
    public function checkAvailability(Request $request)
    {
        try {
            $request->validate([
                'staff_id' => 'required|exists:staff,id',
                'date' => 'required|date',
                'service_id' => 'nullable|exists:services,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        }

        try {
            $staffId = $request->staff_id;
            $selectedDate = $request->date;
            $serviceId = $request->service_id;
            
            // Get salon settings
            $salonSettings = SalonSetting::getSettings();
            // Ensure times are strings (TIME columns might return as objects)
            $openingTime = is_string($salonSettings->opening_time) 
                ? $salonSettings->opening_time 
                : (string)$salonSettings->opening_time ?? '09:00:00';
            $closingTime = is_string($salonSettings->closing_time) 
                ? $salonSettings->closing_time 
                : (string)$salonSettings->closing_time ?? '20:00:00';
            $interval = $salonSettings->slot_interval_minutes ?? 30;
            
            // Ensure time format is H:i:s
            if (strlen($openingTime) === 5) {
                $openingTime .= ':00';
            }
            if (strlen($closingTime) === 5) {
                $closingTime .= ':00';
            }
            
            // Get service duration if service_id is provided
            $serviceDuration = 60; // Default 60 minutes
            if ($serviceId) {
                $service = Service::find($serviceId);
                if ($service) {
                    $serviceDuration = max($service->duration_minutes, 30); // Minimum 30 minutes
                }
            }
            
            // Get existing appointments for the selected staff and date
            $appointments = Appointment::where('staff_id', $staffId)
                ->whereDate('start_datetime', $selectedDate)
                ->whereNotIn('status', ['cancelled', 'no_show'])
                ->get(['start_datetime', 'end_datetime']);
            
            // Generate time slots based on service duration
            $start = Carbon::parse($selectedDate . ' ' . $openingTime);
            $end = Carbon::parse($selectedDate . ' ' . $closingTime);
            $now = Carbon::now();
            $isToday = Carbon::parse($selectedDate)->isToday();
            
            $timeSlots = [];
            
            while ($start->lt($end)) {
                // Skip past times if the selected date is today
                if ($isToday && $start->lt($now)) {
                    $start->addMinutes($interval);
                    continue;
                }
                
                // Calculate when the appointment would end
                $appointmentEnd = $start->copy()->addMinutes($serviceDuration);
                
                // Skip if appointment would end after closing time
                if ($appointmentEnd->gt($end)) {
                    $start->addMinutes($interval);
                    continue;
                }
                
                $isAvailable = true;
                
                // Check if the time slot conflicts with existing appointments
                foreach ($appointments as $appt) {
                    $apptStart = Carbon::parse($appt->start_datetime);
                    $apptEnd = Carbon::parse($appt->end_datetime);
                    
                    // Check for overlap: new appointment overlaps with existing one
                    if ($start->lt($apptEnd) && $appointmentEnd->gt($apptStart)) {
                        $isAvailable = false;
                        break;
                    }
                }
                
                if ($isAvailable) {
                    $timeSlots[] = [
                        'time' => $start->format('H:i:s'),
                        'formatted_time' => $start->format('g:i A')
                    ];
                }
                
                $start->addMinutes($interval);
            }
            
            return response()->json([
                'available_times' => $timeSlots
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in checkAvailability: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'error' => 'Server error',
                'message' => 'An error occurred while checking availability. Please try again.'
            ], 500);
        }
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        // Staff can only update their own appointments
        if (auth()->user()->isStaff() && auth()->user()->staff && $appointment->staff_id !== auth()->user()->staff->id) {
            abort(403, 'You can only update your own appointments.');
        }

        $request->validate([
            'status' => 'required|in:scheduled,confirmed,in_progress,completed,cancelled,no_show,failed'
        ]);

        $currentDateTime = now();
        $appointmentDate = $appointment->start_datetime;
        $oldStatus = $appointment->status;
        $newStatus = $request->status;

        // Prevent marking as completed before appointment date
        if ($newStatus === 'completed' && $currentDateTime->lt($appointmentDate)) {
            $formattedDate = $appointmentDate->format('M j, Y g:i A');
            return back()->withErrors([
                'status' => "Cannot mark as completed before the appointment date ($formattedDate)."
            ]);
        }

        // Prevent marking as in_progress before appointment date
        if ($newStatus === 'in_progress' && $currentDateTime->lt($appointmentDate)) {
            $formattedDate = $appointmentDate->format('M j, Y g:i A');
            return back()->withErrors([
                'status' => "Cannot start appointment before the scheduled date ($formattedDate)."
            ]);
        }

        // Update the appointment status
        $appointment->update(['status' => $newStatus]);

        // Notify customer if status changed and customer has a user account
        if ($oldStatus !== $newStatus && $appointment->customer && $appointment->customer->user) {
            $customerUser = $appointment->customer->user;
            
            // Create notification message based on status
            $statusMessages = [
                'scheduled' => 'Your appointment has been scheduled.',
                'confirmed' => 'Your appointment has been confirmed.',
                'in_progress' => 'Your appointment has started.',
                'completed' => 'Your appointment has been completed. Thank you!',
                'cancelled' => 'Your appointment has been cancelled.',
                'no_show' => 'You were marked as a no-show for your appointment.',
                'failed' => 'Your appointment was marked as failed.',
            ];
            
            $message = $statusMessages[$newStatus] ?? 'Your appointment status has been updated.';
            
            // Send notification using the AppointmentStatusUpdated notification class
            $customerUser->notify(new AppointmentStatusUpdated($appointment, $newStatus, $message));
        }

        return back()->with('success', 'Appointment status updated successfully! The customer has been notified.');
    }
    // Add these methods to AppointmentController.php
    public function showCancelForm(Appointment $appointment)
    {
        return view('dashboard.appointments.cancel', compact('appointment'));
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        // For customer cancellations, make reason optional and default status to cancelled
        if (request()->routeIs('customer.*')) {
            $request->validate([
                'cancellation_reason' => 'nullable|string|max:500',
                'status' => 'nullable|in:cancelled,failed'
            ]);
            
            $cancellationReason = $request->cancellation_reason ?? 'Cancelled by customer';
            $status = $request->status ?? 'cancelled';
        } else {
            // For admin/staff cancellations, require reason
            $request->validate([
                'cancellation_reason' => 'required|string|max:500',
                'status' => 'required|in:cancelled,failed',
                'refund_amount' => 'nullable|numeric|min:0',
                'refund_method' => 'nullable|in:cash,gcash,bank_transfer'
            ]);
            
            $cancellationReason = $request->cancellation_reason;
            $status = $request->status;
        }

        // Use the helper method from the model
        if ($status === 'cancelled') {
            $appointment->cancel($cancellationReason, auth()->user());
        } else {
            $appointment->markAsFailed($cancellationReason);
        }

        // Handle refund if applicable (only for admin/staff)
        if (!$request->routeIs('customer.*') && $request->refund_amount > 0 && $appointment->payment) {
            // Create refund record
            Payment::create([
                'appointment_id' => $appointment->id,
                'customer_id' => $appointment->customer_id,
                'amount' => -$request->refund_amount,
                'method' => $request->refund_method,
                'status' => 'refunded',
                'notes' => "Refund for {$status} appointment: " . $cancellationReason,
                'paid_at' => now()
            ]);
        }

        // Redirect based on route
        if (request()->routeIs('customer.*')) {
            return redirect()->route('customer.appointments.index')
                ->with('success', 'Appointment has been cancelled successfully.');
        }

        return redirect()->route('dashboard.appointments.index')
            ->with('success', "Appointment has been {$status} successfully.");
    }

}