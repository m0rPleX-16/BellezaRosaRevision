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

    @php
        $statusConfig = [
            'scheduled' => [
                'color' => 'blue',
                'gradient' => 'from-blue-500 to-indigo-600',
                'bg' => 'bg-blue-50',
                'text' => 'text-blue-700',
                'border' => 'border-blue-200',
                'icon' => 'fa-clock',
                'label' => 'Scheduled'
            ],
            'confirmed' => [
                'color' => 'green',
                'gradient' => 'from-green-500 to-emerald-600',
                'bg' => 'bg-green-50',
                'text' => 'text-green-700',
                'border' => 'border-green-200',
                'icon' => 'fa-check-circle',
                'label' => 'Confirmed'
            ],
            'in_progress' => [
                'color' => 'yellow',
                'gradient' => 'from-yellow-500 to-amber-600',
                'bg' => 'bg-yellow-50',
                'text' => 'text-yellow-700',
                'border' => 'border-yellow-200',
                'icon' => 'fa-spinner',
                'label' => 'In Progress'
            ],
            'completed' => [
                'color' => 'purple',
                'gradient' => 'from-purple-500 to-indigo-600',
                'bg' => 'bg-purple-50',
                'text' => 'text-purple-700',
                'border' => 'border-purple-200',
                'icon' => 'fa-check-double',
                'label' => 'Completed'
            ],
            'cancelled' => [
                'color' => 'red',
                'gradient' => 'from-red-500 to-rose-600',
                'bg' => 'bg-red-50',
                'text' => 'text-red-700',
                'border' => 'border-red-200',
                'icon' => 'fa-times-circle',
                'label' => 'Cancelled'
            ],
            'failed' => [
                'color' => 'orange',
                'gradient' => 'from-orange-500 to-amber-600',
                'bg' => 'bg-orange-50',
                'text' => 'text-orange-700',
                'border' => 'border-orange-200',
                'icon' => 'fa-exclamation-triangle',
                'label' => 'Failed'
            ],
            'no_show' => [
                'color' => 'gray',
                'gradient' => 'from-gray-500 to-slate-600',
                'bg' => 'bg-gray-50',
                'text' => 'text-gray-700',
                'border' => 'border-gray-200',
                'icon' => 'fa-user-slash',
                'label' => 'No Show'
            ],
        ];
        
        $status = $statusConfig[$appointment->status ?? 'scheduled'] ?? $statusConfig['scheduled'];
        $appointmentDate = \Carbon\Carbon::parse($appointment->start_datetime ?? now());
        $endDate = $appointment->end_datetime ? \Carbon\Carbon::parse($appointment->end_datetime) : $appointmentDate->copy()->addMinutes(60);
        $duration = $appointmentDate->diffInMinutes($endDate);
        $hours = floor($duration / 60);
        $minutes = $duration % 60;
        $durationText = ($hours > 0 ? $hours . 'h ' : '') . ($minutes > 0 ? $minutes . 'm' : ($hours == 0 ? '0m' : ''));
        
        $isUpcoming = in_array($appointment->status, ['scheduled', 'confirmed']);
        $canCancel = $isUpcoming && $appointmentDate->isFuture();
        $canReschedule = $isUpcoming && $appointmentDate->diffInHours(now()) > 24;
    @endphp

    <!-- Appointment Detail Card -->
    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Status Banner -->
        <div class="bg-gradient-to-r {{ $status['gradient'] }} p-6 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas {{ $status['icon'] }} text-xl"></i>
                    </div>
                    <div>
                        <p class="text-white/80 text-sm font-medium">Appointment Status</p>
                        <h2 class="text-2xl font-bold">{{ $status['label'] }}</h2>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0 text-white/90">
                    @if($appointmentDate->isFuture())
                        <i class="fas fa-clock mr-2"></i>{{ $appointmentDate->diffForHumans() }}
                    @elseif($appointmentDate->isPast())
                        <i class="fas fa-history mr-2"></i>{{ $appointmentDate->diffForHumans() }}
                    @else
                        <i class="fas fa-sun mr-2"></i>Today
                    @endif
                </div>
            </div>
        </div>

        <div class="p-8">
            <!-- Service & Staff Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Service Card -->
                <div class="p-5 bg-gradient-to-br from-pink-50 to-rose-50 rounded-2xl border border-pink-100">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-spa text-white"></i>
                        </div>
                        <span class="text-sm font-semibold text-pink-600 uppercase tracking-wide">Service</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $appointment->service->name ?? 'Service Unavailable' }}</h3>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-clock mr-1"></i>{{ $durationText ?: 'N/A' }}
                        </span>
                        <span class="text-xl font-bold text-pink-600">
                            ₱{{ number_format($appointment->total_amount ?? 0, 2) }}
                        </span>
                    </div>
                </div>

                <!-- Staff Card -->
                <div class="p-5 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border border-blue-100">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <span class="text-sm font-semibold text-blue-600 uppercase tracking-wide">Staff Member</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $appointment->staff?->user?->full_name ?? 'Unassigned' }}</h3>
                    @if($appointment->staff && $appointment->staff->formatted_specialty)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-700">
                            <i class="fas fa-star mr-2"></i>
                            {{ $appointment->staff->formatted_specialty }} Specialist
                        </span>
                    @endif
                </div>
            </div>

            <!-- Date & Time Details -->
            <div class="bg-gray-50 rounded-2xl p-6 mb-8">
                <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-4">
                    <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>Appointment Schedule
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Date</p>
                        <p class="text-lg font-bold text-gray-900">{{ $appointmentDate->format('l') }}</p>
                        <p class="text-gray-600">{{ $appointmentDate->format('F j, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Time</p>
                        <p class="text-lg font-bold text-gray-900">{{ $appointmentDate->format('g:i A') }}</p>
                        <p class="text-gray-600">to {{ $endDate->format('g:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Duration</p>
                        <p class="text-lg font-bold text-gray-900">{{ $durationText }}</p>
                        <p class="text-gray-600">{{ $duration }} minutes total</p>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            @if(!empty($appointment->notes))
                <div class="bg-amber-50 rounded-2xl p-6 mb-8 border border-amber-200">
                    <h3 class="text-sm font-semibold text-amber-700 uppercase tracking-wide mb-3">
                        <i class="fas fa-sticky-note mr-2"></i>Special Requests / Notes
                    </h3>
                    <p class="text-gray-700">{{ $appointment->notes }}</p>
                </div>
            @endif

            <!-- Payment Information -->
            @if($appointment->payment)
                <div class="bg-green-50 rounded-2xl p-6 mb-8 border border-green-200">
                    <h3 class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-4">
                        <i class="fas fa-receipt mr-2"></i>Payment Information
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Amount</p>
                            <p class="text-lg font-bold text-gray-900">₱{{ number_format($appointment->payment->amount, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Method</p>
                            <p class="text-lg font-semibold text-gray-900">{{ ucfirst($appointment->payment->method ?? 'N/A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold 
                                {{ $appointment->payment->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ ucfirst($appointment->payment->status ?? 'Pending') }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="px-8 py-6 bg-gray-50 border-t border-gray-100">
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                @if($canCancel)
                    <form action="{{ route('customer.appointments.cancel', $appointment) }}" method="POST" class="w-full sm:w-auto">
                        @csrf
                        <button type="button" 
                                onclick="if(confirm('Are you sure you want to cancel this appointment? This action cannot be undone.')) { this.form.submit(); }"
                                class="w-full inline-flex items-center justify-center px-6 py-3 border-2 border-red-200 text-red-600 rounded-xl font-medium hover:bg-red-50 hover:border-red-300 transition-colors">
                            <i class="fas fa-times-circle mr-2"></i>
                            Cancel Appointment
                        </button>
                    </form>
                @endif

                @if($canReschedule)
                    <a href="{{ route('customer.appointments.create') }}?service_id={{ $appointment->service_id }}&staff_id={{ $appointment->staff_id }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border-2 border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-100 hover:border-gray-300 transition-colors">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Reschedule
                    </a>
                @endif

                <a href="{{ route('customer.appointments.index') }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-pink-500 to-rose-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Appointments
                </a>
            </div>
        </div>
    </div>

    <!-- Booking Another Appointment CTA -->
    <div class="mt-8 bg-gradient-to-r from-pink-500 via-rose-500 to-orange-400 rounded-2xl p-8 text-white text-center relative overflow-hidden">
        <div class="absolute right-0 top-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="relative z-10">
            <h3 class="text-xl font-bold mb-2">Need Another Appointment?</h3>
            <p class="text-pink-100 mb-4">Book your next spa session and continue your wellness journey</p>
            <a href="{{ route('customer.staff') }}" 
               class="inline-flex items-center px-6 py-3 bg-white text-pink-600 rounded-xl font-semibold hover:bg-pink-50 transition-colors shadow-lg">
                <i class="fas fa-calendar-plus mr-2"></i>
                Book Now
            </a>
        </div>
    </div>
</div>
@endsection
