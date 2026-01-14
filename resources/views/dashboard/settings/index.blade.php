@extends('layouts.dashboard')

@section('title', 'Salon Settings - Belleza Rosa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Salon Settings</h1>
        <p class="text-gray-600">Configure business hours, booking intervals, and appointment policies</p>
    </div>

    <!-- Settings Form -->
    <div class="card">
        <form action="{{ route('dashboard.settings.update') }}" method="POST">
            @csrf
            @method('POST')
            
            <div class="space-y-6">
                <!-- Business Hours Section -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Business Hours</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Opening Time -->
                        <div>
                            <label for="opening_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Opening Time <span class="text-red-500">*</span>
                            </label>
                            <input type="time" 
                                name="opening_time" 
                                id="opening_time"
                                value="{{ old('opening_time', $settings->opening_time ? substr($settings->opening_time, 0, 5) : '09:00') }}"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                            <p class="mt-2 text-sm text-gray-500">
                                The time the salon opens each day (24-hour format).
                            </p>
                            @error('opening_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Closing Time -->
                        <div>
                            <label for="closing_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Closing Time <span class="text-red-500">*</span>
                            </label>
                            <input type="time" 
                                name="closing_time" 
                                id="closing_time"
                                value="{{ old('closing_time', $settings->closing_time ? substr($settings->closing_time, 0, 5) : '20:00') }}"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                            <p class="mt-2 text-sm text-gray-500">
                                The time the salon closes each day (24-hour format). Must be after opening time.
                            </p>
                            @error('closing_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Booking Configuration Section -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Booking Configuration</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Slot Interval -->
                        <div>
                            <label for="slot_interval_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                                Time Slot Interval (minutes) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                name="slot_interval_minutes" 
                                id="slot_interval_minutes"
                                value="{{ old('slot_interval_minutes', $settings->slot_interval_minutes ?? 30) }}"
                                min="5"
                                max="120"
                                step="5"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                            <p class="mt-2 text-sm text-gray-500">
                                The interval between available appointment time slots (e.g., 30 for 30-minute intervals).
                            </p>
                            @error('slot_interval_minutes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Max Days Ahead -->
                        <div>
                            <label for="max_days_book_ahead" class="block text-sm font-medium text-gray-700 mb-2">
                                Maximum Days Ahead <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                name="max_days_book_ahead" 
                                id="max_days_book_ahead"
                                value="{{ old('max_days_book_ahead', $settings->max_days_book_ahead ?? 60) }}"
                                min="1"
                                max="365"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                            <p class="mt-2 text-sm text-gray-500">
                                How many days in advance customers can book appointments (e.g., 60 for 2 months).
                            </p>
                            @error('max_days_book_ahead')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Cancellation Policy Section -->
                <div class="pb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Cancellation Policy</h2>
                    
                    <div>
                        <label for="cancel_cutoff_hours" class="block text-sm font-medium text-gray-700 mb-2">
                            Cancellation Cutoff (hours) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                            name="cancel_cutoff_hours" 
                            id="cancel_cutoff_hours"
                            value="{{ old('cancel_cutoff_hours', $settings->cancel_cutoff_hours ?? 2) }}"
                            min="0"
                            max="168"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none max-w-md">
                        <p class="mt-2 text-sm text-gray-500">
                            Minimum hours before an appointment when customers can still cancel (e.g., 2 for 2 hours before).
                        </p>
                        @error('cancel_cutoff_hours')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h2a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">About Salon Settings</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Changes to business hours will affect all new bookings immediately.</li>
                                    <li>Time slot intervals determine how appointments are scheduled (e.g., 30 minutes = slots at 9:00, 9:30, 10:00, etc.).</li>
                                    <li>Maximum days ahead limits how far in advance customers can book.</li>
                                    <li>Cancellation cutoff prevents last-minute cancellations.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                    <button type="submit"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transform hover:scale-105 transition">
                        <i class="fas fa-save mr-2"></i> Save Settings
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
