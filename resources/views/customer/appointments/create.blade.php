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
                        @if (isset($prefilledService) && isset($prefilledStaff))
                            <!-- Service Display (Read-only when pre-filled) -->
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Service <span class="text-red-500">*</span>
                                </label>
                                <div id="service_display"
                                    class="w-full rounded-md border-gray-300 bg-gray-50 px-4 py-3 text-gray-900 font-medium">
                                    {{ $prefilledService->name }} ({{ $prefilledService->duration_minutes }} min)
                                </div>
                                <input type="hidden" name="service_id" id="service_id" value="{{ $prefilledService->id }}"
                                    required>
                                <input type="hidden" id="service_duration"
                                    value="{{ $prefilledService->duration_minutes }}">
                                <input type="hidden" id="service_price" value="{{ $prefilledService->price_regular }}">
                                <input type="hidden" id="service_price_premium"
                                    value="{{ $prefilledService->price_premium ?? $prefilledService->price_regular }}">
                                <input type="hidden" id="service_name" value="{{ $prefilledService->name }}">
                                @error('service_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Staff Display (Read-only when pre-filled) -->
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Staff Member <span class="text-red-500">*</span>
                                </label>
                                <div id="staff_display"
                                    class="w-full rounded-md border-gray-300 bg-gray-50 px-4 py-3 text-gray-900 font-medium">
                                    {{ $prefilledStaff->user->full_name }}@if ($prefilledStaff->user->gender)
                                        ({{ $prefilledStaff->user->formatted_gender }})
                                    @endif
                                </div>
                                <input type="hidden" name="staff_id" id="staff_id" value="{{ $prefilledStaff->id }}"
                                    required>
                                <input type="hidden" id="staff_name"
                                    value="{{ $prefilledStaff->user->full_name }}@if ($prefilledStaff->user->gender) ({{ $prefilledStaff->user->formatted_gender }}) @endif">
                                @error('staff_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @else
                            <!-- Service Selection (Dropdown when not pre-filled) -->
                            <div class="col-span-2">
                                <label for="service_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Service <span class="text-red-500">*</span>
                                </label>
                                <select name="service_id" id="service_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required>
                                    <option value="">-- Select a service --</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}"
                                            data-duration="{{ $service->duration_minutes }}"
                                            data-price="{{ $service->price_regular }}"
                                            data-price-premium="{{ $service->price_premium ?? $service->price_regular }}">
                                            {{ $service->name }} ({{ $service->duration_minutes }} min)
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Staff Selection (Dropdown when not pre-filled) -->
                            <div class="col-span-2">
                                <label for="staff_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Staff Member <span class="text-red-500">*</span>
                                </label>
                                <select name="staff_id" id="staff_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required>
                                    <option value="">-- Select a staff member --</option>
                                    @foreach ($staff as $staffMember)
                                        <option value="{{ $staffMember->id }}">
                                            {{ $staffMember->user->full_name }}@if ($staffMember->user->gender)
                                                ({{ $staffMember->user->formatted_gender }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('staff_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <!-- Date Selection -->
                        <div class="col-span-2">
                            <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="appointment_date" id="appointment_date"
                                min="{{ now()->format('Y-m-d') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required>
                            @error('appointment_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('appointment_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        @error('start_datetime')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">The first available time slot will be automatically
                                selected based on your service duration.</p>
                        <!-- Display selected time slot -->
                        <div id="selected-time-display" class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-md hidden">
                            <span class="text-sm font-medium text-blue-900">Selected Time: </span>
                            <span id="selected-time-text" class="text-sm text-blue-700">--</span>
                        </div>
                        </div>

                        <!-- Hidden time field (auto-filled) -->
                        <input type="hidden" name="appointment_time" id="appointment_time" value="">
                        <input type="hidden" name="start_datetime" id="start_datetime" value="">

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
                                    <span class="text-gray-600">Date:</span>
                                    <span id="date-summary">--</span>
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
                const timeInput = document.getElementById('appointment_time');
                const startDatetimeInput = document.getElementById('start_datetime');

                // Check if service and staff are pre-filled (hidden inputs) or dropdowns
                const isPrefilled = serviceSelect && serviceSelect.tagName === 'INPUT' && staffSelect && staffSelect
                    .tagName === 'INPUT';

                // Get service and staff details
                let serviceDuration, servicePrice, servicePricePremium, serviceName, staffName;

                if (isPrefilled) {
                    // Pre-filled mode: get from hidden inputs
                    serviceDuration = document.getElementById('service_duration')?.value;
                    servicePrice = document.getElementById('service_price')?.value;
                    servicePricePremium = document.getElementById('service_price_premium')?.value;
                    serviceName = document.getElementById('service_name')?.value || document.getElementById(
                        'service_display')?.textContent.trim() || '--';
                    staffName = document.getElementById('staff_name')?.value || document.getElementById('staff_display')
                        ?.textContent.trim() || '--';

                    // Update summary immediately
                    document.getElementById('service-summary').textContent = serviceName;
                    document.getElementById('duration-summary').textContent = serviceDuration ?
                        `${serviceDuration} minutes` : '--';
                    document.getElementById('staff-summary').textContent = staffName;
                    document.getElementById('price-summary').textContent =
                        `₱${parseFloat(servicePricePremium || servicePrice || 0).toFixed(2)}`;
                } else {
                    // Dropdown mode: add event listeners
                    serviceSelect.addEventListener('change', updateSummary);
                    staffSelect.addEventListener('change', updateSummary);
                }

                dateInput.addEventListener('change', function() {
                    fetchTimeSlots();
                });

                // When date changes, fetch available time slots and auto-select first one
                function fetchTimeSlots() {
                    const selectedDate = dateInput.value;
                    // Get staff and service IDs - works for both hidden inputs and selects
                    const staffId = staffSelect ? staffSelect.value : null;
                    const serviceId = serviceSelect ? serviceSelect.value : null;

                    if (!selectedDate || !staffId || !serviceId) {
                        timeInput.value = '';
                        startDatetimeInput.value = '';
                        updateSummary();
                        return;
                    }

                    // Show loading state (optional - you can add a spinner here)
                    const submitButton = document.querySelector('button[type="submit"]');
                    const originalButtonText = submitButton ? submitButton.textContent : '';
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.textContent = 'Loading time slots...';
                    }

                    // Fetch available time slots via AJAX (with service_id for duration-based slots)
                    const formData = new FormData();
                    formData.append('date', selectedDate);
                    formData.append('staff_id', staffId);
                    if (serviceId) {
                        formData.append('service_id', serviceId);
                    }
                    
                    fetch(`{{ route('customer.appointments.checkAvailability') }}`, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            },
                            body: formData
                        })
                        .then(response => {
                            // Check if response is ok
                            if (!response.ok) {
                                // Try to get error message from response
                                return response.json().then(err => {
                                    console.error('Server error response:', err);
                                    const errorMsg = err.message || err.error || 'Failed to fetch time slots';
                                    if (err.errors) {
                                        console.error('Validation errors:', err.errors);
                                    }
                                    throw new Error(errorMsg);
                                }).catch((parseError) => {
                                    console.error('Failed to parse error response:', parseError);
                                    throw new Error(`Server error: ${response.status} ${response.statusText}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.available_times && data.available_times.length > 0) {
                                // Auto-select the first available time slot
                                const firstTime = data.available_times[0];
                                timeInput.value = firstTime.time;

                                // Combine date and time for start_datetime
                                const startDatetime = `${selectedDate} ${firstTime.time}`;
                                startDatetimeInput.value = startDatetime;

                                // Display selected time
                                const timeDisplay = document.getElementById('selected-time-display');
                                const timeText = document.getElementById('selected-time-text');
                                if (timeDisplay && timeText) {
                                    timeText.textContent = firstTime.formatted_time;
                                    timeDisplay.classList.remove('hidden');
                                }

                                updateSummary();
                            } else {
                                timeInput.value = '';
                                startDatetimeInput.value = '';
                                
                                // Hide time display
                                const timeDisplay = document.getElementById('selected-time-display');
                                if (timeDisplay) {
                                    timeDisplay.classList.add('hidden');
                                }
                                
                                updateSummary();
                                alert('No available time slots for the selected date. Please choose another date.');
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching available times:', error);
                            console.error('Error details:', {
                                message: error.message,
                                stack: error.stack,
                                selectedDate: selectedDate,
                                staffId: staffId,
                                serviceId: serviceId
                            });
                            timeInput.value = '';
                            startDatetimeInput.value = '';
                            
                            // Hide time display
                            const timeDisplay = document.getElementById('selected-time-display');
                            if (timeDisplay) {
                                timeDisplay.classList.add('hidden');
                            }
                            
                            updateSummary();
                            alert('Error loading time slots: ' + error.message + '\n\nPlease check the console for details or try selecting a different date.');
                        })
                        .finally(() => {
                            // Restore button state
                            if (submitButton) {
                                submitButton.disabled = false;
                                submitButton.textContent = originalButtonText;
                            }
                        });
                }

                // Enable time fetch when staff or service is selected (only for dropdown mode)
                if (!isPrefilled) {
                    staffSelect.addEventListener('change', function() {
                        if (this.value && dateInput.value && serviceSelect.value) {
                            fetchTimeSlots();
                        }
                    });

                    serviceSelect.addEventListener('change', function() {
                        if (this.value && dateInput.value && staffSelect.value) {
                            fetchTimeSlots();
                        }
                    });
                } else {
                    // For prefilled mode, if date is already set on page load, fetch time slots
                    if (dateInput.value && staffSelect.value && serviceSelect.value) {
                        fetchTimeSlots();
                    }
                }

                function updateSummary() {
                    if (isPrefilled) {
                        // Pre-filled mode: use stored values
                        document.getElementById('service-summary').textContent = serviceName;
                        document.getElementById('duration-summary').textContent = serviceDuration ?
                            `${serviceDuration} minutes` : '--';
                        document.getElementById('staff-summary').textContent = staffName;

                        // Update date only
                        const date = dateInput.value;
                        document.getElementById('date-summary').textContent =
                            date ? new Date(date).toLocaleDateString('en-US', {
                                weekday: 'short',
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric'
                            }) : '--';

                        document.getElementById('price-summary').textContent =
                            `₱${parseFloat(servicePricePremium || servicePrice || 0).toFixed(2)}`;
                    } else {
                        // Dropdown mode: get from selected options
                        const selectedService = serviceSelect.options[serviceSelect.selectedIndex];
                        document.getElementById('service-summary').textContent =
                            selectedService.text || '--';

                        const duration = selectedService ? selectedService.dataset.duration : '--';
                        document.getElementById('duration-summary').textContent =
                            duration ? `${duration} minutes` : '--';

                        const selectedStaff = staffSelect.options[staffSelect.selectedIndex];
                        document.getElementById('staff-summary').textContent =
                            selectedStaff.text || '--';

                        // Update date only
                        const date = dateInput.value;
                        document.getElementById('date-summary').textContent =
                            date ? new Date(date).toLocaleDateString('en-US', {
                                weekday: 'short',
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric'
                            }) : '--';

                        const price = selectedService ? (selectedService.dataset.pricePremium || selectedService.dataset
                            .price) : '0.00';
                        document.getElementById('price-summary').textContent =
                            `₱${parseFloat(price).toFixed(2)}`;
                    }
                }

                // Initial summary update
                updateSummary();

                // Form validation before submission
                document.getElementById('appointmentForm').addEventListener('submit', function(e) {
                    const date = dateInput.value;
                    const time = timeInput.value;
                    const startDatetime = startDatetimeInput.value;
                    const serviceId = document.getElementById('service_id').value;
                    const staffId = document.getElementById('staff_id').value;

                    // Check if all required fields are filled
                    if (!date || !time || !startDatetime || !serviceId || !staffId) {
                        e.preventDefault();
                        alert(
                            'Please ensure all fields are filled and a time slot is selected. If you just selected a date, please wait for the time slot to load.');
                        return false;
                    }
                });
            });
        </script>
    @endpush
@endsection
