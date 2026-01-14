@extends('layouts.staff')

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

            <!-- Addons Section -->
            @if($appointment->addons && $appointment->addons->count() > 0)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl p-6 border border-purple-200">
                    <h3 class="text-lg font-semibold text-purple-700 uppercase tracking-wide mb-4">
                        <i class="fas fa-plus-circle mr-2"></i>Additional Services
                    </h3>
                    
                    <!-- Duration Summary First -->
                    <div class="bg-blue-50 rounded-lg p-4 mb-4 border border-blue-200">
                        <div class="text-sm font-medium text-blue-800 mb-3">
                            <i class="fas fa-clock mr-2"></i>Duration Breakdown
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700 font-medium">{{ $appointment->service->name }}:</span>
                                <span class="font-bold text-blue-700">{{ $appointment->service->duration_minutes }} min</span>
                            </div>
                            @if($appointment->addons->where('service_id', '!=', null)->count() > 0)
                                @foreach($appointment->addons->where('service_id', '!=', null) as $addon)
                                    @if($addon->service && $addon->service->duration_minutes > 0)
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 text-xs pl-4">
                                                <i class="fas fa-plus text-blue-500 mr-1"></i>{{ $addon->service->name }}:
                                            </span>
                                            <span class="font-medium text-blue-600">+{{ $addon->service->duration_minutes }} min</span>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                            <div class="flex justify-between items-center pt-3 border-t border-blue-200 mt-2">
                                <span class="font-bold text-blue-800">Total Duration:</span>
                                <span class="font-bold text-lg text-blue-800">
                                    {{ \Carbon\Carbon::parse($appointment->start_datetime)->diffInMinutes(\Carbon\Carbon::parse($appointment->end_datetime)) }} min
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Addons List -->
                    <div class="space-y-3 mb-6">
                        @foreach($appointment->addons as $index => $addon)
                            <div class="flex justify-between items-center p-4 bg-white rounded-lg border border-purple-200 shadow-sm">
                                <div class="flex items-center flex-1">
                                    @if($addon->isServiceAddon())
                                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mr-4">
                                                <i class="fas fa-spa text-white text-lg"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900 text-lg">{{ $addon->display_name }}</div>
                                                <div class="flex items-center mt-1 gap-2">
                                                    <span class="text-xs text-purple-600 bg-purple-100 px-2 py-1 rounded-full font-medium">Salon Service</span>
                                                    @if($addon->service && $addon->service->duration_minutes > 0)
                                                        <span class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded-full">
                                                            <i class="fas fa-clock mr-1"></i>{{ $addon->service->duration_minutes }} min
                                                        </span>
                                                    @endif
                                                    <span class="text-xs text-gray-500">Service ID: #{{ $addon->service_id }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 bg-gradient-to-br from-gray-500 to-gray-600 rounded-lg flex items-center justify-center mr-4">
                                                <i class="fas fa-plus-circle text-white text-lg"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900 text-lg">{{ $addon->display_name }}</div>
                                                <div class="flex items-center mt-1 gap-2">
                                                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">Custom Addon</span>
                                                    <span class="text-xs text-gray-500">No additional time</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-purple-600 text-lg">{{ $addon->formatted_price }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Detailed Pricing Breakdown -->
                    <div class="bg-white rounded-lg p-4 border border-purple-300">
                        <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">
                            <i class="fas fa-calculator mr-2"></i>Detailed Pricing Breakdown
                        </h4>
                        
                        <div class="space-y-3">
                            <!-- Base Service -->
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-cut text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">Base Service</div>
                                        <div class="text-sm text-gray-500">{{ $appointment->service->name }}</div>
                                    </div>
                                </div>
                                <div class="text-xl font-bold text-blue-900">{{ $appointment->formatted_base_price }}</div>
                            </div>
                            
                            <!-- Addons -->
                            @if($appointment->addons->count() > 0)
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-plus text-purple-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">Additional Services</div>
                                            <div class="text-sm text-gray-500">{{ $appointment->addons->count() }} service{{ $appointment->addons->count() > 1 ? 's' : '' }}</div>
                                        </div>
                                    </div>
                                    <div class="text-xl font-bold text-purple-700">{{ $appointment->formatted_addons_total }}</div>
                                </div>
                            @endif
                            
                            <!-- Total -->
                            <div class="flex justify-between items-center py-3 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg px-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-calculator text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 text-lg">Total Amount</div>
                                        <div class="text-xs text-gray-600">Base + Addons</div>
                                    </div>
                                </div>
                                <div class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                                    ₱{{ number_format($appointment->total_amount, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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