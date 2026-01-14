@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar -->
        @include('customer.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 min-w-0">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">My Appointments</h1>
                    <p class="text-gray-500 mt-1">Manage and track your spa appointments</p>
                </div>
                <a href="{{ route('customer.staff') }}" 
                   class="mt-4 sm:mt-0 inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-[var(--primary)] to-[var(--primary-dark)] text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5">
                    <i class="fas fa-plus mr-2"></i>
                    Book New Appointment
                </a>
            </div>

            <!-- Tabs Navigation -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="border-b border-gray-100">
                    <nav class="flex overflow-x-auto scrollbar-hide" id="appointmentTabs" role="tablist">
                        <button class="tab-button flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-colors active"
                                data-tabs-target="#upcoming" 
                                type="button" 
                                role="tab">
                            <i class="fas fa-clock mr-2"></i>
                            Upcoming
                            @if($upcomingAppointments->count() > 0)
                                <span class="ml-2 px-2 py-0.5 text-xs bg-blue-100 text-[var(--primary)] rounded-full font-bold">
                                    {{ $upcomingAppointments->count() }}
                                </span>
                            @endif
                        </button>
                        
                        <button class="tab-button flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-colors"
                                data-tabs-target="#past" 
                                type="button" 
                                role="tab">
                            <i class="fas fa-history mr-2"></i>
                            Past
                            @if($pastAppointments->count() > 0)
                                <span class="ml-2 px-2 py-0.5 text-xs bg-gray-100 text-gray-700 rounded-full font-bold">
                                    {{ $pastAppointments->total() }}
                                </span>
                            @endif
                        </button>
                        
                        <button class="tab-button flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-colors"
                                data-tabs-target="#cancelled" 
                                type="button" 
                                role="tab">
                            <i class="fas fa-times-circle mr-2"></i>
                            Cancelled
                            @if($cancelledAppointments->count() > 0)
                                <span class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded-full font-bold">
                                    {{ $cancelledAppointments->total() }}
                                </span>
                            @endif
                        </button>
                        
                        <button class="tab-button flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-colors"
                                data-tabs-target="#failed" 
                                type="button" 
                                role="tab">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Failed
                            @if($failedAppointments->count() > 0)
                                <span class="ml-2 px-2 py-0.5 text-xs bg-orange-100 text-orange-700 rounded-full font-bold">
                                    {{ $failedAppointments->total() }}
                                </span>
                            @endif
                        </button>
                        
                        <button class="tab-button flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-colors"
                                data-tabs-target="#no-show" 
                                type="button" 
                                role="tab">
                            <i class="fas fa-user-slash mr-2"></i>
                            No Show
                            @if($noShowAppointments->count() > 0)
                                <span class="ml-2 px-2 py-0.5 text-xs bg-yellow-100 text-yellow-700 rounded-full font-bold">
                                    {{ $noShowAppointments->total() }}
                                </span>
                            @endif
                        </button>
                    </nav>
                </div>

                <!-- Tab Contents -->
                <div class="p-6" id="appointmentTabsContent">
                    <!-- Upcoming Appointments -->
                    <div class="tab-content" id="upcoming" role="tabpanel">
                        @if($upcomingAppointments->count() > 0)
                            <div class="space-y-4">
                                @foreach($upcomingAppointments as $appointment)
                                    @include('customer.appointments.partials.appointment-card', ['appointment' => $appointment])
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-16">
                                <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-calendar-times text-3xl text-blue-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">No Upcoming Appointments</h3>
                                <p class="text-gray-500 mb-6 max-w-sm mx-auto">You don't have any appointments scheduled. Book a service to get started!</p>
                                <a href="{{ route('customer.staff') }}" 
                                   class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-[var(--primary)] to-[var(--primary-dark)] text-white rounded-xl font-medium">
                                    <i class="fas fa-plus mr-2"></i>
                                    Book Now
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Past Appointments -->
                    <div class="tab-content hidden" id="past" role="tabpanel">
                        @if($pastAppointments->count() > 0)
                            <div class="space-y-4">
                                @foreach($pastAppointments as $appointment)
                                    @include('customer.appointments.partials.appointment-card', ['appointment' => $appointment])
                                @endforeach
                            </div>
                            <div class="mt-6">
                                {{ $pastAppointments->appends(['tab' => 'past'])->links() }}
                            </div>
                        @else
                            <div class="text-center py-16">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-history text-3xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">No Past Appointments</h3>
                                <p class="text-gray-500">Your completed appointments will appear here.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Cancelled Appointments -->
                    <div class="tab-content hidden" id="cancelled" role="tabpanel">
                        @if($cancelledAppointments->count() > 0)
                            <div class="space-y-4">
                                @foreach($cancelledAppointments as $appointment)
                                    @include('customer.appointments.partials.appointment-card', ['appointment' => $appointment])
                                @endforeach
                            </div>
                            <div class="mt-6">
                                {{ $cancelledAppointments->appends(['tab' => 'cancelled'])->links() }}
                            </div>
                        @else
                            <div class="text-center py-16">
                                <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-times-circle text-3xl text-red-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">No Cancelled Appointments</h3>
                                <p class="text-gray-500">Your cancelled appointments will appear here.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Failed Appointments -->
                    <div class="tab-content hidden" id="failed" role="tabpanel">
                        @if($failedAppointments->count() > 0)
                            <div class="space-y-4">
                                @foreach($failedAppointments as $appointment)
                                    @include('customer.appointments.partials.appointment-card', ['appointment' => $appointment])
                                @endforeach
                            </div>
                            <div class="mt-6">
                                {{ $failedAppointments->appends(['tab' => 'failed'])->links() }}
                            </div>
                        @else
                            <div class="text-center py-16">
                                <div class="w-20 h-20 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-exclamation-triangle text-3xl text-orange-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">No Failed Appointments</h3>
                                <p class="text-gray-500">Your failed appointments will appear here.</p>
                            </div>
                        @endif
                    </div>

                    <!-- No Show Appointments -->
                    <div class="tab-content hidden" id="no-show" role="tabpanel">
                        @if($noShowAppointments->count() > 0)
                            <div class="space-y-4">
                                @foreach($noShowAppointments as $appointment)
                                    @include('customer.appointments.partials.appointment-card', ['appointment' => $appointment])
                                @endforeach
                            </div>
                            <div class="mt-6">
                                {{ $noShowAppointments->appends(['tab' => 'no-show'])->links() }}
                            </div>
                        @else
                            <div class="text-center py-16">
                                <div class="w-20 h-20 bg-yellow-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-user-slash text-3xl text-yellow-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">No No-Show Appointments</h3>
                                <p class="text-gray-500">Your no-show appointments will appear here.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .tab-button {
        border-color: transparent;
        color: #6B7280;
    }
    
    .tab-button:hover {
        color: #374151;
        background-color: #F9FAFB;
    }
    
    .tab-button.active {
        border-color: #EC4899;
        color: #EC4899;
    }
    
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const target = document.querySelector(this.getAttribute('data-tabs-target'));
                
                // Hide all tab contents
                tabContents.forEach(content => content.classList.add('hidden'));
                
                // Remove active state from all tabs
                tabs.forEach(t => t.classList.remove('active'));
                
                // Show selected tab content
                target.classList.remove('hidden');
                
                // Activate selected tab
                this.classList.add('active');
                
                // Update URL hash
                const tabId = this.getAttribute('data-tabs-target').replace('#', '');
                history.replaceState(null, null, `#${tabId}`);
            });
        });
        
        // Check URL hash on page load
        const hash = window.location.hash.replace('#', '');
        if (hash) {
            const tab = document.querySelector(`[data-tabs-target="#${hash}"]`);
            if (tab) tab.click();
        }
    });
</script>
@endpush
@endsection
