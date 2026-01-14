@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <!-- Back Navigation -->
    <div class="mb-4">
        <a href="{{ route('customer.appointments.index') }}" 
           class="inline-flex items-center text-gray-600 hover:text-[var(--primary)] transition-colors">
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
                'gradient' => 'from-red-500 to-red-600',
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

    <!-- Compact Appointment Detail Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Compact Status Banner -->
        <div class="bg-gradient-to-r {{ $status['gradient'] }} p-4 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas {{ $status['icon'] }} text-lg"></i>
                    </div>
                    <div>
                        <p class="text-white/80 text-sm font-medium">Appointment Status</p>
                        <h2 class="text-xl font-bold">{{ $status['label'] }}</h2>
                    </div>
                </div>
                <div class="mt-2 sm:mt-0 text-white/90">
                    @if($appointmentDate->isFuture())
                        <i class="fas fa-clock mr-1"></i>{{ $appointmentDate->diffForHumans() }}
                    @elseif($appointmentDate->isPast())
                        <i class="fas fa-history mr-1"></i>{{ $appointmentDate->diffForHumans() }}
                    @else
                        <i class="fas fa-sun mr-1"></i>Today
                    @endif
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Compact Service & Staff Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <!-- Compact Service Card -->
                <div class="p-4 bg-gradient-to-br from-[var(--primary)] to-[var(--primary-light)] rounded-xl border border-blue-100">
                    <div class="flex items-center mb-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-[var(--primary)] to-[var(--primary-dark)] rounded-lg flex items-center justify-center mr-2">
                            <i class="fas fa-spa text-white text-lg"></i>
                        </div>
                        <span class="text-sm font-semibold text-[var(--primary)] uppercase tracking-wide">Service</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $appointment->service->name ?? 'Service Unavailable' }}</h3>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-clock mr-1"></i>{{ $durationText ?: 'N/A' }}
                        </span>
                        <span class="text-lg font-bold text-[var(--gold)]">
                            ₱{{ number_format($appointment->total_amount ?? 0, 2) }}
                        </span>
                    </div>
                </div>

                <!-- Compact Staff Card -->
                <div class="p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
                    <div class="flex items-center mb-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mr-2">
                            <i class="fas fa-user text-white text-lg"></i>
                        </div>
                        <span class="text-sm font-semibold text-blue-600 uppercase tracking-wide">Staff Member</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $appointment->staff?->user?->full_name ?? 'Unassigned' }}</h3>
                    @if($appointment->staff && $appointment->staff->formatted_specialty)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-700">
                            <i class="fas fa-star mr-1"></i>
                            {{ $appointment->staff->formatted_specialty }} Specialist
                        </span>
                    @endif
                </div>
            </div>

            <!-- Compact Date & Time Details -->
            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">
                    <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>Appointment Schedule
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Date</p>
                        <p class="text-lg font-bold text-gray-900">{{ $appointmentDate->format('M j') }}</p>
                        <p class="text-gray-600">{{ $appointmentDate->format('Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Time</p>
                        <p class="text-lg font-bold text-gray-900">{{ $appointmentDate->format('g:i A') }}</p>
                        <p class="text-gray-600">to {{ $endDate->format('g:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Duration</p>
                        <p class="text-lg font-bold text-gray-900">{{ $durationText }}</p>
                        <p class="text-gray-600">{{ $duration }} min</p>
                    </div>
                </div>
            </div>

            <!-- Compact Notes Section -->
            @if(!empty($appointment->notes))
                <div class="bg-amber-50 rounded-xl p-4 mb-6 border border-amber-200">
                    <h3 class="text-sm font-semibold text-amber-700 uppercase tracking-wide mb-2">
                        <i class="fas fa-sticky-note mr-2"></i>Special Requests / Notes
                    </h3>
                    <p class="text-gray-700">{{ $appointment->notes }}</p>
                </div>
            @endif

            <!-- Addons Section -->
            @if($appointment->addons && $appointment->addons->count() > 0)
                <div class="bg-purple-50 rounded-xl p-4 mb-6 border border-purple-200">
                    <h3 class="text-sm font-semibold text-purple-700 uppercase tracking-wide mb-3">
                        <i class="fas fa-plus-circle mr-2"></i>Additional Services
                    </h3>
                    
                    <!-- Compact Addons List with Duration -->
                    <div class="space-y-2 mb-3">
                        @foreach($appointment->addons as $index => $addon)
                            <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-purple-200">
                                <div class="flex items-center flex-1">
                                    @if($addon->isServiceAddon())
                                        <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-spa text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $addon->display_name }}</div>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-xs text-purple-600 bg-purple-100 px-2 py-1 rounded-full">Salon Service</span>
                                                @if($addon->service && $addon->service->duration_minutes > 0)
                                                    <span class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded-full">
                                                        <i class="fas fa-clock mr-1"></i>{{ $addon->service->duration_minutes }} min
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 bg-gradient-to-br from-gray-500 to-gray-600 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-plus-circle text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $addon->display_name }}</div>
                                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">Custom Addon</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-purple-600">{{ $addon->formatted_price }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Duration Summary -->
                    <div class="bg-purple-100 rounded-lg p-3 border border-purple-200">
                        <div class="text-sm font-medium text-purple-800 mb-2">
                            <i class="fas fa-clock mr-2"></i>Duration Breakdown
                        </div>
                        <div class="space-y-1 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-700">{{ $appointment->service->name }}:</span>
                                <span class="font-medium">{{ $appointment->service->duration_minutes }} min</span>
                            </div>
                            @if($appointment->addons->where('service_id', '!=', null)->count() > 0)
                                @foreach($appointment->addons->where('service_id', '!=', null) as $addon)
                                    @if($addon->service && $addon->service->duration_minutes > 0)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 text-xs pl-4">{{ $addon->service->name }}:</span>
                                            <span class="font-medium text-xs">+{{ $addon->service->duration_minutes }} min</span>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                            <div class="flex justify-between pt-2 border-t border-purple-200 mt-2">
                                <span class="font-semibold text-purple-800">Total Duration:</span>
                                <span class="font-bold text-purple-800">
                                    {{ \Carbon\Carbon::parse($appointment->start_datetime)->diffInMinutes(\Carbon\Carbon::parse($appointment->end_datetime)) }} min
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Compact Pricing Breakdown -->
                    <div class="bg-white rounded-lg p-3 border border-purple-300">
                        <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">
                            <i class="fas fa-calculator mr-1"></i>Pricing Breakdown
                        </h4>
                        <div class="space-y-2">
                            <!-- Base Service -->
                            <div class="flex justify-between items-center py-1 border-b border-gray-100">
                                <div class="flex items-center">
                                    <div class="w-6 h-6 bg-blue-100 rounded flex items-center justify-center mr-2">
                                        <i class="fas fa-cut text-blue-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Base Service</div>
                                        <div class="text-xs text-gray-500">{{ $appointment->service->name }}</div>
                                    </div>
                                </div>
                                <div class="text-sm font-bold text-blue-900">{{ $appointment->formatted_base_price }}</div>
                            </div>
                            
                            <!-- Addons -->
                            @if($appointment->addons->count() > 0)
                                <div class="flex justify-between items-center py-1 border-b border-gray-100">
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 bg-purple-100 rounded flex items-center justify-center mr-2">
                                            <i class="fas fa-plus text-purple-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">Addons ({{ $appointment->addons->count() }})</div>
                                            <div class="text-xs text-gray-500">Additional services</div>
                                        </div>
                                    </div>
                                    <div class="text-sm font-bold text-purple-700">{{ $appointment->formatted_addons_total }}</div>
                                </div>
                            @endif
                            
                            <!-- Total -->
                            <div class="flex justify-between items-center py-2 bg-gradient-to-r from-blue-50 to-purple-50 rounded px-3">
                                <div class="flex items-center">
                                    <div class="w-6 h-6 bg-gradient-to-br from-blue-600 to-purple-600 rounded flex items-center justify-center mr-2">
                                        <i class="fas fa-receipt text-white text-xs"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">Total</div>
                                        <div class="text-xs text-gray-600">Base + Addons</div>
                                    </div>
                                </div>
                                <div class="text-lg font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                                    ₱{{ number_format($appointment->total_amount, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Compact Payment Information -->
            @if($appointment->payment)
                <div class="bg-green-50 rounded-xl p-4 mb-6 border border-green-200">
                    <h3 class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-3">
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
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-semibold 
                                {{ $appointment->payment->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ ucfirst($appointment->payment->status ?? 'Pending') }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Compact Action Buttons -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                @if($canCancel)
                    <form action="{{ route('customer.appointments.cancel', $appointment) }}" method="POST" class="w-full sm:w-auto">
                        @csrf
                        <button type="button" 
                                onclick="if(confirm('Are you sure you want to cancel this appointment? This action cannot be undone.')) { this.form.submit(); }"
                                class="w-full inline-flex items-center justify-center px-5 py-2.5 border-2 border-red-200 text-red-600 rounded-lg font-medium hover:bg-red-50 hover:border-red-300 transition-colors">
                            <i class="fas fa-times-circle mr-2"></i>
                            Cancel Appointment
                        </button>
                    </form>
                @endif

                @if($canReschedule)
                    <a href="{{ route('customer.appointments.create') }}?service_id={{ $appointment->service_id }}&staff_id={{ $appointment->staff_id }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 border-2 border-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-100 hover:border-gray-300 transition-colors">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Reschedule
                    </a>
                @endif

                <a href="{{ route('customer.appointments.index') }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-[var(--primary)] to-[var(--primary-dark)] text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Appointments
                </a>
            </div>
        </div>
    </div>

    <!-- Booking Another Appointment CTA -->
    <div class="mt-8 bg-gradient-to-br from-[var(--primary-light)] to-[var(--primary)] rounded-2xl p-8 text-white relative overflow-hidden">
        <div class="absolute right-0 top-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="relative z-10">
            <h3 class="text-xl font-bold mb-2">Need Another Appointment?</h3>
            <p class="text-[var(--primary-light)] mb-4">Book your next spa session and continue your wellness journey</p>
            <a href="{{ route('customer.staff') }}" 
               class="inline-flex items-center px-6 py-3 bg-white text-[var(--primary)] rounded-xl font-semibold hover:bg-[var(--primary-light)] transition-colors shadow-lg">
                <i class="fas fa-calendar-plus mr-2"></i>
                Book Now
            </a>
        </div>
    </div>
</div>
@endsection
