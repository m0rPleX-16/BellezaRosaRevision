@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-[var(--gold-light)] py-4">
    <!-- Back Navigation -->
    <div class="max-w-6xl mx-auto px-4 mb-4">
        <a href="{{ route('customer.appointments.index') }}" 
           class="inline-flex items-center text-gray-600 hover:text-pink-600 transition-colors text-sm">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Appointments
        </a>
    </div>

    <!-- Compact Booking Form -->
    <div class="max-w-6xl mx-auto px-4">
        <form action="{{ route('customer.appointments.store') }}" method="POST" id="appointmentForm" class="bg-white rounded-2xl shadow-xl border border-gray-100">
            @csrf
            <input type="hidden" name="customer_id" value="{{ auth()->user()->customer->id }}">
            
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-[var(--primary)] to-[var(--primary-dark)] rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-calendar-plus text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Book Appointment</h1>
                            <p class="text-sm text-gray-600">Select service, staff, date and time</p>
                        </div>
                    </div>
                    <div id="price-display" class="text-right">
                        <p class="text-sm text-gray-500">Total</p>
                        <p id="price-summary" class="text-2xl font-bold text-[var(--gold)]">₱0.00</p>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="p-6">
                @if (isset($prefilledService) && isset($prefilledStaff))
                    <!-- Pre-filled Selections (Compact) -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-medium text-[var(--primary)] uppercase">Service</span>
                                <i class="fas fa-spa text-[var(--primary)]"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900 text-sm">{{ $prefilledService->name }}</h3>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-500">{{ $prefilledService->duration_minutes }} min</span>
                                <span class="text-sm font-bold text-[var(--gold)]">₱{{ number_format($prefilledService->price_premium ?? $prefilledService->price_regular, 2) }}</span>
                            </div>
                            <input type="hidden" name="service_id" id="service_id" value="{{ $prefilledService->id }}">
                            <input type="hidden" id="service_duration" value="{{ $prefilledService->duration_minutes }}">
                            <input type="hidden" id="service_price" value="{{ $prefilledService->price_regular }}">
                            <input type="hidden" id="service_price_premium" value="{{ $prefilledService->price_premium ?? $prefilledService->price_regular }}">
                            <input type="hidden" id="service_name" value="{{ $prefilledService->name }}">
                        </div>

                        <div class="p-4 bg-gradient-to-br from-[var(--gold-light)] to-[var(--gold)] rounded-xl border border-[var(--gold-dark)]">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-medium text-[var(--gold-dark)] uppercase">Staff</span>
                                <i class="fas fa-user text-[var(--gold)]"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900 text-sm">{{ $prefilledStaff->user->full_name }}</h3>
                            @if($prefilledStaff->user->gender)
                                <p class="text-xs text-gray-500 mt-1">{{ $prefilledStaff->user->formatted_gender }}</p>
                            @endif
                            <input type="hidden" name="staff_id" id="staff_id" value="{{ $prefilledStaff->id }}">
                            <input type="hidden" id="staff_name" value="{{ $prefilledStaff->user->full_name }}">
                        </div>
                    </div>
                @else
                    <!-- Service & Staff Selection (Side by side) -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="service_id" class="block text-xs font-semibold text-gray-700 mb-2">
                                <i class="fas fa-spa text-[var(--primary)] mr-1"></i>Service <span class="text-red-500">*</span>
                            </label>
                            <select name="service_id" id="service_id" required
                                class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50 transition-all">
                                <option value="">Choose service...</option>
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
                                <p class="mt-1 text-xs text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="staff_id" class="block text-xs font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user text-[var(--gold)] mr-1"></i>Staff <span class="text-red-500">*</span>
                            </label>
                            <select name="staff_id" id="staff_id" required
                                class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50 transition-all">
                                <option value="">Choose staff...</option>
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
                                <p class="mt-1 text-xs text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endif

                <!-- Date & Time Section (Compact) -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <!-- Date Selection -->
                    <div>
                        <label for="appointment_date" class="block text-xs font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar text-green-500 mr-1"></i>Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="appointment_date" 
                               id="appointment_date"
                               min="{{ now()->format('Y-m-d') }}"
                               required
                               class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50 transition-all">
                        @error('appointment_date')
                            <p class="mt-1 text-xs text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Selected Time Display -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">
                            <i class="fas fa-clock text-blue-500 mr-1"></i>Time <span class="text-red-500">*</span>
                        </label>
                        <div id="selected-time-display" class="hidden">
                            <div class="px-3 py-2.5 bg-blue-50 border border-blue-200 rounded-lg text-sm font-medium text-blue-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                <span id="selected-time-text">--</span>
                            </div>
                        </div>
                        <div id="no-time-selected" class="px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-500">
                            Select date first
                        </div>
                    </div>

                    <!-- Duration Display -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">
                            <i class="fas fa-hourglass-half text-orange-500 mr-1"></i>Duration
                        </label>
                        <div class="px-3 py-2.5 bg-orange-50 border border-orange-200 rounded-lg text-sm font-medium text-orange-800">
                            <span id="duration-display">-- min</span>
                        </div>
                    </div>
                </div>

                <!-- Schedule Info -->
                <div id="schedule-info" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
                    <div class="flex items-center text-blue-700">
                        <i class="fas fa-info-circle mr-2 text-sm"></i>
                        <span id="schedule-message" class="text-sm"></span>
                    </div>
                </div>

                <!-- Available Time Slots (Compact) -->
                <div id="available-times-container" class="mb-6 hidden">
                    <label class="block text-xs font-semibold text-gray-700 mb-2">
                        <i class="fas fa-clock text-blue-500 mr-1"></i>Available Time Slots
                    </label>
                    <div id="available-times-grid" class="grid grid-cols-6 gap-2 max-h-32 overflow-y-auto">
                        <!-- Time slots dynamically inserted here -->
                    </div>
                    <p id="no-times-message" class="mt-2 text-xs text-red-600 hidden">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        No available time slots for this date.
                    </p>
                </div>

                <!-- Hidden time fields -->
                <input type="hidden" name="appointment_time" id="appointment_time" value="">
                <input type="hidden" name="start_datetime" id="start_datetime" value="">

                <!-- Notes (Compact) -->
                <div class="mb-6">
                    <label for="notes" class="block text-xs font-semibold text-gray-700 mb-2">
                        <i class="fas fa-sticky-note text-[var(--gold)] mr-1"></i>Special Requests (Optional)
                    </label>
                    <textarea name="notes" id="notes" rows="2"
                        placeholder="Any special requests or notes..."
                        class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50 transition-all resize-none"></textarea>
                </div>

            </div>

            <!-- Form Actions (Sticky Footer) -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 rounded-b-2xl">
                <div class="flex justify-end gap-3">
                    <a href="{{ route('customer.appointments.index') }}"
                       class="inline-flex items-center justify-center px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-100 transition-colors text-sm">
                        Cancel
                    </a>
                    <button type="submit" id="submitBtn"
                        class="inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-[var(--primary)] to-[var(--primary-dark)] text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 text-sm">
                        <i class="fas fa-calendar-check mr-2"></i>
                        Book Appointment
                    </button>
                </div>
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
                        timeButton.className = 'time-slot-btn px-2 py-1.5 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-blue-50 hover:border-[var(--primary)] hover:text-[var(--primary)] focus:outline-none transition-all';
                        timeButton.textContent = timeSlot.formatted_time;
                        timeButton.dataset.time = timeSlot.time;
                        
                        timeButton.addEventListener('click', function() {
                            // Remove active from all
                            availableTimesGrid.querySelectorAll('.time-slot-btn').forEach(btn => {
                                btn.classList.remove('bg-[var(--primary)]', 'border-[var(--primary)]', 'text-white');
                                btn.classList.add('bg-white', 'border-gray-200', 'text-gray-700');
                            });
                            
                            // Activate clicked
                            this.classList.remove('bg-white', 'border-gray-200', 'text-gray-700');
                            this.classList.add('bg-[var(--primary)]', 'border-[var(--primary)]', 'text-white');
                            
                            // Set values
                            timeInput.value = timeSlot.time;
                            startDatetimeInput.value = `${selectedDate} ${timeSlot.time}`;
                            
                            // Update display
                            if (timeDisplay) {
                                document.getElementById('selected-time-text').textContent = timeSlot.formatted_time;
                                timeDisplay.classList.remove('hidden');
                                document.getElementById('no-time-selected').classList.add('hidden');
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
                        firstButton.classList.add('bg-[var(--primary)]', 'border-[var(--primary)]', 'text-white');
                    }
                    
                    if (timeDisplay) {
                        document.getElementById('selected-time-text').textContent = firstTime.formatted_time;
                        timeDisplay.classList.remove('hidden');
                        document.getElementById('no-time-selected').classList.add('hidden');
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
        document.getElementById('no-time-selected')?.classList.remove('hidden');
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
            // Update price display in header
            const price = parseFloat(servicePricePremium || servicePrice || 0);
            document.getElementById('price-summary').textContent = `₱${price.toFixed(2)}`;
            
            // Update duration display
            const durationDisplay = document.getElementById('duration-display');
            if (durationDisplay && serviceDuration) {
                durationDisplay.textContent = `${serviceDuration} min`;
            }
        } else {
            const selectedService = serviceSelect?.options[serviceSelect.selectedIndex];
            
            // Update price display in header
            const price = selectedService?.dataset?.pricePremium || selectedService?.dataset?.price || '0';
            document.getElementById('price-summary').textContent = `₱${parseFloat(price).toFixed(2)}`;
            
            // Update duration display
            const durationDisplay = document.getElementById('duration-display');
            if (durationDisplay && selectedService?.dataset?.duration) {
                durationDisplay.textContent = `${selectedService.dataset.duration} min`;
            }
        }
    }

    // Initial update
    updateSummary();
});
</script>
@endpush
@endsection
