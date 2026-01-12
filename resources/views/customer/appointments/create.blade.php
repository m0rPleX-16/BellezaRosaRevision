@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Book New Appointment</h1>
                <a href="{{ route('customer.appointments.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    &larr; Back to Appointments
                </a>
            </div>

            <form action="{{ route('customer.appointments.store') }}" method="POST" id="appointmentForm">
                @csrf
                <input type="hidden" name="customer_id" value="{{ auth()->user()->customer->id }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Service Selection -->
                    <div class="col-span-2">
                        <label for="service_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Service <span class="text-red-500">*</span>
                        </label>
                        <select name="service_id" id="service_id" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required>
                            <option value="">-- Select a service --</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" 
                                        data-duration="{{ $service->duration_minutes }}"
                                        data-price="{{ $service->price_regular }}">
                                    {{ $service->name }} ({{ $service->duration_minutes }} min)
                                </option>
                            @endforeach
                        </select>
                        @error('service_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Staff Selection -->
                    <div class="col-span-2">
                        <label for="staff_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Staff Member <span class="text-red-500">*</span>
                        </label>
                        <select name="staff_id" id="staff_id" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required>
                            <option value="">-- Select a staff member --</option>
                            @foreach($staff as $staffMember)
                                <option value="{{ $staffMember->id }}">
                                    {{ $staffMember->user->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('staff_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date Selection -->
                    <div>
                        <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="appointment_date" 
                               id="appointment_date" 
                               min="{{ now()->format('Y-m-d') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               required>
                        @error('appointment_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Time Selection -->
                    <div>
                        <label for="appointment_time" class="block text-sm font-medium text-gray-700 mb-1">
                            Time <span class="text-red-500">*</span>
                        </label>
                        <select name="appointment_time" id="appointment_time" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                disabled
                                required>
                            <option value="">Select a date first</option>
                        </select>
                        @error('appointment_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                            Special Requests or Notes
                        </label>
                        <textarea name="notes" id="notes" rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Any special requests or notes for your appointment"></textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Summary -->
                    <div class="col-span-2 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Appointment Summary</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Service:</span>
                                <span id="service-summary" class="font-medium">--</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Duration:</span>
                                <span id="duration-summary">--</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Staff:</span>
                                <span id="staff-summary">--</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Date & Time:</span>
                                <span id="datetime-summary">--</span>
                            </div>
                            <div class="border-t border-gray-200 my-2"></div>
                            <div class="flex justify-between font-medium">
                                <span class="text-gray-900">Total:</span>
                                <span id="price-summary" class="text-lg text-blue-600">₱0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('customer.appointments.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Book Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const serviceSelect = document.getElementById('service_id');
        const staffSelect = document.getElementById('staff_id');
        const dateInput = document.getElementById('appointment_date');
        const timeSelect = document.getElementById('appointment_time');
        
        // Update summary when service changes
        serviceSelect.addEventListener('change', updateSummary);
        staffSelect.addEventListener('change', updateSummary);
        dateInput.addEventListener('change', updateSummary);
        timeSelect.addEventListener('change', updateSummary);
        
        // When date changes, fetch available time slots
        dateInput.addEventListener('change', function() {
            const selectedDate = this.value;
            const staffId = staffSelect.value;
            
            if (!selectedDate || !staffId) {
                timeSelect.disabled = true;
                timeSelect.innerHTML = '<option value="">Select staff and date first</option>';
                return;
            }
            
            // Show loading
            timeSelect.disabled = true;
            timeSelect.innerHTML = '<option value="">Loading available times...</option>';
            
            // Fetch available time slots via AJAX
            fetch(`/dashboard/appointments/check-availability?date=${selectedDate}&staff_id=${staffId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                timeSelect.innerHTML = '';
                
                if (data.available_times && data.available_times.length > 0) {
                    data.available_times.forEach(time => {
                        const option = document.createElement('option');
                        option.value = time.time;
                        option.textContent = time.formatted_time || time.time; // Fallback to time if formatted_time is not available
                        timeSelect.appendChild(option);
                    });
                    timeSelect.disabled = false;
                } else {
                    timeSelect.innerHTML = '<option value="">No available time slots</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching available times:', error);
                timeSelect.innerHTML = '<option value="">Error loading times. Please try again.</option>';
            });
        });
        
        // Enable time select when staff is selected
        staffSelect.addEventListener('change', function() {
            if (this.value && dateInput.value) {
                dateInput.dispatchEvent(new Event('change'));
            }
        });
        
        function updateSummary() {
            // Update service summary
            const selectedService = serviceSelect.options[serviceSelect.selectedIndex];
            document.getElementById('service-summary').textContent = 
                selectedService.text || '--';
                
            // Update duration
            const duration = selectedService ? selectedService.dataset.duration : '--';
            document.getElementById('duration-summary').textContent = 
                duration ? `${duration} minutes` : '--';
                
            // Update staff
            const selectedStaff = staffSelect.options[staffSelect.selectedIndex];
            document.getElementById('staff-summary').textContent = 
                selectedStaff.text || '--';
                
            // Update date and time
            const date = dateInput.value;
            const time = timeSelect.options[timeSelect.selectedIndex]?.text || '--';
            document.getElementById('datetime-summary').textContent = 
                date ? `${new Date(date).toDateString()}, ${time}` : '--';
                
            // Update price
            const price = selectedService ? selectedService.dataset.price : '0.00';
            document.getElementById('price-summary').textContent = 
                `₱${parseFloat(price).toFixed(2)}`;
        }
        
        // Initial summary update
        updateSummary();
    });
</script>
@endpush
@endsection
