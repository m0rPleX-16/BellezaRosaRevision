@extends('layouts.dashboard')

@section('title', 'Generate Commission Report - Belleza Rosa')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Generate Commission Report</h1>
            <a href="{{ route('dashboard.commissions.index') }}" 
                class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-xl transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Commissions
            </a>
        </div>

        <!-- Report Form -->
        <div class="card">
            <form action="{{ route('dashboard.commissions.report.generate') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Report Type -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Report Type *</label>
                    <select name="report_type" id="report_type" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                        <option value="">Select Report Type</option>
                        <option value="monthly">Monthly Report</option>
                        <option value="staff">Staff Report</option>
                        <option value="detailed">Detailed Report</option>
                    </select>
                    @error('report_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Year Field -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Year *</label>
                    <input type="number" name="year" value="{{ date('Y') }}" min="2020" max="{{ date('Y') + 5 }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                    @error('year')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Month Field (Conditional) -->
                <div id="monthField" class="hidden">
                    <label class="block text-gray-700 font-semibold mb-2">Month *</label>
                    <select name="month"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                        <option value="">Select Month</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                        @endfor
                    </select>
                    @error('month')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Staff Field (Conditional) -->
                <div id="staffField" class="hidden">
                    <label class="block text-gray-700 font-semibold mb-2">Staff Member</label>
                    <select name="staff_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                        <option value="">All Staff</option>
                        @foreach ($staff as $staffMember)
                            <option value="{{ $staffMember->id }}">{{ $staffMember->user->full_name }}</option>
                        @endforeach
                    </select>
                    @error('staff_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('dashboard.commissions.index') }}" 
                        class="px-6 py-3 bg-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-300 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transform hover:scale-105 transition">
                        <i class="fas fa-chart-bar mr-2"></i> Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Show/hide conditional fields based on report type
        document.getElementById('report_type').addEventListener('change', function() {
            const reportType = this.value;
            const monthField = document.getElementById('monthField');
            const staffField = document.getElementById('staffField');
            const monthSelect = document.querySelector('select[name="month"]');
            
            // Hide all conditional fields first
            monthField.classList.add('hidden');
            staffField.classList.add('hidden');
            
            // Remove required attribute from month when hidden
            monthSelect.removeAttribute('required');
            
            // Show relevant fields based on report type
            if (reportType === 'monthly') {
                monthField.classList.remove('hidden');
                monthSelect.setAttribute('required', 'required');
            } else if (reportType === 'staff') {
                staffField.classList.remove('hidden');
            }
            // For 'detailed', only year is needed
        });
    </script>
@endsection
