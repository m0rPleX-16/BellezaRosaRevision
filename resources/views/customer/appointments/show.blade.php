@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Appointment Details</h1>
            <a href="{{ route('customer.appointments.index') }}" 
               class="text-sm text-blue-600 hover:text-blue-800">
                &larr; Back to Appointments
            </a>
        </div>

        <!-- Appointment Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Status Banner -->
            @php
                $statusColors = [
                    'scheduled' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'confirmed' => 'bg-green-100 text-green-800 border-green-200',
                    'completed' => 'bg-purple-100 text-purple-800 border-purple-200',
                    'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                    'no_show' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                ];
                $statusColor = $statusColors[$appointment->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                $appointmentDate = \Carbon\Carbon::parse($appointment->start_datetime);
                $endDate = \Carbon\Carbon::parse($appointment->end_datetime);
                $duration = $appointmentDate->diffInMinutes($endDate);
                $hours = floor($duration / 60);
                $minutes = $duration % 60;
                $durationText = ($hours > 0 ? $hours . 'h ' : '') . ($minutes > 0 ? $minutes . 'm' : '');
            @endphp

            <div class="border-b {{ $statusColor }} p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium">
                            {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                        </span>
                    </div>
                    <div class="text-sm">
                        @if($appointmentDate->isFuture())
                            <span class="text-gray-600">Scheduled for {{ $appointmentDate->diffForHumans() }}</span>
                        @elseif($appointmentDate->isPast())
                            <span class="text-gray-600">Completed {{ $appointmentDate->diffForHumans() }}</span>
                        @else
                            <span class="text-gray-600">Today</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Appointment Details -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Service Information -->
                    <div class="space-y-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Service Information</h2>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Service</label>
                                    <p class="mt-1 text-base text-gray-900">{{ $appointment->service->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Duration</label>
                                    <p class="mt-1 text-base text-gray-900">{{ $durationText }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Price</label>
                                    <p class="mt-1 text-lg font-semibold text-gray-900">₱{{ number_format($appointment->total_amount, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Appointment Details -->
                    <div class="space-y-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Appointment Details</h2>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Date</label>
                                    <p class="mt-1 text-base text-gray-900">{{ $appointmentDate->format('l, F j, Y') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Time</label>
                                    <p class="mt-1 text-base text-gray-900">
                                        {{ $appointmentDate->format('g:i A') }} - {{ $endDate->format('g:i A') }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Staff Member</label>
                                    <p class="mt-1 text-base text-gray-900">
                                        {{ $appointment->staff->user->full_name ?? 'Unassigned' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                @if(!empty($appointment->notes))
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Special Requests / Notes</h3>
                    <p class="text-base text-gray-900">{{ $appointment->notes }}</p>
                </div>
                @endif

                <!-- Payment Information -->
                @if($appointment->payment)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Payment Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Amount</label>
                            <p class="mt-1 text-base text-gray-900">₱{{ number_format($appointment->payment->amount, 2) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Method</label>
                            <p class="mt-1 text-base text-gray-900">{{ ucfirst($appointment->payment->method ?? 'N/A') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <p class="mt-1 text-base text-gray-900">{{ ucfirst($appointment->payment->status ?? 'N/A') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="mt-8 pt-6 border-t border-gray-200 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                    @php
                        $isUpcoming = $appointment->status === 'scheduled' || $appointment->status === 'confirmed';
                        $canCancel = $isUpcoming && $appointmentDate->isFuture();
                        $canReschedule = $isUpcoming && $appointmentDate->diffInHours(now()) > 24;
                    @endphp

                    @if($canCancel)
                        <form action="{{ route('customer.appointments.cancel', $appointment) }}" method="POST" class="w-full sm:w-auto">
                            @csrf
                            <button type="button" 
                                    onclick="if(confirm('Are you sure you want to cancel this appointment?')) { this.form.submit(); }"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Cancel Appointment
                            </button>
                        </form>
                    @endif

                    @if($canReschedule)
                        <a href="{{ route('customer.appointments.create') }}?service_id={{ $appointment->service_id }}&staff_id={{ $appointment->staff_id }}" 
                           class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                            </svg>
                            Reschedule
                        </a>
                    @endif

                    <a href="{{ route('customer.appointments.index') }}" 
                       class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Back to Appointments
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
