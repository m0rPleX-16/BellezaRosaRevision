@php
    // Status configuration
    $statusConfig = [
        'scheduled' => [
            'color' => 'blue',
            'bg' => 'bg-blue-50',
            'text' => 'text-blue-700',
            'border' => 'border-blue-200',
            'icon' => 'fa-clock',
            'label' => 'Scheduled'
        ],
        'confirmed' => [
            'color' => 'green',
            'bg' => 'bg-green-50',
            'text' => 'text-green-700',
            'border' => 'border-green-200',
            'icon' => 'fa-check-circle',
            'label' => 'Confirmed'
        ],
        'in_progress' => [
            'color' => 'yellow',
            'bg' => 'bg-yellow-50',
            'text' => 'text-yellow-700',
            'border' => 'border-yellow-200',
            'icon' => 'fa-spinner',
            'label' => 'In Progress'
        ],
        'completed' => [
            'color' => 'purple',
            'bg' => 'bg-purple-50',
            'text' => 'text-purple-700',
            'border' => 'border-purple-200',
            'icon' => 'fa-check-double',
            'label' => 'Completed'
        ],
        'cancelled' => [
            'color' => 'red',
            'bg' => 'bg-red-50',
            'text' => 'text-red-700',
            'border' => 'border-red-200',
            'icon' => 'fa-times-circle',
            'label' => 'Cancelled'
        ],
        'failed' => [
            'color' => 'orange',
            'bg' => 'bg-orange-50',
            'text' => 'text-orange-700',
            'border' => 'border-orange-200',
            'icon' => 'fa-exclamation-triangle',
            'label' => 'Failed'
        ],
        'no_show' => [
            'color' => 'gray',
            'bg' => 'bg-gray-50',
            'text' => 'text-gray-700',
            'border' => 'border-gray-200',
            'icon' => 'fa-user-slash',
            'label' => 'No Show'
        ],
    ];
    
    $status = $statusConfig[$appointment->status ?? 'scheduled'] ?? $statusConfig['scheduled'];
    
    // Date/time formatting with null safety
    $appointmentDate = \Carbon\Carbon::parse($appointment->start_datetime ?? now());
    $endTime = $appointment->end_datetime ? \Carbon\Carbon::parse($appointment->end_datetime) : $appointmentDate->copy()->addMinutes(60);
    $durationInMinutes = $appointmentDate->diffInMinutes($endTime);
    $hours = floor($durationInMinutes / 60);
    $minutes = $durationInMinutes % 60;
    $duration = ($hours > 0 ? $hours . 'h ' : '') . ($minutes > 0 ? $minutes . 'm' : ($hours == 0 ? '0m' : ''));
    
    // Appointment state checks
    $isUpcoming = in_array($appointment->status, ['scheduled', 'confirmed']);
    $canCancel = in_array($appointment->status, ['scheduled', 'confirmed']) && $appointmentDate->isFuture();
    $canReschedule = in_array($appointment->status, ['scheduled', 'confirmed']) && $appointmentDate->diffInHours(now()) > 24;
    $isToday = $appointmentDate->isToday();
    $isPast = $appointmentDate->isPast();
    
    // Time until/since appointment
    $timeRelative = $appointmentDate->diffForHumans();
@endphp

<div class="bg-white rounded-2xl border {{ $status['border'] }} overflow-hidden hover:shadow-lg transition-all duration-300 group">
    <!-- Status Strip -->
    <div class="h-1.5 {{ str_replace('50', '400', $status['bg']) }}"></div>
    
    <div class="p-5">
        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
            <!-- Service Icon & Info -->
            <div class="flex items-start flex-1 min-w-0">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center flex-shrink-0 shadow-lg">
                    <i class="fas fa-spa text-white text-xl"></i>
                </div>
                
                <div class="ml-4 min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h3 class="text-lg font-bold text-gray-900 truncate">
                            {{ $appointment->service->name ?? 'Service Unavailable' }}
                        </h3>
                        
                        <!-- Status Badge -->
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $status['bg'] }} {{ $status['text'] }}">
                            <i class="fas {{ $status['icon'] }} mr-1.5"></i>
                            {{ $status['label'] }}
                        </span>
                        
                        @if($isToday && $isUpcoming)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 animate-pulse">
                                <i class="fas fa-sun mr-1.5"></i>
                                Today
                            </span>
                        @endif
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500">
                        <span class="flex items-center">
                            <i class="fas fa-user mr-1.5 text-gray-400"></i>
                            {{ $appointment->staff?->user?->full_name ?? 'Staff Unavailable' }}
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-clock mr-1.5 text-gray-400"></i>
                            {{ $duration }}
                        </span>
                        <span class="text-gray-400">{{ $timeRelative }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Date/Time & Price -->
            <div class="flex flex-col items-end text-right flex-shrink-0 pl-4 border-l border-gray-100 hidden lg:flex">
                <div class="flex items-center text-gray-500 mb-1">
                    <i class="fas fa-calendar mr-2"></i>
                    <span class="font-medium">{{ $appointmentDate->format('D, M j, Y') }}</span>
                </div>
                <div class="text-2xl font-bold text-gray-900">
                    {{ $appointmentDate->format('g:i A') }}
                </div>
                <div class="text-lg font-semibold text-pink-600 mt-1">
                    ₱{{ number_format($appointment->total_amount ?? 0, 2) }}
                </div>
            </div>
        </div>
        
        <!-- Mobile Date/Time/Price -->
        <div class="lg:hidden mt-4 pt-4 border-t border-gray-100">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Date</p>
                    <p class="font-semibold text-gray-900">{{ $appointmentDate->format('M j') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Time</p>
                    <p class="font-semibold text-gray-900">{{ $appointmentDate->format('g:i A') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Amount</p>
                    <p class="font-semibold text-pink-600">₱{{ number_format($appointment->total_amount ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        
        <!-- Notes (if exists) -->
        @if(!empty($appointment->notes))
            <div class="mt-4 p-3 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">
                    <i class="fas fa-sticky-note mr-1"></i> Notes
                </p>
                <p class="text-sm text-gray-700">{{ $appointment->notes }}</p>
            </div>
        @endif
        
        <!-- Action Buttons -->
        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap gap-2 justify-end">
            @if($canCancel)
                <form action="{{ route('customer.appointments.cancel', $appointment) }}" method="POST" class="inline">
                    @csrf
                    <button type="button" 
                            onclick="if(confirm('Are you sure you want to cancel this appointment? This action cannot be undone.')) { this.form.submit(); }"
                            class="inline-flex items-center px-4 py-2 border border-gray-200 text-sm font-medium rounded-xl text-gray-600 bg-white hover:bg-red-50 hover:border-red-200 hover:text-red-600 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </button>
                </form>
            @endif
            
            @if($canReschedule)
                <a href="{{ route('customer.appointments.create') }}?service_id={{ $appointment->service_id }}&staff_id={{ $appointment->staff_id }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-200 text-sm font-medium rounded-xl text-gray-600 bg-white hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 transition-colors">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Reschedule
                </a>
            @endif
            
            <a href="{{ route('customer.appointments.show', $appointment) }}" 
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-pink-500 to-rose-600 text-sm font-medium rounded-xl text-white hover:shadow-lg transition-all">
                <i class="fas fa-eye mr-2"></i>
                View Details
            </a>
        </div>
    </div>
</div>
