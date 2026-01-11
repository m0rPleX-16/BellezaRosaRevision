@extends('layouts.dashboard')

@section('title', 'Cancel Appointment - Belleza Rosa')

@section('content')
<div class="space-y-6 max-w-2xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Cancel Appointment</h1>
        <a href="{{ route('dashboard.appointments.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-xl transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Appointments
        </a>
    </div>

    <!-- Appointment Info -->
    <div class="card">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Appointment Details</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-medium">Customer:</span> {{ $appointment->customer->full_name }}
            </div>
            <div>
                <span class="font-medium">Service:</span> {{ $appointment->service->name }}
            </div>
            <div>
                <span class="font-medium">Date:</span> {{ $appointment->start_datetime->format('M j, Y g:i A') }}
            </div>
            <div>
                <span class="font-medium">Staff:</span> {{ $appointment->staff->user->full_name ?? 'Unassigned' }}
            </div>
            <div>
                <span class="font-medium">Status:</span>
                <span class="text-xs font-semibold px-2 py-1 rounded-full
                    {{ $appointment->status == 'scheduled' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $appointment->status == 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $appointment->status == 'in_progress' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                    {{ str_replace('_', ' ', ucfirst($appointment->status)) }}
                </span>
            </div>
            <div>
                <span class="font-medium">Amount:</span> ₱{{ number_format($appointment->total_amount, 2) }}
            </div>
        </div>
        
        @if($appointment->payment)
        <div class="mt-4 p-3 bg-blue-50 rounded-lg">
            <h4 class="font-medium text-blue-900 mb-2">Payment Information</h4>
            <div class="grid grid-cols-2 gap-2 text-sm">
                <div>
                    <span class="font-medium">Status:</span>
                    <span class="font-semibold
                        {{ $appointment->payment->status == 'paid' ? 'text-green-600' : '' }}
                        {{ $appointment->payment->status == 'pending' ? 'text-yellow-600' : '' }}
                        {{ $appointment->payment->status == 'failed' ? 'text-red-600' : '' }}">
                        {{ ucfirst($appointment->payment->status) }}
                    </span>
                </div>
                <div>
                    <span class="font-medium">Method:</span> {{ ucfirst($appointment->payment->method) }}
                </div>
                <div>
                    <span class="font-medium">Amount Paid:</span> ₱{{ number_format($appointment->payment->amount, 2) }}
                </div>
                <div>
                    <span class="font-medium">Paid At:</span>
                    {{ $appointment->payment->paid_at ? $appointment->payment->paid_at->format('M j, Y g:i A') : 'Not paid' }}
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Cancellation Form -->
    <div class="card">
        <form action="{{ route('dashboard.appointments.cancel', $appointment) }}" method="POST">
            @csrf
            @method('POST')
            
            <div class="space-y-4">
                <!-- Status Selection -->
                <div class="form-group">
                    <label class="block text-gray-700 font-semibold mb-2">Cancellation Type *</label>
                    <div class="space-y-2">
                        <div class="flex items-center mb-3">
                            <input type="radio" id="status_cancelled" name="status" value="cancelled" checked class="mr-2">
                            <label for="status_cancelled" class="flex items-center cursor-pointer">
                                <span class="px-3 py-1 bg-orange-100 text-orange-800 rounded-lg mr-2 font-medium">Cancelled</span>
                                <span class="text-sm text-gray-600">(Customer or salon cancelled appointment)</span>
                            </label>
                        </div>
                        <div class="flex items-center mb-4">
                            <input type="radio" id="status_failed" name="status" value="failed" class="mr-2">
                            <label for="status_failed" class="flex items-center cursor-pointer">
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-lg mr-2 font-medium">Failed</span>
                                <span class="text-sm text-gray-600">(Payment failed, no-show, or service issue)</span>
                            </label>
                        </div>
                        <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <strong>Note:</strong> "Failed" is used when payment failed or customer no-show. 
                                "Cancelled" is used when appointment is cancelled by customer or salon.
                            </p>
                        </div>
                        <div class="mb-4">
                            <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-2">
                                Cancellation/Failure Reason <span class="text-red-500">*</span>
                            </label>
                            <textarea name="cancellation_reason" id="cancellation_reason" rows="4" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                                placeholder="Please provide a detailed reason for cancellation or failure (e.g., Customer no-show, Payment declined, Emergency cancellation, etc.)"></textarea>
                            <p class="text-xs text-gray-500 mt-1">This reason will be recorded in the database for tracking purposes.</p>
                        </div>
                        @if($appointment->payment && $appointment->payment->isPaid())
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Refund Information</label>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Refund Amount</label>
                                    <input type="number" name="refund_amount" step="0.01" min="0" 
                                        value="{{ $appointment->payment->amount }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Refund Method</label>
                                    <select name="refund_method" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                                        <option value="cash">Cash</option>
                                        <option value="gcash">GCash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="mb-4 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>Database Record:</strong> This cancellation will be recorded with:
                                <ul class="list-disc list-inside mt-2 text-xs">
                                    <li>Status: <span id="statusPreview">Cancelled</span></li>
                                    <li>Reason: [Your provided reason above]</li>
                                    <li>Cancelled by: {{ auth()->user()->full_name }}</li>
                                    <li>Cancelled at: [Current timestamp]</li>
                                </ul>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Cancellation Summary -->
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-semibold text-gray-900 mb-2">Cancellation Summary</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Appointment will be marked as <span id="statusPreview" class="font-semibold">Cancelled</span></li>
                        <li>• Cancellation reason will be recorded in the database</li>
                        <li>• Staff schedule will be freed up for this time slot</li>
                        <li>• Customer will be notified (if contact information available)</li>
                        @if($appointment->payment && $appointment->payment->isPaid())
                        <li>• Refund process will be initiated</li>
                        @endif
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 pt-4">
                    <a href="{{ route('dashboard.appointments.index') }}" 
                       class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                        Go Back
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg transform hover:scale-105 transition">
                        <i class="fas fa-times-circle mr-2"></i> Confirm Cancellation
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Update status preview
document.querySelectorAll('input[name="status"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const previewElements = document.querySelectorAll('#statusPreview');
        previewElements.forEach(el => {
            el.textContent = this.value.charAt(0).toUpperCase() + this.value.slice(1);
        });
    });
});

// Initialize status preview on page load
document.addEventListener('DOMContentLoaded', function() {
    const checkedRadio = document.querySelector('input[name="status"]:checked');
    if (checkedRadio) {
        const previewElements = document.querySelectorAll('#statusPreview');
        previewElements.forEach(el => {
            el.textContent = checkedRadio.value.charAt(0).toUpperCase() + checkedRadio.value.slice(1);
        });
    }
});
</script>
@endsection