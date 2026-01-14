@extends('layouts.dashboard')

@section('title', 'Appointments - Belleza Rosa')

@section('content')
    @php
        $statusPill = [
            'scheduled' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
            'confirmed' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            'in_progress' => 'bg-amber-50 text-amber-800 ring-amber-600/20',
            'completed' => 'bg-slate-100 text-slate-700 ring-slate-600/20',
            'cancelled' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
            'failed' => 'bg-red-50 text-red-700 ring-red-600/20',
            'no_show' => 'bg-orange-50 text-orange-700 ring-orange-600/20',
        ];
    @endphp
    <div class="space-y-6" data-appointments-store="{{ route('dashboard.appointments.store') }}"
        data-opening-time="{{ substr($openingTime, 0, 5) }}" data-closing-time="{{ substr($closingTime, 0, 5) }}"
        data-max-days-ahead="{{ $maxDaysAhead }}" data-slot-interval="{{ $slotInterval }}">
        <!-- Header -->
        <div class="flex flex-col gap-1">
            <h1 class="text-3xl font-bold text-gray-900">Appointments</h1>
            <p class="text-sm text-gray-500">Manage bookings, update statuses, and record payments.</p>
        </div>

        <!-- Statistics Cards -->
        <div class="flex flex-col md:flex-row gap-4 md:gap-6">
            <!-- Total Appointments -->
            <div class="flex-1 card border-l-4 border-blue-500 min-w-0">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center min-w-0">
                        <div class="p-3 bg-blue-100 rounded-xl mr-3 flex-shrink-0">
                            <i class="fas fa-calendar-check text-blue-600 text-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-medium text-gray-500 truncate">Total Appointments</h3>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 ml-2 flex-shrink-0">
                        {{ $totalAppointments ?? 0 }}
                    </p>
                </div>
            </div>

            <!-- Today's Appointments -->
            <div class="flex-1 card border-l-4 border-green-500 min-w-0">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center min-w-0">
                        <div class="p-3 bg-green-100 rounded-xl mr-3 flex-shrink-0">
                            <i class="fas fa-calendar-day text-green-600 text-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-medium text-gray-500 truncate">Today's Appointments</h3>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 ml-2 flex-shrink-0">
                        {{ $todayAppointments ?? 0 }}
                    </p>
                </div>
            </div>

            <!-- Completed This Month -->
            <div class="flex-1 card border-l-4 border-red-500 min-w-0">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center min-w-0">
                        <div class="p-3 bg-red-100 rounded-xl mr-3 flex-shrink-0">
                            <i class="fas fa-check-circle text-red-600 text-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-medium text-gray-500 truncate">Completed (Month)</h3>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 ml-2 flex-shrink-0">
                        {{ $completedThisMonth ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card">
            <form action="{{ route('dashboard.appointments.index') }}" method="GET"
                class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex flex-col md:flex-row gap-4 flex-1">
                    <!-- Search Input -->
                    <div class="relative w-full md:w-80">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <i class="fas fa-magnifying-glass"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search name, phone, service..."
                            class="w-full px-10 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                    </div>

                    <!-- Status Filter -->
                    <select name="status"
                        class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none md:w-48">
                        <option value="">All Status</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled
                        </option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed
                        </option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress
                        </option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                        </option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                        </option>
                    </select>

                    <!-- Date Filter -->
                    <div class="relative md:w-48">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <i class="fas fa-calendar"></i>
                        </span>
                        <input type="date" name="date" value="{{ request('date') }}"
                            class="w-full px-10 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                    </div>
                </div>

                <!-- Filter and Clear Buttons -->
                <div class="flex gap-2">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl transition focus:outline-none focus:ring-4 focus:ring-blue-200">
                        <i class="fas fa-filter mr-2"></i> Apply Filters
                    </button>

                    @if (request()->hasAny(['search', 'status', 'date']))
                        <a href="{{ route('dashboard.appointments.index') }}"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-xl transition focus:outline-none focus:ring-4 focus:ring-gray-200">
                            <i class="fas fa-times mr-2"></i> Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Appointments Table -->
        <div class="card">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-blue-600 text-white sticky top-0 z-10">
                            <th class="px-4 py-3 text-left text-sm font-semibold">Date & Time</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Customer</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold hidden lg:table-cell">Service</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold hidden lg:table-cell">Staff</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold hidden sm:table-cell">Amount</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($appointments as $appointment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm">
                                    <div class="font-medium text-gray-900">
                                        {{ $appointment->start_datetime->format('M j, Y') }}</div>
                                    <div class="text-gray-500">{{ $appointment->start_datetime->format('g:i A') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $appointment->customer->full_name ?? 'Deleted Customer' }}</div>
                                    <div class="text-sm text-gray-500">{{ $appointment->customer->phone ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 hidden lg:table-cell">{{ $appointment->service->name ?? 'Deleted Service' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 hidden lg:table-cell">
                                    {{ $appointment->staff->user->full_name ?? 'Unassigned' }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 hidden sm:table-cell">
                                    ₱{{ number_format($appointment->total_amount, 2) }}
                                    @if($appointment->addons && $appointment->addons->count() > 0)
                                        <div class="text-xs text-purple-600 font-normal">
                                            +{{ $appointment->addons->count() }} addon{{ $appointment->addons->count() > 1 ? 's' : '' }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusPill[$appointment->status] ?? 'bg-gray-100 text-gray-700 ring-gray-600/20' }}">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>
                                        {{ str_replace('_', ' ', ucfirst($appointment->status)) }}
                                    </span>
                                    @if ($appointment->cancellation_reason)
                                        <div class="mt-1 text-xs text-gray-500 italic"
                                            title="{{ $appointment->cancellation_reason }}">
                                            Reason: {{ Str::limit($appointment->cancellation_reason, 30) }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Actions Column -->
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2 items-center">
                                        <!-- View -->
                                        <a href="{{ route('dashboard.appointments.show', $appointment) }}"
                                            class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                                            title="View Appointment">
                                            <i class="fas fa-eye mr-2"></i>View
                                        </a>

                                        <!-- Status Update -->
                                        <form action="{{ route('dashboard.appointments.status', $appointment) }}"
                                            method="POST" class="inline" id="statusForm{{ $appointment->id }}">
                                            @csrf
                                            <select name="status" data-old-value="{{ $appointment->status }}"
                                                data-appointment-id="{{ $appointment->id }}"
                                                onchange="confirmStatusChange(this)"
                                                class="text-xs font-semibold rounded-lg px-3 py-1.5 bg-blue-600 text-white focus:outline-none focus:ring-4 focus:ring-blue-200">
                                                <option value="scheduled"
                                                    {{ $appointment->status == 'scheduled' ? 'selected' : '' }}>Scheduled
                                                </option>
                                                <option value="confirmed"
                                                    {{ $appointment->status == 'confirmed' ? 'selected' : '' }}>Confirmed
                                                </option>
                                                <option value="in_progress"
                                                    {{ $appointment->status == 'in_progress' ? 'selected' : '' }}>In
                                                    Progress</option>
                                                <option value="completed"
                                                    {{ $appointment->status == 'completed' ? 'selected' : '' }}>Completed
                                                </option>
                                                <option value="cancelled"
                                                    {{ $appointment->status == 'cancelled' ? 'selected' : '' }}>Cancelled
                                                </option>
                                                <option value="failed"
                                                    {{ $appointment->status == 'failed' ? 'selected' : '' }}>Failed
                                                </option>
                                                <option value="no_show"
                                                    {{ $appointment->status == 'no_show' ? 'selected' : '' }}>No Show
                                                </option>
                                            </select>
                                        </form>

                                        <!-- Edit -->
                                        <a href="{{ route('dashboard.appointments.edit', $appointment) }}"
                                            class="inline-flex items-center justify-center rounded-lg border border-blue-100 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 shadow-sm transition hover:bg-blue-100"
                                            title="Edit Appointment">
                                            <i class="fas fa-pen-to-square mr-2"></i>Edit
                                        </a>

                                        <!-- Cancel Appointment Button (only if not already cancelled or failed) -->
                                        @if (!$appointment->isCancelled() && !$appointment->isFailed())
                                            <a href="{{ route('dashboard.appointments.cancel.form', $appointment) }}"
                                                class="inline-flex items-center justify-center rounded-lg border border-rose-100 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 shadow-sm transition hover:bg-rose-100"
                                                title="Cancel Appointment"
                                                onclick="return confirmCancelAppointment(event)">
                                                <i class="fas fa-ban mr-2"></i>Cancel
                                            </a>
                                        @endif

                                        <!-- Payment Actions -->
                                        @if ($appointment->status === 'completed')
                                            @if ($appointment->payment && $appointment->payment->isPaid())
                                                <!-- View Payment Details -->
                                                <a href="{{ route('dashboard.payments.show', $appointment->payment) }}"
                                                    class="inline-flex items-center justify-center rounded-lg border border-indigo-100 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 shadow-sm transition hover:bg-indigo-100"
                                                    title="View Payment Details">
                                                    <i class="fas fa-credit-card mr-2"></i>Payment
                                                </a>
                                            @else
                                                <!-- Create New Payment -->
                                                <a href="{{ route('dashboard.payments.create', ['appointment' => $appointment->id]) }}"
                                                    class="inline-flex items-center justify-center rounded-lg border border-emerald-100 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-100"
                                                    title="Record Payment">
                                                    <i class="fas fa-money-bill-wave mr-2"></i>Record
                                                </a>
                                            @endif
                                        @endif

                                        <!-- Paid Indicator -->
                                        @if ($appointment->payment && $appointment->payment->isPaid())
                                            <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/20"
                                                title="Fully Paid">
                                                <i class="fas fa-check-circle"></i> Paid
                                            </span>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-calendar-times text-4xl mb-3 text-gray-300"></i>
                                    <p>No appointments found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($appointments->hasPages())
                <div class="mt-6">
                    {{ $appointments->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Booking Modal -->
    <div id="bookingModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto">
            <div class="bg-gradient-to-r from-blue-900 to-blue-700 text-white p-6 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold">New Appointment</h2>
                    <button onclick="closeModal('bookingModal')"
                        class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <form action="{{ route('dashboard.appointments.store') }}" method="POST" data-toast="true"
                    data-toast-message="Appointment created successfully!" data-toast-type="success">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="block text-gray-700 font-semibold mb-2">Customer</label>
                            <select name="customer_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                                <option value="">Select Customer</option>
                                @foreach ($customers ?? [] as $customer)
                                    @if (isset($customer) && is_object($customer))
                                        <option value="{{ $customer->id ?? '' }}">{{ $customer->full_name ?? '' }} -
                                            {{ $customer->phone ?? '' }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="block text-gray-700 font-semibold mb-2">Service</label>
                            <select name="service_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                                <option value="">Select Service</option>
                                @foreach ($services ?? [] as $service)
                                    @if (isset($service) && is_object($service))
                                        <option value="{{ $service->id ?? '' }}">{{ $service->name ?? '' }} -
                                            ₱{{ number_format($service->price_regular ?? 0, 2) }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="block text-gray-700 font-semibold mb-2">Staff</label>
                            <select name="staff_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                                <option value="">Select Staff</option>
                                @foreach ($staff ?? [] as $staffMember)
                                    <option value="{{ $staffMember->id }}">{{ $staffMember->user->full_name }}
                                        ({{ $staffMember->formatted_specialty }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Date & Time Input -->
                        <div class="form-group">
                            <label class="block text-gray-700 font-semibold mb-2">Date & Time</label>
                            <input type="datetime-local" name="start_datetime" required
                                min="{{ now()->format('Y-m-d\T') . substr($openingTime, 0, 5) }}"
                                max="{{ now()->addDays($maxDaysAhead)->format('Y-m-d\T') . substr($closingTime, 0, 5) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none appointment-time"
                                step="{{ $slotInterval * 60 }}" onchange="validateAppointmentTime(this)">
                            <p class="text-sm text-gray-500 mt-1">
                                Business hours:
                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $openingTime)->format('g:i A') }} -
                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $closingTime)->format('g:i A') }}
                            </p>
                            <p class="text-xs text-red-600 mt-1 hidden" id="timeError">
                                Please select a time within business hours.
                            </p>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-4">
                        <button type="button" onclick="closeModal('bookingModal')"
                            class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-yellow-500 hover:bg-yellow-400 text-blue-900 font-bold rounded-xl shadow-lg transform hover:scale-105 transition">
                            <i class="fas fa-calendar-check mr-2"></i> Book Appointment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Status change configurations
        const statusConfig = {
            'scheduled': {
                title: 'Mark as Scheduled',
                message: 'This will mark the appointment as scheduled. The customer will be notified of their appointment details.',
                icon: 'info',
                confirmText: 'Yes, mark as scheduled',
                confirmColor: '#3b82f6',
                showCancel: true
            },
            'confirmed': {
                title: 'Confirm Appointment',
                message: 'This will confirm the appointment. The customer will receive a confirmation notification.',
                icon: 'success',
                confirmText: 'Yes, confirm appointment',
                confirmColor: '#10b981',
                showCancel: true
            },
            'in_progress': {
                title: 'Start Service',
                message: 'Mark this appointment as in progress? This will notify staff to prepare for the service.',
                icon: 'info',
                confirmText: 'Yes, start service',
                confirmColor: '#f59e0b',
                showCancel: true
            },
            'completed': {
                title: 'Complete Appointment',
                message: 'Mark this appointment as completed? This will finalize the service and allow for payment processing.',
                icon: 'question',
                confirmText: 'Yes, complete appointment',
                confirmColor: '#10b981',
                showCancel: true
            },
            'cancelled': {
                title: 'Cancel Appointment',
                message: 'Are you sure you want to cancel this appointment? This action requires a cancellation reason.',
                icon: 'warning',
                confirmText: 'Yes, cancel appointment',
                confirmColor: '#dc2626',
                showCancel: true,
                requiresReason: true
            },
            'failed': {
                title: 'Mark as Failed',
                message: 'Mark this appointment as failed? Please ensure you have notified the customer and documented the reason.',
                icon: 'error',
                confirmText: 'Yes, mark as failed',
                confirmColor: '#dc2626',
                showCancel: true,
                requiresReason: true
            },
            'no_show': {
                title: 'Mark as No Show',
                message: 'Mark this appointment as a no-show? This will be recorded in the appointment history.',
                icon: 'warning',
                confirmText: 'Yes, mark as no-show',
                confirmColor: '#f59e0b',
                showCancel: true
            }
        };

        // Format status for display
        function formatStatus(status) {
            if (!status) return '';
            return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
        }

        // Handle status change with confirmation
        async function confirmStatusChange(selectElement) {
            const newStatus = selectElement.value;
            const oldStatus = selectElement.dataset.oldValue;
            const appointmentId = selectElement.dataset.appointmentId;
            const form = document.getElementById('statusForm' + appointmentId);

            // If no change, do nothing
            if (newStatus === oldStatus) {
                return;
            }

            // Get configuration for this status
            const config = statusConfig[newStatus] || {
                title: 'Change Status',
                message: `Are you sure you want to change the status to ${formatStatus(newStatus)}?`,
                icon: 'question',
                confirmText: 'Yes, change status',
                confirmColor: '#3b82f6',
                showCancel: true
            };

            // Show confirmation dialog
            const result = await Swal.fire({
                title: config.title,
                text: config.message,
                icon: config.icon,
                showCancelButton: config.showCancel,
                confirmButtonColor: config.confirmColor,
                cancelButtonColor: '#6b7280',
                confirmButtonText: config.confirmText,
                cancelButtonText: 'Cancel',
                reverseButtons: true
            });

            if (result.isConfirmed) {
                // If this status requires a reason, show another input
                if (config.requiresReason) {
                    const {
                        value: reason
                    } = await Swal.fire({
                        title: 'Reason Required',
                        input: 'text',
                        inputLabel: 'Please provide a reason for ' + newStatus.replace('_', ' ').toLowerCase(),
                        inputPlaceholder: 'Enter the reason...',
                        inputAttributes: {
                            'aria-label': 'Enter the reason',
                            required: 'required'
                        },
                        showCancelButton: true,
                        inputValidator: (value) => {
                            if (!value) {
                                return 'You need to provide a reason!';
                            }
                        }
                    });

                    if (reason) {
                        // Add reason to form and submit
                        let reasonInput = form.querySelector('input[name="cancellation_reason"]');
                        if (!reasonInput) {
                            reasonInput = document.createElement('input');
                            reasonInput.type = 'hidden';
                            reasonInput.name = 'cancellation_reason';
                            form.appendChild(reasonInput);
                        }
                        reasonInput.value = reason;
                        form.submit();
                    } else {
                        // Reset to old value if no reason provided
                        selectElement.value = oldStatus;
                        return false;
                    }
                } else {
                    // Submit the form for status changes that don't require a reason
                    form.submit();
                }
            } else {
                // Reset to old value if not confirmed
                selectElement.value = oldStatus;
                return false;
            }
        }

        // Confirmation for cancel appointment
        function confirmCancelAppointment(event) {
            event.preventDefault();
            const url = event.target.closest('a').href;

            Swal.fire({
                title: 'Cancel Appointment',
                html: 'You are about to cancel this appointment. This action requires a reason and cannot be undone.<br><br>You will be redirected to the cancellation form.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Continue to Cancellation',
                cancelButtonText: 'Keep Appointment',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state before redirect
                    Swal.fire({
                        title: 'Preparing Cancellation',
                        text: 'Please wait...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Redirect to cancellation form
                    window.location.href = url;
                }
            });

            return false;
        }
    </script>
    <script>
        // Function to validate appointment time
        function validateAppointmentTime(input) {
            if (!input.value) return true;

            const selectedDateTime = new Date(input.value);
            const selectedTime = selectedDateTime.toTimeString().split(' ')[0].substring(0, 5); // HH:mm format

            // Parse business hours (passed from PHP)
            // Get business hours from data attributes
            const contentDiv = document.querySelector('.space-y-6');
            const openingTime = contentDiv ? contentDiv.dataset.openingTime : '09:00';
            const closingTime = contentDiv ? contentDiv.dataset.closingTime : '20:00';

            const errorElement = document.getElementById('timeError');

            // Check if selected time is within business hours
            if (selectedTime < openingTime || selectedTime > closingTime) {
                errorElement.textContent = `Please select a time within business hours (${openingTime} - ${closingTime})`;
                errorElement.classList.remove('hidden');
                input.classList.add('border-red-500');
                input.classList.remove('border-gray-300');
                return false;
            } else {
                errorElement.classList.add('hidden');
                input.classList.remove('border-red-500');
                input.classList.add('border-gray-300');
                return true;
            }
        }

        // Enhanced openBookingModal function with time constraints
        function openBookingModal() {
            const modal = document.getElementById('bookingModal');
            modal.classList.remove('hidden');

            // Get current date/time
            const now = new Date();
            const today = now.toISOString().split('T')[0];
            const currentTime = now.toTimeString().split(' ')[0].substring(0, 5); // HH:mm

            // Salon settings from data attributes
            const contentDiv = document.querySelector('.space-y-6');
            const openingTime = contentDiv ? contentDiv.dataset.openingTime : '09:00';
            const closingTime = contentDiv ? contentDiv.dataset.closingTime : '20:00';
            const maxDaysAhead = parseInt(contentDiv ? contentDiv.dataset.maxDaysAhead : 30);
            const slotInterval = parseInt(contentDiv ? contentDiv.dataset.slotInterval : 15);

            // Calculate max date
            const maxDate = new Date(now);
            maxDate.setDate(maxDate.getDate() + maxDaysAhead);
            const maxDateStr = maxDate.toISOString().split('T')[0];

            // Set min time: if current time is before opening, use opening time
            let minTime = openingTime;
            if (currentTime > openingTime && currentTime < closingTime) {
                // Round current time to nearest slot interval
                const [currentHour, currentMinute] = currentTime.split(':').map(Number);
                const roundedMinutes = Math.ceil(currentMinute / slotInterval) * slotInterval;

                let roundedHour = currentHour;
                let roundedMin = roundedMinutes;

                if (roundedMinutes >= 60) {
                    roundedHour = currentHour + 1;
                    roundedMin = 0;
                }

                minTime = `${roundedHour.toString().padStart(2, '0')}:${roundedMin.toString().padStart(2, '0')}`;
            }

            // If minTime is after closing time, set to next day's opening
            if (minTime >= closingTime) {
                // Move to next day
                const tomorrow = new Date(now);
                tomorrow.setDate(tomorrow.getDate() + 1);
                const tomorrowStr = tomorrow.toISOString().split('T')[0];
                minTime = openingTime;

                // Update all datetime inputs in the modal
                const dateTimeInputs = document.querySelectorAll('.appointment-time');
                dateTimeInputs.forEach(input => {
                    input.min = `${tomorrowStr}T${minTime}`;
                    input.max = `${maxDateStr}T${closingTime}`;
                    input.step = slotInterval * 60; // Convert minutes to seconds
                });
            } else {
                // Update all datetime inputs in the modal
                const dateTimeInputs = document.querySelectorAll('.appointment-time');
                dateTimeInputs.forEach(input => {
                    input.min = `${today}T${minTime}`;
                    input.max = `${maxDateStr}T${closingTime}`;
                    input.step = slotInterval * 60; // Convert minutes to seconds
                });
            }

            // Clear any previous errors
            const errorElement = document.getElementById('timeError');
            if (errorElement) {
                errorElement.classList.add('hidden');
            }
        }

        // Override form submission to validate time
        document.addEventListener('DOMContentLoaded', function() {
            const contentDiv = document.querySelector('.space-y-6');
            const appointmentsStoreUrl = contentDiv ? contentDiv.dataset.appointmentsStore : '';
            const appointmentForm = appointmentsStoreUrl ? document.querySelector(
                `form[action="${appointmentsStoreUrl}"]`) : null;
            if (appointmentForm) {
                appointmentForm.addEventListener('submit', function(e) {
                    const dateTimeInput = document.querySelector('.appointment-time');
                    if (dateTimeInput && !validateAppointmentTime(dateTimeInput)) {
                        e.preventDefault();
                        const contentDiv2 = document.querySelector('.space-y-6');
                        const openingTime = contentDiv2 ? contentDiv2.dataset.openingTime : '09:00';
                        const closingTime = contentDiv2 ? contentDiv2.dataset.closingTime : '20:00';
                        alert(
                            `Please select a time within business hours: ${openingTime} - ${closingTime}`
                        );
                        dateTimeInput.focus();
                    }
                });
            }
        });

        // Keep your existing closeModal and window.onclick functions
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('fixed')) {
                event.target.classList.add('hidden');
            }
        }
    </script>
@endsection
