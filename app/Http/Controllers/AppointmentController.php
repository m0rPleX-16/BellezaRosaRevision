<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentAddon;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Payment;
use App\Models\SalonSetting;
use App\Models\StaffSchedule;
use App\Helpers\ToastHelper;
use App\Notifications\AppointmentStatusUpdated;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $customer = $user->customer;

        // Get upcoming appointments (scheduled, confirmed, in_progress - future appointments)
        $upcomingAppointments = $customer->appointments()
            ->with(['service', 'staff', 'addons'])
            ->whereIn('status', ['scheduled', 'confirmed', 'in_progress'])
            ->where('start_datetime', '>=', now())
            ->orderBy('start_datetime', 'asc')
            ->get();

        // Get past appointments (completed or past dates, excluding cancelled/failed/no_show)
        $pastAppointments = $customer->appointments()
            ->with(['service', 'staff', 'addons'])
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
            ->with(['service', 'staff', 'addons'])
            ->where('status', 'cancelled')
            ->orderBy('start_datetime', 'desc')
            ->paginate(10);

        // Get failed appointments (failed status)
        $failedAppointments = $customer->appointments()
            ->with(['service', 'staff', 'addons'])
            ->where('status', 'failed')
            ->orderBy('start_datetime', 'desc')
            ->paginate(10);

        // Get no-show appointments (no_show status)
        $noShowAppointments = $customer->appointments()
            ->with(['service', 'staff', 'addons'])
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
        $query = Appointment::with(['customer', 'service', 'staff.user', 'payment', 'addons']);

        // Staff can only view their own appointments
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isStaff() && $user->staff) {
            $query->where('staff_id', $user->staff->id);
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

        // 4. Date range filters (same as dashboard)
        $dateRange = request('date_range');
        if ($dateRange) {
            switch ($dateRange) {
                case 'today':
                    $query->whereDate('start_datetime', today());
                    break;
                    
                case 'yesterday':
                    $query->whereDate('start_datetime', today()->subDay());
                    break;
                    
                case 'this_week':
                    $query->whereBetween('start_datetime', [
                        today()->startOfWeek(),
                        today()->endOfWeek()
                    ]);
                    break;
                    
                case 'last_week':
                    $query->whereBetween('start_datetime', [
                        today()->subWeek()->startOfWeek(),
                        today()->subWeek()->endOfWeek()
                    ]);
                    break;
                    
                case 'this_month':
                    $query->whereMonth('start_datetime', now()->month)
                         ->whereYear('start_datetime', now()->year);
                    break;
                    
                case 'last_month':
                    $query->whereMonth('start_datetime', now()->subMonth()->month)
                         ->whereYear('start_datetime', now()->subMonth()->year);
                    break;
                    
                case 'custom':
                    if ($customDate = request('custom_date')) {
                        $query->whereMonth('start_datetime', Carbon::createFromFormat('Y-m', $customDate)->month)
                             ->whereYear('start_datetime', Carbon::createFromFormat('Y-m', $customDate)->year);
                    }
                    break;
                    
                case 'custom_range':
                    $dateFrom = request('date_from');
                    $dateTo = request('date_to');
                    if ($dateFrom && $dateTo) {
                        $query->whereBetween('start_datetime', [
                            Carbon::parse($dateFrom)->startOfDay(),
                            Carbon::parse($dateTo)->endOfDay()
                        ]);
                    }
                    break;
            }
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
        
        // For customer view, only show staff with active schedules
        if (request()->routeIs('customer.*')) {
            $staff = Staff::with('user')
                ->whereHas('schedules', function($query) {
                    $query->where('is_active', true);
                })
                ->get();
        } else {
            // For admin view, show all staff
            $staff = Staff::with('user')->get();
        }
        
        $services = Service::where('is_active', true)->with('category')->get();
        $servicesByCategory = $services->groupBy('category.name');

        // Get salon settings for admin create form
        $salonSettings = SalonSetting::getSettings();
        $openingTime = $salonSettings->opening_time;
        $closingTime = $salonSettings->closing_time;
        $maxDaysAhead = $salonSettings->max_days_book_ahead;
        $slotInterval = $salonSettings->slot_interval_minutes;

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

        // Default to admin dashboard view with all required variables
        return view('dashboard.appointments.create', compact(
            'customers',
            'services',
            'staff',
            'servicesByCategory',
            'openingTime',
            'closingTime',
            'maxDaysAhead',
            'slotInterval'
        ));
    }

    public function show(Appointment $appointment)
    {
        // Load relationships
        $appointment->load(['customer', 'service', 'staff.user', 'payment', 'addons']);
        
        // Check if this is a customer route - ensure customer can only view their own appointments
        if (request()->routeIs('customer.*')) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $customer = $user->customer;
            
            if (!$customer || $appointment->customer_id !== $customer->id) {
                abort(403, 'You can only view your own appointments.');
            }
            
            return view('customer.appointments.show', compact('appointment'));
        }
        
        // For admin/staff routes, check permissions
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Admin can view any appointment, staff can only view their own
        if ($user->isStaff() && $appointment->staff_id !== $user->staff->id) {
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

        // Validate addon data if present
        $addonRules = [];
        
        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'addon_service_') && !empty($value)) {
                $addonId = substr($key, 14);
                $nameKey = "addon_name_{$addonId}";
                $priceKey = "addon_price_{$addonId}";
                
                if ($value === 'custom') {
                    // Custom addon validation
                    $addonRules[$nameKey] = 'required|string|max:100';
                    $addonRules[$priceKey] = 'required|numeric|min:0|max:99999.99';
                } else {
                    // Service-based addon validation
                    $addonRules[$key] = 'required|exists:services,id';
                }
            }
        }
        
        if (!empty($addonRules)) {
            $request->validate($addonRules);
        }

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
        $baseDuration = $service->duration_minutes;

        // Minimum duration check
        if ($baseDuration < 30) {
            return back()->withErrors([
                'service_id' => 'Service duration must be at least 30 minutes.'
            ]);
        }

        // Check staff availability (proper overlap detection)
        if (!$this->isStaffAvailable($request->staff_id, $request->start_datetime, $baseDuration)) {
            return back()->withErrors([
                'start_datetime' => 'Staff is not available for the selected time slot. Please choose another time.'
            ]);
        }

        // Calculate end time
        $endDateTime = $startDateTime->copy()->addMinutes($baseDuration);

        // Ensure appointment doesn't exceed closing time
        $closingTimeToday = Carbon::parse($startDateTime->format('Y-m-d') . ' ' . $salonSettings->closing_time);
        if ($endDateTime->gt($closingTimeToday)) {
            return back()->withErrors([
                'start_datetime' => 'Appointment would end after closing time. Please select an earlier time.'
            ]);
        }

        // Validate staff schedule for the selected day
        $dayOfWeek = strtolower($startDateTime->format('l'));
        $staffHasSchedule = StaffSchedule::where('staff_id', $request->staff_id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->exists();

        if (!$staffHasSchedule) {
            return back()->withErrors([
                'start_datetime' => 'This staff member has not set their availability for ' . ucfirst($dayOfWeek) . '. Please select another date or choose a different staff member.'
            ]);
        }

        // Check if time slot is within staff's schedule
        $staffSchedules = StaffSchedule::where('staff_id', $request->staff_id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->get();

        $isWithinSchedule = false;
        foreach ($staffSchedules as $schedule) {
            $scheduleStartTime = $schedule->start_time instanceof Carbon 
                ? $schedule->start_time->format('H:i:s')
                : Carbon::parse($schedule->start_time)->format('H:i:s');
            $scheduleEndTime = $schedule->end_time instanceof Carbon 
                ? $schedule->end_time->format('H:i:s')
                : Carbon::parse($schedule->end_time)->format('H:i:s');
            
            $scheduleStart = Carbon::parse($startDateTime->format('Y-m-d') . ' ' . $scheduleStartTime);
            $scheduleEnd = Carbon::parse($startDateTime->format('Y-m-d') . ' ' . $scheduleEndTime);
            
            if ($startDateTime->gte($scheduleStart) && $endDateTime->lte($scheduleEnd)) {
                $isWithinSchedule = true;
                break;
            }
        }

        if (!$isWithinSchedule) {
            return back()->withErrors([
                'start_datetime' => 'The selected time slot is not within the staff member\'s scheduled availability for this day.'
            ]);
        }

        // Calculate total duration including addons
        $addonTotal = 0;
        $addonDurationTotal = 0;
        
        // Process addons
        $addons = [];
        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'addon_service_') && !empty($value)) {
                $addonId = substr($key, 14); // Remove 'addon_service_' prefix
                $nameKey = "addon_name_{$addonId}";
                $priceKey = "addon_price_{$addonId}";
                
                if ($value === 'custom') {
                    // Custom addon - no duration impact
                    if ($request->has($nameKey) && $request->has($priceKey) && 
                        is_numeric($request->input($priceKey)) && !empty($request->input($nameKey))) {
                        $addonPrice = (float) $request->input($priceKey);
                        $addonTotal += $addonPrice;
                        
                        $addons[] = [
                            'service_id' => null,
                            'name' => $request->input($nameKey),
                            'price' => $addonPrice,
                        ];
                    }
                } else {
                    // Service-based addon - includes duration
                    $addonService = Service::find($value);
                    if ($addonService) {
                        $addonPrice = $addonService->price_premium ?? $addonService->price_regular;
                        $addonTotal += $addonPrice;
                        $addonDurationTotal += $addonService->duration_minutes;
                        
                        $addons[] = [
                            'service_id' => $addonService->id,
                            'name' => $addonService->name,
                            'price' => $addonPrice,
                        ];
                    }
                }
            }
        }
        
        $totalAmount = $service->price_premium ?? $service->price_regular + $addonTotal;
        $totalDuration = $baseDuration + $addonDurationTotal;
        $endDateTime = Carbon::parse($request->start_datetime)->addMinutes($totalDuration);

        // Create appointment within a database transaction
        try {
            DB::beginTransaction();

            // Double-check availability right before creating (race condition prevention)
            if (!$this->isStaffAvailable($request->staff_id, $request->start_datetime, $totalDuration)) {
                DB::rollBack();
                return back()->withErrors([
                    'start_datetime' => 'This time slot was just booked by another customer. Please select another time.'
                ]);
            }

            $appointment = Appointment::create([
                'customer_id' => $request->customer_id,
                'service_id' => $request->service_id,
                'staff_id' => $request->staff_id,
                'start_datetime' => $request->start_datetime,
                'end_datetime' => $endDateTime,
                'total_amount' => $totalAmount,
                'status' => 'scheduled',
                'notes' => $request->notes ?? null,
            ]);
            
            // Create addons if any
            if (!empty($addons)) {
                foreach ($addons as $addon) {
                    AppointmentAddon::create([
                        'appointment_id' => $appointment->id,
                        'service_id' => $addon['service_id'],
                        'name' => $addon['name'],
                        'price' => $addon['price'],
                    ]);
                }
            }

            DB::commit();

            // Redirect based on the route - customer or admin/staff
            if (request()->routeIs('customer.*')) {
                return redirect()->route('customer.appointments.index')
                    ->with('success', 'Appointment booked successfully!');
            }

            return redirect()->route('dashboard.appointments.index')
                ->with('success', 'Appointment created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Appointment creation failed: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors([
                'start_datetime' => 'An error occurred while creating the appointment. Please try again.'
            ]);
        }
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
            'start_datetime' => 'required|date|after_or_equal:today',
        ]);

        $service = Service::findOrFail($request->service_id);
        $baseDuration = $service->duration_minutes;

        // Minimum duration check (30 minutes)
        if ($baseDuration < 30) {
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

        // Check staff availability, excluding current appointment itself
        if (!$this->isStaffAvailable($request->staff_id, $request->start_datetime, $baseDuration, $appointment->id)) {
            return back()->withErrors([
                'start_datetime' => 'Staff is not available for selected time slot. Please choose another time.'
            ]);
        }

        // Calculate end time and ensure it doesn't exceed closing time
        $endDateTime = $startDateTime->copy()->addMinutes($baseDuration);
        $closingTimeToday = Carbon::parse($startDateTime->format('Y-m-d') . ' ' . $salonSettings->closing_time);
        if ($endDateTime->gt($closingTimeToday)) {
            return back()->withErrors([
                'start_datetime' => 'Appointment would end after closing time. Please select an earlier time.'
            ]);
        }

        // Validate max days ahead
        $maxDate = now()->addDays($salonSettings->max_days_book_ahead);
        if ($startDateTime->gt($maxDate)) {
            return back()->withErrors([
                'start_datetime' => "Appointments can only be booked up to {$salonSettings->max_days_book_ahead} days in advance."
            ]);
        }

        // Validate staff schedule for the selected day
        $dayOfWeek = strtolower($startDateTime->format('l'));
        $staffHasSchedule = StaffSchedule::where('staff_id', $request->staff_id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->exists();

        if (!$staffHasSchedule) {
            return back()->withErrors([
                'start_datetime' => 'This staff member has not set their availability for ' . ucfirst($dayOfWeek) . '. Please select another date or choose a different staff member.'
            ]);
        }

        // Check if time slot is within staff's schedule
        $staffSchedules = StaffSchedule::where('staff_id', $request->staff_id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->get();

        $isWithinSchedule = false;
        foreach ($staffSchedules as $schedule) {
            $scheduleStartTime = $schedule->start_time instanceof Carbon 
                ? $schedule->start_time->format('H:i:s')
                : Carbon::parse($schedule->start_time)->format('H:i:s');
            $scheduleEndTime = $schedule->end_time instanceof Carbon 
                ? $schedule->end_time->format('H:i:s')
                : Carbon::parse($schedule->end_time)->format('H:i:s');
            
            $scheduleStart = Carbon::parse($startDateTime->format('Y-m-d') . ' ' . $scheduleStartTime);
            $scheduleEnd = Carbon::parse($startDateTime->format('Y-m-d') . ' ' . $scheduleEndTime);
            
            if ($startDateTime->gte($scheduleStart) && $endDateTime->lte($scheduleEnd)) {
                $isWithinSchedule = true;
                break;
            }
        }

        if (!$isWithinSchedule) {
            return back()->withErrors([
                'start_datetime' => 'The selected time slot is not within the staff member\'s scheduled availability for this day.'
            ]);
        }

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
                'addon_services' => 'nullable|array',
                'addon_services.*' => 'nullable|integer',
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
            
            // Get the day of the week for the selected date
            $selectedCarbon = Carbon::parse($selectedDate);
            $dayOfWeek = strtolower($selectedCarbon->format('l')); // monday, tuesday, etc.
            
            // Get salon settings (fallback if no staff schedule)
            $salonSettings = SalonSetting::getSettings();
            $openingTime = is_string($salonSettings->opening_time) 
                ? $salonSettings->opening_time 
                : (string)$salonSettings->opening_time ?? '09:00:00';
            $closingTime = is_string($salonSettings->closing_time) 
                ? $salonSettings->closing_time 
                : (string)$salonSettings->closing_time ?? '20:00:00';
            $interval = 60; // Fixed to hourly slots (60 minutes)
            
            // Ensure time format is H:i:s
            if (strlen($openingTime) === 5) {
                $openingTime .= ':00';
            }
            if (strlen($closingTime) === 5) {
                $closingTime .= ':00';
            }
            
            // Get service duration if service_id is provided
            $baseServiceDuration = 60; // Default 60 minutes
            if ($serviceId) {
                $service = Service::find($serviceId);
                if ($service) {
                    $baseServiceDuration = max($service->duration_minutes, 30); // Minimum 30 minutes
                }
            }
            
            // Calculate addon duration if provided
            $addonDurationTotal = 0;
            if ($request->has('addon_services') && is_array($request->addon_services)) {
                foreach ($request->addon_services as $addonServiceId) {
                    if ($addonServiceId && $addonServiceId !== 'custom') {
                        $addonService = Service::find($addonServiceId);
                        if ($addonService) {
                            $addonDurationTotal += $addonService->duration_minutes;
                        }
                    }
                }
            }
            
            // Total duration for availability checking
            $serviceDuration = $baseServiceDuration + $addonDurationTotal;
            
            // Get staff schedules for this day of the week
            $staffSchedules = StaffSchedule::where('staff_id', $staffId)
                ->where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->orderBy('start_time')
                ->get();
            
            // Get existing appointments for the selected staff and date
            $appointments = Appointment::where('staff_id', $staffId)
                ->whereDate('start_datetime', $selectedDate)
                ->whereNotIn('status', ['cancelled', 'no_show'])
                ->get(['start_datetime', 'end_datetime']);
            
            $timeSlots = [];
            $now = Carbon::now();
            $isToday = $selectedCarbon->isToday();
            
            // If staff has schedules for this day, use them
            if ($staffSchedules->count() > 0) {
                foreach ($staffSchedules as $schedule) {
                    // Handle time format - could be Carbon instance or string
                    $scheduleStartTime = $schedule->start_time;
                    $scheduleEndTime = $schedule->end_time;
                    
                    if ($scheduleStartTime instanceof Carbon) {
                        $scheduleStartTime = $scheduleStartTime->format('H:i:s');
                    } else {
                        $scheduleStartTime = Carbon::parse($scheduleStartTime)->format('H:i:s');
                    }
                    
                    if ($scheduleEndTime instanceof Carbon) {
                        $scheduleEndTime = $scheduleEndTime->format('H:i:s');
                    } else {
                        $scheduleEndTime = Carbon::parse($scheduleEndTime)->format('H:i:s');
                    }
                    
                    $scheduleStart = Carbon::parse($selectedDate . ' ' . $scheduleStartTime);
                    $scheduleEnd = Carbon::parse($selectedDate . ' ' . $scheduleEndTime);
                    
                    // Check max appointments limit for this schedule
                    if ($schedule->max_appointments !== null) {
                        $appointmentsInSchedule = Appointment::where('staff_id', $staffId)
                            ->whereDate('start_datetime', $selectedDate)
                            ->whereNotIn('status', ['cancelled', 'no_show'])
                            ->where(function ($q) use ($scheduleStart, $scheduleEnd) {
                                $q->whereBetween('start_datetime', [$scheduleStart, $scheduleEnd->copy()->subMinute()])
                                    ->orWhereBetween('end_datetime', [$scheduleStart->copy()->addMinute(), $scheduleEnd])
                                    ->orWhere(function ($q2) use ($scheduleStart, $scheduleEnd) {
                                        $q2->where('start_datetime', '<=', $scheduleStart)
                                            ->where('end_datetime', '>=', $scheduleEnd);
                                    });
                            })
                            ->count();
                        
                        if ($appointmentsInSchedule >= $schedule->max_appointments) {
                            continue; // Skip this schedule, max appointments reached
                        }
                    }
                    
                    // Generate time slots within this schedule
                    $current = $scheduleStart->copy();
                    
                    while ($current->lt($scheduleEnd)) {
                        // Skip past times if the selected date is today
                        if ($isToday && $current->lt($now)) {
                            $current->addMinutes($interval);
                            continue;
                        }
                        
                        // Calculate when the appointment would end
                        $appointmentEnd = $current->copy()->addMinutes($serviceDuration);
                        
                        // Skip if appointment would end after schedule end time
                        if ($appointmentEnd->gt($scheduleEnd)) {
                            $current->addMinutes($interval);
                            continue;
                        }
                        
                        $isAvailable = true;
                        
                        // Check if the time slot conflicts with existing appointments
                        foreach ($appointments as $appt) {
                            $apptStart = Carbon::parse($appt->start_datetime);
                            $apptEnd = Carbon::parse($appt->end_datetime);
                            
                            // Check for overlap: new appointment overlaps with existing one
                            if ($current->lt($apptEnd) && $appointmentEnd->gt($apptStart)) {
                                $isAvailable = false;
                                break;
                            }
                        }
                        
                        if ($isAvailable) {
                            $timeSlots[] = [
                                'time' => $current->format('H:i:s'),
                                'formatted_time' => $current->format('g:i A')
                            ];
                        }
                        
                        $current->addMinutes($interval);
                    }
                }
            }
            // If staff has no schedule for this day, timeSlots array remains empty (no fallback to salon hours)
            
            // Sort time slots by time
            usort($timeSlots, function($a, $b) {
                return strcmp($a['time'], $b['time']);
            });
            
            return response()->json([
                'available_times' => $timeSlots,
                'has_schedule' => $staffSchedules->count() > 0,
                'day_of_week' => ucfirst($dayOfWeek)
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in checkAvailability: ' . $e->getMessage(), [
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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isStaff() && $user->staff && $appointment->staff_id !== $user->staff->id) {
            abort(403, 'You can only update your own appointments.');
        }

        $request->validate([
            'status' => 'required|in:scheduled,confirmed,in_progress,completed,cancelled,no_show,failed'
        ]);

        $currentDateTime = now();
        $appointmentDate = $appointment->start_datetime;
        $oldStatus = $appointment->status;
        $newStatus = $request->status;

        // Validate status transitions
        $validTransitions = [
            'scheduled' => ['confirmed', 'cancelled', 'no_show', 'failed'],
            'confirmed' => ['in_progress', 'cancelled', 'no_show', 'failed'],
            'in_progress' => ['completed', 'cancelled', 'failed'],
            'completed' => [], // Cannot transition from completed
            'cancelled' => [], // Cannot transition from cancelled
            'no_show' => [], // Cannot transition from no_show
            'failed' => [], // Cannot transition from failed
        ];

        if (!in_array($newStatus, $validTransitions[$oldStatus] ?? [])) {
            return back()->withErrors([
                'status' => "Invalid status transition from '{$oldStatus}' to '{$newStatus}'. Valid transitions: " . implode(', ', $validTransitions[$oldStatus] ?? [])
            ]);
        }

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
                'status' => "Cannot start appointment before the scheduled time ($formattedDate). The appointment timeslot is not available yet."
            ]);
        }

        // Update the appointment status within a transaction
        try {
            DB::beginTransaction();
            
            $appointment->update(['status' => $newStatus]);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Status update failed: ' . $e->getMessage(), [
                'appointment_id' => $appointment->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

            return back()->withErrors([
                'status' => 'An error occurred while updating the appointment status. Please try again.'
            ]);
        }

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

    public function showCustomerCancelForm(Appointment $appointment)
    {
        // Check if this is a customer route - ensure customer can only cancel their own appointments
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $customer = $user->customer;
        
        if (!$customer || $appointment->customer_id !== $customer->id) {
            abort(403, 'You can only cancel your own appointments.');
        }
        
        // Check if appointment can be cancelled (only upcoming appointments)
        $appointmentDate = \Carbon\Carbon::parse($appointment->start_datetime);
        if (!in_array($appointment->status, ['scheduled', 'confirmed']) || !$appointmentDate->isFuture()) {
            abort(403, 'This appointment cannot be cancelled.');
        }
        
        return view('customer.appointments.cancel', compact('appointment'));
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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($status === 'cancelled') {
            $appointment->cancel($cancellationReason, $user);
        } else {
            $appointment->markAsFailed($cancellationReason);
        }

        // Handle refund if applicable (only for admin/staff)
        if (!$request->routeIs('customer.*') && $request->has('refund_amount') && $request->refund_amount > 0 && $appointment->payment) {
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