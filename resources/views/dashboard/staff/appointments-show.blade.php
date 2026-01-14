@extends('layouts.dashboard')

@section('title', 'Appointment Details - Staff Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Appointment Details</h1>
        <a href="{{ route('staff.appointments') }}" class="text-sm text-blue-600 hover:text-blue-800">
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
                'in_progress' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                'completed' => 'bg-purple-100 text-purple-800 border-purple-200',
                'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                'no_show' => 'bg-gray-100 text-gray-800 border-gray-200',
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
                <!-- Customer Information -->
                <div class="space-y-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h2>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Name</label>
                                <p class="mt-1 text-base text-gray-900">{{ $appointment->customer->full_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Phone</label>
                                <p class="mt-1 text-base text-gray-900">{{ $appointment->customer->phone ?? 'N/A' }}</p>
                            </div>
                            @if($appointment->customer->email)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Email</label>
                                <p class="mt-1 text-base text-gray-900">{{ $appointment->customer->email }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Service & Appointment Details -->
                <div class="space-y-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Service & Appointment Details</h2>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Service</label>
                                <p class="mt-1 text-base text-gray-900">{{ $appointment->service->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Duration</label>
                                <p class="mt-1 text-base text-gray-900">{{ $durationText }}</p>
                            </div>
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
                                <label class="block text-sm font-medium text-gray-500">Price</label>
                                <p class="mt-1 text-lg font-semibold text-gray-900">₱{{ number_format($appointment->total_amount, 2) }}</p>
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

            <!-- Status Update Actions -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-500 mb-4">Update Status</h3>
                <div class="flex flex-wrap gap-3">
                    @if(in_array($appointment->status, ['scheduled', 'confirmed']))
                        <form action="{{ route('dashboard.appointments.status', $appointment) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="status" value="in_progress">
                            <button type="submit" 
                                    onclick="return confirm('Mark this appointment as in progress?');"
                                    class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-md">
                                <i class="fas fa-play-circle mr-2"></i>Start Appointment
                            </button>
                        </form>
                    @endif

                    @if($appointment->status === 'in_progress')
                        <form action="{{ route('dashboard.appointments.status', $appointment) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" 
                                    onclick="return confirm('Mark this appointment as completed?');"
                                    class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md">
                                <i class="fas fa-check-circle mr-2"></i>Mark as Completed
                            </button>
                        </form>
                    @endif

                    @if(in_array($appointment->status, ['scheduled', 'confirmed', 'in_progress']))
                        <form action="{{ route('dashboard.appointments.status', $appointment) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="status" value="no_show">
                            <button type="submit" 
                                    onclick="return confirm('Mark this appointment as no-show?');"
                                    class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md">
                                <i class="fas fa-times-circle mr-2"></i>Mark as No-Show
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-6">
                <a href="{{ route('staff.appointments') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Appointments
                </a>
            </div>
        </div>
    </div>
</div>
@endsection