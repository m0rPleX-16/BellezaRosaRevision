@extends('layouts.app')

@section('title', 'Cancel Appointment - Belleza Rosa')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <!-- Back Navigation -->
    <div class="mb-6">
        <a href="{{ route('customer.appointments.show', $appointment) }}" 
           class="inline-flex items-center text-gray-600 hover:text-[var(--primary)] transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Appointment Details
        </a>
    </div>

    <!-- Header -->
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-times-circle text-3xl text-red-500"></i>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Cancel Appointment</h1>
        <p class="text-gray-600">We're sorry to see you go. Please let us know why you need to cancel.</p>
    </div>

    <!-- Appointment Info -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-calendar-check mr-2 text-[var(--primary)]"></i>
            Appointment Details
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="flex items-center">
                <span class="font-medium text-gray-600 mr-2">Service:</span>
                <span class="text-gray-900 font-semibold">{{ $appointment->service->name }}</span>
            </div>
            <div class="flex items-center">
                <span class="font-medium text-gray-600 mr-2">Date:</span>
                <span class="text-gray-900">{{ $appointment->start_datetime->format('M j, Y') }}</span>
            </div>
            <div class="flex items-center">
                <span class="font-medium text-gray-600 mr-2">Time:</span>
                <span class="text-gray-900">{{ $appointment->start_datetime->format('g:i A') }}</span>
            </div>
            <div class="flex items-center">
                <span class="font-medium text-gray-600 mr-2">Staff:</span>
                <span class="text-gray-900">{{ $appointment->staff?->user?->full_name ?? 'Unassigned' }}</span>
            </div>
            <div class="flex items-center">
                <span class="font-medium text-gray-600 mr-2">Amount:</span>
                <span class="text-lg font-bold text-[var(--gold)]">₱{{ number_format($appointment->total_amount, 2) }}</span>
            </div>
            <div class="flex items-center">
                <span class="font-medium text-gray-600 mr-2">Status:</span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold 
                    {{ $appointment->status == 'scheduled' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $appointment->status == 'confirmed' ? 'bg-green-100 text-green-800' : '' }}">
                    {{ str_replace('_', ' ', ucfirst($appointment->status)) }}
                </span>
            </div>
        </div>
        
        @if($appointment->payment && $appointment->payment->isPaid())
        <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <h4 class="font-medium text-blue-900 mb-2 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Payment Information
            </h4>
            <div class="text-sm text-blue-800">
                <p><strong>Amount Paid:</strong> ₱{{ number_format($appointment->payment->amount, 2) }}</p>
                <p><strong>Payment Method:</strong> {{ ucfirst($appointment->payment->method) }}</p>
                <p class="mt-2 text-xs"><strong>Note:</strong> Refunds will be processed according to our cancellation policy. Please contact the salon for refund details.</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Cancellation Form -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <form action="{{ route('customer.appointments.cancel', $appointment) }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <!-- Cancellation Reason -->
                <div>
                    <label for="cancellation_reason" class="block text-lg font-semibold text-gray-900 mb-3">
                        <i class="fas fa-comment-dots mr-2 text-[var(--primary)]"></i>
                        Reason for Cancellation <span class="text-red-500">*</span>
                    </label>
                    <textarea name="cancellation_reason" id="cancellation_reason" rows="4" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-[var(--primary-light)] focus:border-[var(--primary)] outline-none transition-all resize-none"
                        placeholder="Please let us know why you need to cancel this appointment..."></textarea>
                    <p class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Your feedback helps us improve our service. This information will be kept confidential.
                    </p>
                </div>

                <!-- Common Reasons (Quick Select) -->
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-3">Common reasons (click to add):</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        <button type="button" 
                                onclick="document.getElementById('cancellation_reason').value = 'Schedule conflict - I have another commitment at this time.'"
                                class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors text-left">
                            <i class="fas fa-calendar-times mr-1"></i> Schedule conflict
                        </button>
                        <button type="button" 
                                onclick="document.getElementById('cancellation_reason').value = 'Emergency - Unexpected personal or family emergency.'"
                                class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors text-left">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Emergency
                        </button>
                        <button type="button" 
                                onclick="document.getElementById('cancellation_reason').value = 'Feeling unwell - Not feeling well enough to attend the appointment.'"
                                class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors text-left">
                            <i class="fas fa-user-md mr-1"></i> Feeling unwell
                        </button>
                        <button type="button" 
                                onclick="document.getElementById('cancellation_reason').value = 'Financial reasons - Unable to afford the service at this time.'"
                                class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors text-left">
                            <i class="fas fa-money-bill-wave mr-1"></i> Financial reasons
                        </button>
                        <button type="button" 
                                onclick="document.getElementById('cancellation_reason').value = 'Service no longer needed - My circumstances have changed.'"
                                class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors text-left">
                            <i class="fas fa-times-circle mr-1"></i> No longer needed
                        </button>
                        <button type="button" 
                                onclick="document.getElementById('cancellation_reason').value = 'Found alternative service - Found another provider that better suits my needs.'"
                                class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors text-left">
                            <i class="fas fa-search mr-1"></i> Found alternative
                        </button>
                    </div>
                </div>

                <!-- Cancellation Summary -->
                <div class="p-4 bg-amber-50 rounded-lg border border-amber-200">
                    <h4 class="font-semibold text-amber-900 mb-2 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        Cancellation Summary
                    </h4>
                    <ul class="text-sm text-amber-800 space-y-1">
                        <li>• Your appointment will be marked as <strong>Cancelled</strong></li>
                        <li>• Your cancellation reason will be recorded for our records</li>
                        <li>• The staff member's schedule will be freed up for this time slot</li>
                        @if($appointment->payment && $appointment->payment->isPaid())
                        <li>• You may be eligible for a refund according to our cancellation policy</li>
                        @endif
                        <li>• You can always book a new appointment when you're ready</li>
                    </ul>
                </div>

                <!-- Confirmation Checkbox -->
                <div>
                    <label class="flex items-start">
                        <input type="checkbox" name="confirm_cancellation" required 
                               class="mt-1 mr-3 w-4 h-4 text-[var(--primary)] border-gray-300 rounded focus:ring-[var(--primary)]">
                        <span class="text-sm text-gray-700">
                            I understand that cancelling this appointment will free up the time slot for other customers. 
                            I confirm that I want to proceed with the cancellation.
                        </span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('customer.appointments.show', $appointment) }}" 
                       class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition font-medium text-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Go Back
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all">
                        <i class="fas fa-times-circle mr-2"></i>
                        Confirm Cancellation
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Rebooking CTA -->
    <div class="mt-8 bg-gradient-to-br from-[var(--primary-light)] to-[var(--primary)] rounded-2xl p-8 text-white relative overflow-hidden">
        <div class="absolute right-0 top-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="relative z-10 text-center">
            <h3 class="text-xl font-bold mb-2">Need to Reschedule Instead?</h3>
            <p class="text-[var(--primary-light)] mb-4">If you'd like to book for a different time, we're here to help!</p>
            <a href="{{ route('customer.appointments.create') }}?service_id={{ $appointment->service_id }}&staff_id={{ $appointment->staff_id }}" 
               class="inline-flex items-center px-6 py-3 bg-white text-[var(--primary)] rounded-xl font-semibold hover:bg-[var(--primary-light)] transition-colors shadow-lg">
                <i class="fas fa-calendar-plus mr-2"></i>
                Book New Appointment
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for cancellation reason
    const textarea = document.getElementById('cancellation_reason');
    const maxLength = 500;
    
    // Add character counter display
    const counterDiv = document.createElement('div');
    counterDiv.className = 'text-sm text-gray-500 mt-1 text-right';
    counterDiv.innerHTML = `<span id="char-count">0</span> / ${maxLength} characters`;
    textarea.parentNode.insertBefore(counterDiv, textarea.nextSibling);
    
    textarea.addEventListener('input', function() {
        const length = this.value.length;
        document.getElementById('char-count').textContent = length;
        
        if (length > maxLength) {
            this.value = this.value.substring(0, maxLength);
            document.getElementById('char-count').textContent = maxLength;
        }
        
        // Change color based on length
        if (length > maxLength * 0.9) {
            counterDiv.classList.add('text-red-500');
            counterDiv.classList.remove('text-gray-500');
        } else {
            counterDiv.classList.remove('text-red-500');
            counterDiv.classList.add('text-gray-500');
        }
    });
    
    // Prevent form submission if reason is too short
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const reason = textarea.value.trim();
        if (reason.length < 10) {
            e.preventDefault();
            alert('Please provide a more detailed reason for cancellation (at least 10 characters).');
            textarea.focus();
        }
    });
});
</script>
@endpush
