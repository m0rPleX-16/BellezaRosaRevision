@extends('layouts.dashboard')

@section('title', 'Commission Settings - Belleza Rosa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Commission Settings</h1>
            <p class="text-gray-600">Configure commission rates and payment schedules</p>
        </div>
        <a href="{{ route('dashboard.commissions.index') }}"
            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-xl transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Commissions
        </a>
    </div>

    <!-- Settings Form -->
    <div class="card">
        <form action="{{ route('dashboard.commissions.settings.update') }}" method="POST">
            @csrf
            @method('POST')
            
            <div class="space-y-6">
                <!-- Default Commission Rate -->
                <div>
                    <label for="default_commission_rate" class="block text-sm font-medium text-gray-700 mb-2">
                        Default Commission Rate (%)
                    </label>
                    <input type="number" 
                        name="default_commission_rate" 
                        id="default_commission_rate"
                        value="{{ old('default_commission_rate', $settings->default_commission_rate ?? 30) }}"
                        min="0" 
                        max="100" 
                        step="0.01"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                    <p class="mt-2 text-sm text-gray-500">
                        The default percentage of service amount that staff will receive as commission (e.g., 30 for 30%).
                    </p>
                    @error('default_commission_rate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Commission Payment Day -->
                <div>
                    <label for="commission_payment_day" class="block text-sm font-medium text-gray-700 mb-2">
                        Commission Payment Day
                    </label>
                    <input type="number" 
                        name="commission_payment_day" 
                        id="commission_payment_day"
                        value="{{ old('commission_payment_day', $settings->commission_payment_day ?? 15) }}"
                        min="1" 
                        max="31" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                    <p class="mt-2 text-sm text-gray-500">
                        The day of each month when commissions are typically paid out (1-31).
                    </p>
                    @error('commission_payment_day')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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
                            <h3 class="text-sm font-medium text-blue-800">About Commission Settings</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Commission rate applies to all new commissions created after saving these settings.</li>
                                    <li>Existing commissions will not be affected by rate changes.</li>
                                    <li>The payment day is informational and helps staff know when to expect payments.</li>
                                    <li>Commissions can be paid manually at any time regardless of this setting.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('dashboard.commissions.index') }}"
                        class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                        Cancel
                    </a>
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
