@extends('layouts.dashboard')

@section('title', 'Appointment Details - Belleza Rosa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Appointment Details</h1>
        <a href="{{ route('dashboard.appointments.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
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
                'failed' => 'bg-red-100 text-red-800 border-red-200',
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
                            @if(!empty($appointment->customer?->email))
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
                                <label class="block text-sm font-medium text-gray-500">Staff</label>
                                <p class="mt-1 text-base text-gray-900">{{ $appointment->staff->user->full_name ?? 'Unassigned' }}</p>
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
                        <div class="bg-white rounded-lg p-5 border border-purple-300">
                            <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">
                                <i class="fas fa-calculator mr-2"></i>Complete Pricing Breakdown
                            </h4>
                            
                            <div class="space-y-4">
                                <!-- Base Service -->
                                <div class="flex justify-between items-center py-3 border-b border-gray-200">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                            <i class="fas fa-cut text-blue-600 text-lg"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 text-base">Base Service</div>
                                            <div class="text-sm text-gray-600">{{ $appointment->service->name }}</div>
                                            <div class="text-xs text-gray-500">Duration: {{ $appointment->service->duration_minutes }} minutes</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xl font-bold text-blue-900">{{ $appointment->formatted_base_price }}</div>
                                    </div>
                                </div>
                                
                                <!-- Addons Summary -->
                                @if($appointment->addons->count() > 0)
                                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                                                <i class="fas fa-layer-group text-purple-600 text-lg"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900 text-base">Additional Services</div>
                                                <div class="text-sm text-gray-600">{{ $appointment->addons->count() }} service{{ $appointment->addons->count() > 1 ? 's' : '' }}</div>
                                                <div class="text-xs text-gray-500">
                                                    @foreach($appointment->addons as $addon)
                                                        {{ $addon->display_name }}{{ !$loop->last ? ', ' : '' }}
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xl font-bold text-purple-700">{{ $appointment->formatted_addons_total }}</div>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Total Calculation -->
                                <div class="flex justify-between items-center py-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg px-5 border-2 border-purple-300">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center mr-4">
                                            <i class="fas fa-receipt text-white text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 text-xl">Total Amount</div>
                                            <div class="text-sm text-gray-600">Base Service + Addons</div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                ₱{{ number_format($appointment->base_price, 2) }} + ₱{{ number_format($appointment->addons_total, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                                            ₱{{ number_format($appointment->total_amount, 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Cancellation Details -->
            @if(!empty($appointment->cancellation_reason))
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Cancellation Details</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Reason</label>
                            <p class="text-base text-gray-900">{{ $appointment->cancellation_reason }}</p>
                        </div>
                        @if($appointment->cancelled_at)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Cancelled On</label>
                                    <p class="text-base text-gray-900">{{ $appointment->cancelled_at->format('M j, Y \a\t g:i A') }}</p>
                                </div>
                                @if($appointment->cancelled_by && $appointment->cancelledByUser)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-1">Cancelled By</label>
                                        <p class="text-base text-gray-900">{{ $appointment->cancelledByUser->full_name }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif
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

                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('dashboard.payments.show', $appointment->payment) }}"
                           class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium">
                            <i class="fas fa-credit-card mr-2"></i>View Payment
                        </a>
                        @if(method_exists($appointment->payment, 'isPaid') && !$appointment->payment->isPaid())
                            <a href="{{ route('dashboard.payments.edit', $appointment->payment) }}"
                               class="inline-flex items-center px-4 py-2 rounded-md bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium">
                                <i class="fas fa-edit mr-2"></i>Edit Payment
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex flex-wrap gap-3">
                    @if($appointment->status === 'completed')
                        @if($appointment->payment)
                            <a href="{{ route('dashboard.payments.show', $appointment->payment) }}"
                               class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium">
                                <i class="fas fa-receipt mr-2"></i>Payment Details
                            </a>
                        @else
                            <a href="{{ route('dashboard.payments.create', ['appointment' => $appointment->id]) }}"
                               class="inline-flex items-center px-4 py-2 rounded-md bg-green-600 hover:bg-green-700 text-white text-sm font-medium">
                                <i class="fas fa-money-bill-wave mr-2"></i>Record Payment
                            </a>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-6">
                <a href="{{ route('dashboard.appointments.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Appointments
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

