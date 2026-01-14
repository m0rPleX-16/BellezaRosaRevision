@component('mail::layout')
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot

    @php
        $statusColors = [
            'paid' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'failed' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-blue-100 text-blue-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
        ];
        $statusColor = $statusColors[$payment->status] ?? 'bg-gray-100 text-gray-800';
    @endphp

    <div class="max-w-2xl mx-auto p-6">
        <!-- Status Banner -->
        <div class="{{ $statusColor }} p-4 rounded-lg mb-6 text-center">
            @if($payment->status === 'paid')
                <h1 class="text-2xl font-bold">Payment Confirmed!</h1>
                <p class="mt-2">Thank you for your payment. We've successfully processed your transaction.</p>
            @elseif($payment->status === 'failed')
                <h1 class="text-2xl font-bold">Payment Failed</h1>
                <p class="mt-2">We couldn't process your payment. Please try again or contact support.</p>
            @elseif($payment->status === 'refunded')
                <h1 class="text-2xl font-bold">Payment Refunded</h1>
                <p class="mt-2">Your payment has been refunded successfully.</p>
            @else
                <h1 class="text-2xl font-bold">Payment Status Updated</h1>
                <p class="mt-2">The status of your payment has been updated.</p>
            @endif
        </div>

        <!-- Greeting -->
        <p class="text-gray-700 mb-6">Hello {{ $notifiable->name }},</p>
        
        <!-- Status Message -->
        @if(isset($message) && $message)
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <p class="text-gray-700">{{ $message }}</p>
            </div>
        @endif

        <!-- Appointment Details -->
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Appointment Details</h2>
            <div class="space-y-2">
                <p><span class="font-medium">Service:</span> {{ $appointment->service->name ?? 'Service Unavailable' }}</p>
                <p><span class="font-medium">Date & Time:</span> {{ $appointment->start_datetime->format('l, F j, Y \a\t h:i A') }}</p>
                <p><span class="font-medium">Duration:</span> {{ $appointment->service->duration_minutes ?? 0 }} minutes</p>
                <p><span class="font-medium">Staff:</span> {{ $appointment->staff->user->full_name ?? 'To be assigned' }}</p>
                <p><span class="font-medium">Status:</span> <span class="capitalize">{{ $appointment->status }}</span></p>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Summary</h2>
            <div class="space-y-2">
                <p><span class="font-medium">Payment ID:</span> #{{ $payment->id }}</p>
                <p><span class="font-medium">Amount:</span> ₱{{ number_format($payment->amount, 2) }}</p>
                <p><span class="font-medium">Payment Method:</span> {{ ucfirst($payment->method) }}</p>
                <p><span class="font-medium">Status:</span> 
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColor }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </p>
                @if($payment->reference_number)
                    <p><span class="font-medium">Reference #:</span> {{ $payment->reference_number }}</p>
                @endif
                @if($payment->paid_at)
                    <p><span class="font-medium">Paid On:</span> {{ $payment->paid_at->format('M j, Y \a\t h:i A') }}</p>
                @endif
            </div>
        </div>

        <!-- Additional Notes -->
        @if(!empty($payment->notes))
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">{{ $payment->notes }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="mt-8 text-center">
            @component('mail::button', ['url' => route('dashboard.appointments.show', $appointment)])
                View Appointment
            @endcomponent
            
            <div class="mt-4">
                @component('mail::button', ['url' => route('dashboard.payments.show', $payment), 'color' => 'gray'])
                    View Payment Details
                @endcomponent
            </div>
            
            @if($payment->status === 'failed')
                <div class="mt-4">
                    @component('mail::button', ['url' => route('dashboard.payments.retry', $payment->id), 'color' => 'red'])
                        Retry Payment
                    @endcomponent
                </div>
            @endif
            
            <p class="mt-4 text-sm text-gray-500">
                Having trouble with your payment? <a href="mailto:{{ config('mail.support_email') }}" class="text-blue-600 hover:text-blue-800">Contact our support team</a>.
            </p>
        </div>
    </div>

    @if($payment->status === 'paid')
    <p>We look forward to seeing you for your appointment! If you have any questions, please don't hesitate to contact us.</p>
    @else
    <p>If you have any questions about this update, please contact our support team.</p>
    @endif

    Best regards,<br>
    The {{ config('app.name') }} Team

    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
            [Contact Support](mailto:support@example.com)
        @endcomponent
    @endslot
@endcomponent
