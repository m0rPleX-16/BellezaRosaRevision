<!-- [file name]: resources/views/dashboard/payments/show.blade.php -->
@extends('layouts.dashboard')

@section('title', 'Payment Details - Belleza Rosa')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Payment Details</h1>
            <a href="{{ route('dashboard.payments.index') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-xl transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Payments
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Payment Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Payment Card -->
                <div class="card">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Payment Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Payment ID</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900">#{{ $payment->id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Amount Due</label>
                            <p class="mt-1 text-2xl font-bold text-green-600">₱{{ number_format($payment->amount, 2) }}</p>
                        </div>
                        @if($payment->method === 'cash' && $payment->customer_payment)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Amount Received</label>
                                <p class="mt-1 text-xl font-semibold text-blue-600">₱{{ number_format($payment->customer_payment, 2) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Change</label>
                                <p class="mt-1 text-xl font-bold text-purple-600">₱{{ number_format($payment->change_amount ?? 0, 2) }}</p>
                            </div>
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Payment Method</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900 capitalize">{{ $payment->method }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <span
                                class="mt-1 inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                            {{ $payment->status == 'paid' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $payment->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $payment->status == 'failed' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $payment->status == 'refunded' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>

                        <!-- GCash Reference Number (Visible only for GCash payments) -->
                        @if ($payment->method === 'gcash' && $payment->reference_number)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500">GCash Reference Number</label>
                                <div class="mt-1 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <code class="text-lg font-bold text-blue-800">{{ $payment->reference_number }}</code>
                                    <p class="text-sm text-blue-600 mt-1">This is the transaction reference from GCash</p>
                                </div>
                            </div>
                        @endif

                        <!-- Bank Transfer Reference -->
                        @if ($payment->method === 'bank_transfer' && $payment->reference_number)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500">Bank Reference Number</label>
                                <div class="mt-1 p-3 bg-purple-50 border border-purple-200 rounded-lg">
                                    <code class="text-lg font-bold text-purple-800">{{ $payment->reference_number }}</code>
                                </div>
                            </div>
                        @endif

                        @if ($payment->notes)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500">Notes</label>
                                <p class="mt-1 text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $payment->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Appointment Information -->
                <div class="card">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Appointment Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Service</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $payment->appointment->service->name ?? 'Service Unavailable' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Date & Time</label>
                            <p class="mt-1 text-gray-900">
                                {{ $payment->appointment->start_datetime?->format('F j, Y g:i A') ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Staff</label>
                            <p class="mt-1 text-gray-900">
                                {{ $payment->appointment->staff->user->full_name ?? 'Unassigned' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Appointment Status</label>
                            <span
                                class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $payment->appointment->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $payment->appointment->status == 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $payment->appointment->status == 'scheduled' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                {{ str_replace('_', ' ', ucfirst($payment->appointment->status)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Information & Actions -->
            <div class="space-y-6">
                <!-- Customer Card -->
                <div class="card">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Customer Information</h2>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Name</label>
                            <p class="mt-1 font-semibold text-gray-900">{{ $payment->customer->full_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Phone</label>
                            <p class="mt-1 text-gray-900">{{ $payment->customer->phone ?? 'N/A' }}</p>
                        </div>
                        @if ($payment->customer?->email)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Email</label>
                                <p class="mt-1 text-gray-900">{{ $payment->customer->email }}</p>
                            </div>
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Total Visits</label>
                            <p class="mt-1 text-gray-900">{{ $payment->customer->total_visits ?? 0 }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Total Spent</label>
                            <p class="mt-1 font-semibold text-green-600">
                                ₱{{ number_format($payment->customer->total_spent ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Actions -->
                <div class="card">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Update Payment Status</h2>
                    <form action="{{ route('dashboard.payments.status', $payment) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Status</label>
                                <select name="status" id="paymentStatus" data-old-value="{{ $payment->status }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                                    <option value="pending" {{ $payment->status == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="paid" {{ $payment->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="failed" {{ $payment->status == 'failed' ? 'selected' : '' }}>Failed
                                    </option>
                                    <option value="refunded" {{ $payment->status == 'refunded' ? 'selected' : '' }}>
                                        Refunded</option>
                                </select>
                            </div>

                            @if (in_array($payment->method, ['gcash', 'bank_transfer']))
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-2">Reference Number</label>
                                    <input type="text" name="reference_number" value="{{ $payment->reference_number }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                                        placeholder="Enter reference number">
                                </div>
                            @endif

                            <div id="cancellationReasonDiv" class="hidden">
                                <label class="block text-sm font-medium text-gray-500 mb-2">Reason <span
                                        class="text-red-500">*</span></label>
                                <textarea name="cancellation_reason" rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                                    placeholder="Please provide a reason for this status change"></textarea>
                            </div>

                            <button type="submit" onclick="return confirmPaymentStatusChange(event)"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition">
                                Update Payment Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Show/hide cancellation reason field
        document.getElementById('paymentStatus').addEventListener('change', function() {
            const status = this.value;
            const reasonDiv = document.getElementById('cancellationReasonDiv');
            if (['failed', 'refunded'].includes(status)) {
                reasonDiv.classList.remove('hidden');
            } else {
                reasonDiv.classList.add('hidden');
            }
        });

        // Confirmation for payment status change
        async function confirmPaymentStatusChange(event) {
            event.preventDefault();
            const form = event.target.closest('form');
            const statusSelect = document.getElementById('paymentStatus');
            const newStatus = statusSelect.value;
            const oldStatus = statusSelect.dataset.oldValue;

            if (newStatus === oldStatus) {
                Swal.fire({
                    title: 'No Change',
                    text: 'Please select a different status.',
                    icon: 'info',
                    confirmButtonColor: '#3b82f6'
                });
                return false;
            }

            // Check if reason is required
            const reasonDiv = document.getElementById('cancellationReasonDiv');
            if (!reasonDiv.classList.contains('hidden')) {
                const reasonInput = form.querySelector('textarea[name="cancellation_reason"]');
                if (!reasonInput.value.trim()) {
                    Swal.fire({
                        title: 'Reason Required',
                        text: 'Please provide a reason for marking payment as ' + newStatus + '.',
                        icon: 'warning',
                        confirmButtonColor: '#dc2626'
                    });
                    reasonInput.focus();
                    return false;
                }
            }

            const statusLabels = {
                'pending': 'Pending',
                'paid': 'Paid',
                'failed': 'Failed',
                'refunded': 'Refunded'
            };

            const result = await Swal.fire({
                title: 'Confirm Payment Status Change',
                html: `Are you sure you want to change payment status from <strong>${statusLabels[oldStatus]}</strong> to <strong>${statusLabels[newStatus]}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: newStatus === 'paid' ? '#10b981' : '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, update it',
                cancelButtonText: 'Cancel'
            });

            if (result.isConfirmed) {
                form.submit();
            }

            return false;
        }
    </script>
@endsection
