@component('mail::layout')
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot

    @php
        $statusColors = [
            'scheduled' => 'bg-blue-100 text-blue-800',
            'confirmed' => 'bg-green-100 text-green-800',
            'completed' => 'bg-indigo-100 text-indigo-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'rescheduled' => 'bg-yellow-100 text-yellow-800',
            'no_show' => 'bg-gray-100 text-gray-800',
        ];
        $statusColor = $statusColors[$appointment->status] ?? 'bg-gray-100 text-gray-800';
        
        $statusMessages = [
            'scheduled' => 'Your appointment has been scheduled successfully!',
            'confirmed' => 'Your appointment is confirmed!',
            'completed' => 'Appointment completed successfully!',
            'cancelled' => 'Appointment has been cancelled.',
            'rescheduled' => 'Appointment has been rescheduled.',
            'no_show' => 'Missed appointment.',
        ];
        $statusMessage = $statusMessages[$appointment->status] ?? 'Your appointment status has been updated.';
    @endphp

    <div class="max-w-2xl mx-auto p-6">
        <!-- Status Banner -->
        <div class="{{ $statusColor }} p-4 rounded-lg mb-6 text-center">
            <h1 class="text-2xl font-bold">
                @if($appointment->status === 'scheduled') Appointment Scheduled
                @elseif($appointment->status === 'confirmed') Appointment Confirmed
                @elseif($appointment->status === 'completed') Appointment Completed
                @elseif($appointment->status === 'cancelled') Appointment Cancelled
                @elseif($appointment->status === 'rescheduled') Appointment Rescheduled
                @elseif($appointment->status === 'no_show') Missed Appointment
                @else Appointment Status Updated
                @endif
            </h1>
            <p class="mt-2">{{ $statusMessage }}</p>
        </div>

        <!-- Greeting -->
        <p class="text-gray-700 mb-6">Hello {{ $appointment->customer->name ?? 'Valued Customer' }},</p>
        
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
                <p><span class="font-medium">Status:</span> 
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColor }}">
                        {{ ucfirst($appointment->status) }}
                    </span>
                </p>
            </div>
        </div>

        @if($appointment->payment)
        <!-- Payment Summary -->
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h2>
            <div class="space-y-2">
                <p><span class="font-medium">Amount:</span> ₱{{ number_format($appointment->payment->amount, 2) }}</p>
                <p><span class="font-medium">Status:</span> 
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $appointment->payment->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($appointment->payment->status) }}
                    </span>
                </p>
                @if($appointment->payment->method)
                    <p><span class="font-medium">Payment Method:</span> {{ ucfirst($appointment->payment->method) }}</p>
                @endif
                @if($appointment->payment->reference_number)
                    <p><span class="font-medium">Reference #:</span> {{ $appointment->payment->reference_number }}</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Additional Notes -->
        @if(!empty($appointment->notes))
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">{{ $appointment->notes }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="mt-8 text-center">
            @component('mail::button', ['url' => route('customer.appointments.show', $appointment->id)])
                View Appointment
            @endcomponent
            
            @if(in_array($appointment->status, ['scheduled', 'confirmed']))
                <div class="mt-4">
                    @component('mail::button', [
                        'url' => route('customer.appointments.create', ['service_id' => $appointment->service_id, 'staff_id' => $appointment->staff_id]),
                        'color' => 'gray'
                    ])
                        Reschedule Appointment
                    @endcomponent
                </div>
                
                <div class="mt-4">
                    @component('mail::button', [
                        'url' => route('customer.appointments.index'),
                        'color' => 'red'
                    ])
                        Manage Appointments
                    @endcomponent
                </div>
            @endif
            
            <p class="mt-4 text-sm text-gray-500">
                Need help? <a href="mailto:{{ config('mail.support_email') }}" class="text-blue-600 hover:text-blue-800">Contact our support team</a>.
            </p>
        </div>
    </div>

    @if(in_array($appointment->status, ['scheduled', 'confirmed']))
    <p>We look forward to seeing you for your appointment! If you have any questions, please don't hesitate to contact us.</p>
    @elseif($appointment->status === 'completed')
    <p>Thank you for choosing us! We hope you enjoyed your service and look forward to seeing you again soon.</p>
    @else
    <p>If you have any questions about this update, please don't hesitate to contact our support team.</p>
    @endif

    Best regards,<br>
    The {{ config('app.name') }} Team

    @slot('footer')
        @component('mail::footer')
            {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
            [Contact Support](mailto:{{ config('mail.support_email', 'support@example.com') }})
        @endcomponent
    @endslot
@endcomponent
