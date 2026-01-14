<?php

namespace App\Http\Controllers;

use App\Models\SalonSetting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Show salon settings page
     */
    public function index(): View
    {
        $settings = SalonSetting::getSettings();
        return view('dashboard.settings.index', compact('settings'));
    }

    /**
     * Update salon settings
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i',
            'slot_interval_minutes' => 'required|integer|min:5|max:120',
            'max_days_book_ahead' => 'required|integer|min:1|max:365',
            'cancel_cutoff_hours' => 'required|integer|min:0|max:168', // Max 7 days (168 hours)
        ], [
            'opening_time.required' => 'Opening time is required.',
            'opening_time.date_format' => 'Opening time must be in HH:MM format.',
            'closing_time.required' => 'Closing time is required.',
            'closing_time.date_format' => 'Closing time must be in HH:MM format.',
            'slot_interval_minutes.required' => 'Slot interval is required.',
            'slot_interval_minutes.integer' => 'Slot interval must be a number.',
            'slot_interval_minutes.min' => 'Slot interval must be at least 5 minutes.',
            'slot_interval_minutes.max' => 'Slot interval cannot exceed 120 minutes.',
            'max_days_book_ahead.required' => 'Maximum days ahead is required.',
            'max_days_book_ahead.integer' => 'Maximum days ahead must be a number.',
            'max_days_book_ahead.min' => 'Maximum days ahead must be at least 1.',
            'max_days_book_ahead.max' => 'Maximum days ahead cannot exceed 365.',
            'cancel_cutoff_hours.required' => 'Cancellation cutoff is required.',
            'cancel_cutoff_hours.integer' => 'Cancellation cutoff must be a number.',
            'cancel_cutoff_hours.min' => 'Cancellation cutoff cannot be negative.',
            'cancel_cutoff_hours.max' => 'Cancellation cutoff cannot exceed 168 hours (7 days).',
        ]);

        // Validate that closing time is after opening time
        if (strtotime($validated['closing_time']) <= strtotime($validated['opening_time'])) {
            return redirect()->back()
                ->withErrors(['closing_time' => 'Closing time must be after opening time.'])
                ->withInput();
        }

        // Convert time format from H:i to H:i:s
        $validated['opening_time'] = $validated['opening_time'] . ':00';
        $validated['closing_time'] = $validated['closing_time'] . ':00';

        $settings = SalonSetting::getSettings();
        $settings->update($validated);

        return redirect()->route('dashboard.settings.index')
            ->with('success', 'Salon settings updated successfully!');
    }
}
