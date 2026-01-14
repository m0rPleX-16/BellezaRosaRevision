<?php

namespace App\Http\Controllers;

use App\Models\StaffSchedule;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffScheduleController extends Controller
{
    /**
     * Display the weekly schedule for the logged-in staff
     */
    public function index()
    {
        $user = Auth::user();
        $staff = $user->staff;

        if (!$staff) {
            abort(403, 'Staff profile not found.');
        }

        // Get all schedules for this staff, grouped by day
        $schedules = StaffSchedule::where('staff_id', $staff->id)
            ->where('is_active', true)
            ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')")
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        // Days of the week in order
        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        return view('dashboard.staff.schedule', compact('schedules', 'daysOfWeek', 'staff'));
    }

    /**
     * Store a newly created schedule
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $staff = $user->staff;

        if (!$staff) {
            return response()->json(['error' => 'Staff profile not found.'], 403);
        }

        $request->validate([
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'max_appointments' => 'nullable|integer|min:1',
        ]);

        // Check for overlapping time slots on the same day
        $overlapping = StaffSchedule::where('staff_id', $staff->id)
            ->where('day_of_week', $request->day_of_week)
            ->where('is_active', true)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->exists();

        if ($overlapping) {
            return response()->json([
                'error' => 'This time slot overlaps with an existing schedule for this day.'
            ], 422);
        }

        $schedule = StaffSchedule::create([
            'staff_id' => $staff->id,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'max_appointments' => $request->max_appointments ?: null,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Schedule added successfully.',
            'schedule' => $schedule->load('staff'),
        ]);
    }

    /**
     * Update the specified schedule
     */
    public function update(Request $request, StaffSchedule $schedule)
    {
        $user = Auth::user();
        $staff = $user->staff;

        if (!$staff || $schedule->staff_id !== $staff->id) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'max_appointments' => 'nullable|integer|min:1',
        ]);

        // Check for overlapping time slots (excluding current schedule)
        $overlapping = StaffSchedule::where('staff_id', $staff->id)
            ->where('day_of_week', $request->day_of_week)
            ->where('is_active', true)
            ->where('id', '!=', $schedule->id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->exists();

        if ($overlapping) {
            return response()->json([
                'error' => 'This time slot overlaps with an existing schedule for this day.'
            ], 422);
        }

        $schedule->update([
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'max_appointments' => $request->max_appointments ?: null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Schedule updated successfully.',
            'schedule' => $schedule->fresh(),
        ]);
    }

    /**
     * Show the specified schedule (for editing)
     */
    public function show(StaffSchedule $schedule)
    {
        $user = Auth::user();
        $staff = $user->staff;

        if (!$staff || $schedule->staff_id !== $staff->id) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        return response()->json([
            'success' => true,
            'schedule' => $schedule,
        ]);
    }

    /**
     * Remove the specified schedule
     */
    public function destroy(StaffSchedule $schedule)
    {
        $user = Auth::user();
        $staff = $user->staff;

        if (!$staff || $schedule->staff_id !== $staff->id) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully.',
        ]);
    }
}
