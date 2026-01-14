@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Back Navigation -->
    <div class="mb-6">
        <a href="{{ route('customer.appointments.index') }}" 
           class="inline-flex items-center text-gray-600 hover:text-pink-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Appointments
        </a>
    </div>

    <!-- Page Header -->
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
            <i class="fas fa-calendar-plus text-white text-2xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Book Your Appointment</h1>
        <p class="text-gray-500 mt-2">Select your preferred service, staff, date and time</p>
    </div>

    <!-- Booking Form Card -->
    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
        <form action="{{ route('customer.appointments.store') }}" method="POST" id="appointmentForm">
            @csrf
            <input type="hidden" name="customer_id" value="{{ auth()->user()->customer->id }}">

            <div class="p-8">
                @if (isset($prefilledService) && isset($prefilledStaff))
                    <!-- Pre-filled Service & Staff Display -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Service Card -->
                        <div class="p-5 bg-gradient-to-br from-pink-50 to-rose-50 rounded-2xl border border-pink-100">
                            <div class="flex items-center mb-3">
                                <div class="w-10 h-10 bg-pink-500 rounded-xl flex items-center justify-center mr-3">
                                    <i class="fas fa-spa text-white"></i>
                                </div>
                                <span class="text-sm font-medium text-pink-600 uppercase tracking-wide">Selected Service</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $prefilledService->name }}</h3>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-sm text-gray-500">
                                    <i class="fas fa-clock mr-1"></i>{{ $prefilledService->duration_minutes }} minutes
                                </span>
                                <span class="text-lg font-bold text-pink-600">
                                    ₱{{ number_format($prefilledService->price_premium ?? $prefilledService->price_regular, 2) }}
                                </span>
                            </div>
                            <input type="hidden" name="service_id" id="service_id" value="{{ $prefilledService->id }}">
                            <input type="hidden" id="service_duration" value="{{ $prefilledService->duration_minutes }}">
                            <input type="hidden" id="service_price" value="{{ $prefilledService->price_regular }}">
                            <input type="hidden" id="service_price_premium" value="{{ $prefilledService->price_premium ?? $prefilledService->price_regular }}">
                            <input type="hidden" id="service_name" value="{{ $prefilledService->name }}">
                        </div>

                        <!-- Staff Card -->
                        <div class="p-5 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border border-blue-100">
                            <div class="flex items-center mb-3">
                                <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <span class="text-sm font-medium text-blue-600 uppercase tracking-wide">Selected Staff</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $prefilledStaff->user->full_name }}</h3>
                            @if($prefilledStaff->user->gender)
                                <p class="text-sm text-gray-500 mt-1">{{ $prefilledStaff->user->formatted_gender }}</p>
                            @endif
                            <input type="hidden" name="staff_id" id="staff_id" value="{{ $prefilledStaff->id }}">
                            <input type="hidden" id="staff_name" value="{{ $prefilledStaff->user->full_name }}">
                        </div>
                    </div>
                @else
                    <!-- Service Selection -->
                    <div class="mb-6">
                        <label for="service_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-spa text-pink-500 mr-2"></i>Select Service <span class="text-red-500">*</span>
                        </label>
                        <select name="service_id" id="service_id" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50 transition-all">
                            <option value="">Choose a service...</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}"
                                    data-duration="{{ $service->duration_minutes }}"
                                    data-price="{{ $service->price_regular }}"
                                    data-price-premium="{{ $service->price_premium ?? $service->price_regular }}">
                                    {{ $service->name }} ({{ $service->duration_minutes }} min) - ₱{{ number_format($service->price_premium ?? $service->price_regular, 2) }}
                                </option>
                            @endforeach
                        </select>
                        @error('service_id')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Staff Selection -->
                    <div class="mb-6">
                        <label for="staff_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user text-blue-500 mr-2"></i>Select Staff Member <span class="text-red-500">*</span>
                        </label>
                        <select name="staff_id" id="staff_id" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50 transition-all">
                            <option value="">Choose a staff member...</option>
                            @foreach ($staff as $staffMember)
                                <option value="{{ $staffMember->id }}">
                                    {{ $staffMember->user->full_name }}
                                    @if($staffMember->user->gender)
                                        ({{ $staffMember->user->formatted_gender }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('staff_id')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <!-- Date Selection -->
                <div class="mb-6">
                    <label for="appointment_date" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar text-green-500 mr-2"></i>Select Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="appointment_date" 
                           id="appointment_date"
                           min="{{ now()->format('Y-m-d') }}"
                           required
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50 transition-all">
                    @error('appointment_date')
                        <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                    @error('start_datetime')
                        <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <!-- Schedule Info -->
                <div id="schedule-info" class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-xl hidden">
                    <div class="flex items-center text-blue-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span id="schedule-message" class="text-sm"></span>
                    </div>
                </div>

                <!-- Available Time Slots -->
                <div id="available-times-container" class="mb-6 hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-clock text-purple-500 mr-2"></i>Available Time Slots <span class="text-red-500">*</span>
                    </label>
                    <div id="available-times-grid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                        <!-- Time slots dynamically inserted here -->
                    </div>
                    <p id="no-times-message" class="mt-3 text-sm text-red-600 hidden">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        No available time slots for this date.
                    </p>
                </div>

                <!-- Selected Time Display -->
                <div id="selected-time-display" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl hidden">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-green-700">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span class="font-medium">Selected Time:</span>
                        </div>
                        <span id="selected-time-text" class="text-lg font-bold text-green-800">--</span>
                    </div>
                </div>

                <!-- Hidden time fields -->
                <input type="hidden" name="appointment_time" id="appointment_time" value="">
                <input type="hidden" name="start_datetime" id="start_datetime" value="">

                <!-- Notes -->
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-sticky-note text-yellow-500 mr-2"></i>Special Requests or Notes
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                        placeholder="Any special requests, allergies, or notes for your appointment..."
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50 transition-all resize-none"></textarea>
                </div>

                <!-- Booking Summary -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-receipt text-pink-500 mr-2"></i>
                        Booking Summary
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Service</span>
                            <span id="service-summary" class="font-medium text-gray-900">--</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Duration</span>
                            <span id="duration-summary" class="font-medium text-gray-900">--</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Staff</span>
                            <span id="staff-summary" class="font-medium text-gray-900">--</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Date</span>
                            <span id="date-summary" class="font-medium text-gray-900">--</span>
                        </div>
                        <hr class="border-gray-200">
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-lg font-semibold text-gray-900">Total Amount</span>
                            <span id="price-summary" class="text-2xl font-bold text-pink-600">₱0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-8 py-6 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row justify-end gap-3">
                <a href="{{ route('customer.appointments.index') }}"
                   class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-100 transition-colors">
                    Cancel
                </a>
                <button type="submit" id="submitBtn"
                    class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-pink-500 to-rose-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0">
                    <i class="fas fa-calendar-check mr-2"></i>
                    Book Appointment
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Route URL for availability check (extracted to avoid linter issues with Blade syntax)
    const checkAvailabilityUrl = "{{ route('customer.appointments.checkAvailability') }}";
    
    const serviceSelect = document.getElementById('service_id');
    const staffSelect = document.getElementById('staff_id');
    const dateInput = document.getElementById('appointment_date');
    const timeInput = document.getElementById('appointment_time');
    const startDatetimeInput = document.getElementById('start_datetime');
    const submitBtn = document.getElementById('submitBtn');

    // Check if service and staff are pre-filled
    const isPrefilled = serviceSelect && serviceSelect.tagName === 'INPUT';

    let serviceDuration, servicePrice, servicePricePremium, serviceName, staffName;
    let isLoadingTimeSlots = false;
    let hasNoTimeSlots = false;

    if (isPrefilled) {
        serviceDuration = document.getElementById('service_duration')?.value;
        servicePrice = document.getElementById('service_price')?.value;
        servicePricePremium = document.getElementById('service_price_premium')?.value;
        serviceName = document.getElementById('service_name')?.value || '--';
        staffName = document.getElementById('staff_name')?.value || '--';
        updateSummary();
    } else {
        serviceSelect?.addEventListener('change', updateSummary);
        staffSelect?.addEventListener('change', updateSummary);
    }

    dateInput?.addEventListener('change', fetchTimeSlots);

    // Form validation
    document.getElementById('appointmentForm')?.addEventListener('submit', function(e) {
        const selectedTime = timeInput?.value;
        const selectedDate = dateInput?.value;
        
        if (!selectedDate) {
            e.preventDefault();
            alert('Please select a date for your appointment.');
            return false;
        }
        
        if (!selectedTime) {
            e.preventDefault();
            if (isLoadingTimeSlots) {
                alert('Please wait for the available time slots to load.');
            } else if (hasNoTimeSlots) {
                alert('No time slots are available for the selected date. Please select another date.');
            } else {
                alert('Please select an available time slot for your appointment.');
            }
            return false;
        }
    });

    function fetchTimeSlots() {
        const selectedDate = dateInput?.value;
        const staffId = staffSelect?.value;
        const serviceId = serviceSelect?.value;

        if (!selectedDate || !staffId || !serviceId) {
            resetTimeSlots();
            return;
        }

        isLoadingTimeSlots = true;
        hasNoTimeSlots = false;

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Loading...';
        }

        const formData = new FormData();
        formData.append('date', selectedDate);
        formData.append('staff_id', staffId);
        formData.append('service_id', serviceId);

        fetch(checkAvailabilityUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Failed to fetch time slots');
                });
            }
            return response.json();
        })
        .then(data => {
            isLoadingTimeSlots = false;
            
            const scheduleInfo = document.getElementById('schedule-info');
            const scheduleMessage = document.getElementById('schedule-message');
            const availableTimesContainer = document.getElementById('available-times-container');
            const availableTimesGrid = document.getElementById('available-times-grid');
            const noTimesMessage = document.getElementById('no-times-message');
            const timeDisplay = document.getElementById('selected-time-display');

            if (data.has_schedule && scheduleInfo && scheduleMessage) {
                scheduleInfo.classList.remove('hidden');
                scheduleMessage.textContent = `Staff is available on ${data.day_of_week}.`;
            } else {
                scheduleInfo?.classList.add('hidden');
            }

            if (data.available_times && data.available_times.length > 0) {
                hasNoTimeSlots = false;
                availableTimesContainer?.classList.remove('hidden');
                noTimesMessage?.classList.add('hidden');
                
                if (availableTimesGrid) {
                    availableTimesGrid.innerHTML = '';
                    
                    data.available_times.forEach((timeSlot) => {
                        const timeButton = document.createElement('button');
                        timeButton.type = 'button';
                        timeButton.className = 'time-slot-btn px-3 py-2.5 text-sm font-medium rounded-xl border-2 border-gray-200 bg-white text-gray-700 hover:bg-pink-50 hover:border-pink-300 hover:text-pink-600 focus:outline-none transition-all';
                        timeButton.textContent = timeSlot.formatted_time;
                        timeButton.dataset.time = timeSlot.time;
                        
                        timeButton.addEventListener('click', function() {
                            // Remove active from all
                            availableTimesGrid.querySelectorAll('.time-slot-btn').forEach(btn => {
                                btn.classList.remove('bg-pink-500', 'border-pink-500', 'text-white');
                                btn.classList.add('bg-white', 'border-gray-200', 'text-gray-700');
                            });
                            
                            // Activate clicked
                            this.classList.remove('bg-white', 'border-gray-200', 'text-gray-700');
                            this.classList.add('bg-pink-500', 'border-pink-500', 'text-white');
                            
                            // Set values
                            timeInput.value = timeSlot.time;
                            startDatetimeInput.value = `${selectedDate} ${timeSlot.time}`;
                            
                            // Update display
                            if (timeDisplay) {
                                document.getElementById('selected-time-text').textContent = timeSlot.formatted_time;
                                timeDisplay.classList.remove('hidden');
                            }
                            
                            updateSummary();
                        });
                        
                        availableTimesGrid.appendChild(timeButton);
                    });

                    // Auto-select first time slot
                    const firstTime = data.available_times[0];
                    timeInput.value = firstTime.time;
                    startDatetimeInput.value = `${selectedDate} ${firstTime.time}`;
                    
                    const firstButton = availableTimesGrid.querySelector('.time-slot-btn');
                    if (firstButton) {
                        firstButton.classList.remove('bg-white', 'border-gray-200', 'text-gray-700');
                        firstButton.classList.add('bg-pink-500', 'border-pink-500', 'text-white');
                    }
                    
                    if (timeDisplay) {
                        document.getElementById('selected-time-text').textContent = firstTime.formatted_time;
                        timeDisplay.classList.remove('hidden');
                    }
                }
            } else {
                hasNoTimeSlots = true;
                availableTimesContainer?.classList.add('hidden');
                
                if (noTimesMessage) {
                    noTimesMessage.classList.remove('hidden');
                    noTimesMessage.textContent = data.has_schedule 
                        ? 'No available time slots for this date.'
                        : `Staff is not available on ${data.day_of_week}. Please select another date.`;
                }
                
                resetTimeInputs();
                timeDisplay?.classList.add('hidden');
            }
            
            updateSummary();
        })
        .catch(error => {
            isLoadingTimeSlots = false;
            hasNoTimeSlots = true;
            console.error('Error:', error);
            resetTimeSlots();
            
            const noTimesMessage = document.getElementById('no-times-message');
            if (noTimesMessage) {
                noTimesMessage.textContent = 'Error loading time slots. Please try again.';
                noTimesMessage.classList.remove('hidden');
            }
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-calendar-check mr-2"></i> Book Appointment';
            }
        });
    }

    function resetTimeSlots() {
        document.getElementById('available-times-container')?.classList.add('hidden');
        document.getElementById('selected-time-display')?.classList.add('hidden');
        document.getElementById('no-times-message')?.classList.add('hidden');
        document.getElementById('schedule-info')?.classList.add('hidden');
        resetTimeInputs();
        updateSummary();
    }

    function resetTimeInputs() {
        if (timeInput) timeInput.value = '';
        if (startDatetimeInput) startDatetimeInput.value = '';
    }

    // Enable fetch when staff/service changes (dropdown mode)
    if (!isPrefilled) {
        staffSelect?.addEventListener('change', function() {
            if (this.value && dateInput?.value && serviceSelect?.value) fetchTimeSlots();
        });
        serviceSelect?.addEventListener('change', function() {
            if (this.value && dateInput?.value && staffSelect?.value) fetchTimeSlots();
        });
    }

    function updateSummary() {
        if (isPrefilled) {
            document.getElementById('service-summary').textContent = serviceName;
            document.getElementById('duration-summary').textContent = serviceDuration ? `${serviceDuration} minutes` : '--';
            document.getElementById('staff-summary').textContent = staffName;
            document.getElementById('price-summary').textContent = `₱${parseFloat(servicePricePremium || servicePrice || 0).toFixed(2)}`;
        } else {
            const selectedService = serviceSelect?.options[serviceSelect.selectedIndex];
            document.getElementById('service-summary').textContent = selectedService?.text?.split(' (')[0] || '--';
            document.getElementById('duration-summary').textContent = selectedService?.dataset?.duration ? `${selectedService.dataset.duration} minutes` : '--';
            
            const selectedStaff = staffSelect?.options[staffSelect.selectedIndex];
            document.getElementById('staff-summary').textContent = selectedStaff?.text || '--';
            
            const price = selectedService?.dataset?.pricePremium || selectedService?.dataset?.price || '0';
            document.getElementById('price-summary').textContent = `₱${parseFloat(price).toFixed(2)}`;
        }

        // Update date
        const date = dateInput?.value;
        document.getElementById('date-summary').textContent = date 
            ? new Date(date).toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' })
            : '--';
    }

    // Initial update
    updateSummary();
});
</script>
@endpush
@endsection
