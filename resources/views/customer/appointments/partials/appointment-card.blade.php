@php
    // Determine the status badge color
    $statusColors = [
        'scheduled' => 'bg-blue-100 text-blue-800',
        'confirmed' => 'bg-green-100 text-green-800',
        'in_progress' => 'bg-yellow-100 text-yellow-800',
        'completed' => 'bg-purple-100 text-purple-800',
        'cancelled' => 'bg-red-100 text-red-800',
        'failed' => 'bg-orange-100 text-orange-800',
        'no_show' => 'bg-yellow-100 text-yellow-800',
    ];
    $statusColor = $statusColors[$appointment->status] ?? 'bg-gray-100 text-gray-800';
    
    // Format status display name
    $statusDisplayNames = [
        'scheduled' => 'Scheduled',
        'confirmed' => 'Confirmed',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'failed' => 'Failed',
        'no_show' => 'No Show',
    ];
    $statusDisplayName = $statusDisplayNames[$appointment->status] ?? ucfirst($appointment->status);
    
    // Format the date and time
    $appointmentDate = \Carbon\Carbon::parse($appointment->start_datetime);
    $formattedDate = $appointmentDate->format('D, M j, Y');
    $formattedTime = $appointmentDate->format('g:i A');
    
    // Check if the appointment is upcoming and can be cancelled
    $isUpcoming = in_array($appointment->status, ['scheduled', 'confirmed', 'in_progress']);
    $canCancel = in_array($appointment->status, ['scheduled', 'confirmed']) && $appointmentDate->isFuture();
    $canReschedule = in_array($appointment->status, ['scheduled', 'confirmed']) && $appointmentDate->diffInHours(now()) > 24; // Can reschedule if more than 24h in advance
    
    // Calculate time until appointment
    $timeUntil = $appointmentDate->diffForHumans(now(), [
        'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW,
        'options' => \Carbon\CarbonInterface::ONE_DAY_WORDS + \Carbon\CarbonInterface::TWO_DAY_WORDS,
    ]);
    
    // Determine if this is a past appointment
    $isPast = $appointmentDate->isPast();
    
    // Determine if the appointment is today
    $isToday = $appointmentDate->isToday();
    
    // Service duration
    $endTime = \Carbon\Carbon::parse($appointment->end_datetime);
    $durationInMinutes = $appointmentDate->diffInMinutes($endTime);
    $hours = floor($durationInMinutes / 60);
    $minutes = $durationInMinutes % 60;
    $duration = ($hours > 0 ? $hours . 'h ' : '') . ($minutes > 0 ? $minutes . 'm' : '');
@endphp

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
    <div class="p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
            <!-- Left side: Service and Staff info -->
            <div class="flex-1">
                <div class="flex items-start">
                    <!-- Service Icon -->
                    <div class="flex-shrink-0 h-12 w-12 bg-blue-50 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ $appointment->service->name }}</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            <span class="inline-flex items-center">
                                <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ $appointment->staff->full_name }}
                            </span>
                        </p>
                        
                        <!-- Status and Time -->
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                {{ $statusDisplayName }}
                            </span>
                            
                            @if($isToday)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Today
                                </span>
                            @endif
                            
                            <span class="text-xs text-gray-500">
                                {{ $timeUntil }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Appointment details (visible on mobile) -->
                <div class="mt-4 sm:hidden">
                    <div class="border-t border-gray-200 pt-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Date</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $formattedDate }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Time</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $formattedTime }} <span class="text-gray-500">({{ $duration }})</span></p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Duration</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $duration }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Amount</p>
                                <p class="mt-1 text-sm text-gray-900">₱{{ number_format($appointment->total_amount, 2) }}</p>
                            </div>
                        </div>
                        
                        @if(!empty($appointment->notes))
                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-500">Notes</p>
                                <p class="mt-1 text-sm text-gray-700">{{ $appointment->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Right side: Date and time (hidden on mobile) -->
            <div class="hidden sm:block mt-4 sm:mt-0 sm:ml-6">
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-500">{{ $formattedDate }}</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $formattedTime }}</p>
                    <p class="text-sm text-gray-500">{{ $duration }}</p>
                    <p class="mt-2 text-lg font-medium text-gray-900">₱{{ number_format($appointment->total_amount, 2) }}</p>
                </div>
            </div>
        </div>
        
        <!-- Action buttons -->
        <div class="mt-6 pt-4 border-t border-gray-200 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
            @if($canCancel)
                <form action="{{ route('customer.appointments.cancel', $appointment) }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <button type="button" 
                            onclick="if(confirm('Are you sure you want to cancel this appointment?')) { this.form.submit(); }"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        Cancel
                    </button>
                </form>
            @endif
            
            @if($canReschedule)
                <a href="{{ route('customer.appointments.create') }}?service_id={{ $appointment->service_id }}&staff_id={{ $appointment->staff_id }}" 
                   class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                    </svg>
                    Reschedule
                </a>
            @endif
            
            <a href="{{ route('customer.appointments.show', $appointment) }}" 
               class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                </svg>
                View Details
            </a>
        </div>
    </div>
</div>
