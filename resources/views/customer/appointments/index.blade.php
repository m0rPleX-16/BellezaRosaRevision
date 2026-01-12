@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">My Appointments</h1>
        <a href="{{ route('customer.appointments.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
            Book New Appointment
        </a>
    </div>

    <!-- Tabs for filtering appointments -->
    <div class="mb-6 border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px" id="appointmentTabs" role="tablist">
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" 
                        id="upcoming-tab" 
                        data-tabs-target="#upcoming" 
                        type="button" 
                        role="tab" 
                        aria-controls="upcoming" 
                        aria-selected="true">
                    Upcoming
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" 
                        id="past-tab" 
                        data-tabs-target="#past" 
                        type="button" 
                        role="tab" 
                        aria-controls="past" 
                        aria-selected="false">
                    Past
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" 
                        id="cancelled-tab" 
                        data-tabs-target="#cancelled" 
                        type="button" 
                        role="tab" 
                        aria-controls="cancelled" 
                        aria-selected="false">
                    Cancelled
                </button>
            </li>
        </ul>
    </div>

    <!-- Tab content -->
    <div id="appointmentTabsContent">
        <!-- Upcoming Appointments -->
        <div class="p-4 rounded-lg bg-white shadow-sm" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
            @if($upcomingAppointments->count() > 0)
                <div class="space-y-4">
                    @foreach($upcomingAppointments as $appointment)
                        @include('customer.appointments.partials.appointment-card', ['appointment' => $appointment])
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No upcoming appointments</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by booking a new appointment.</p>
                    <div class="mt-6">
                        <a href="{{ route('customer.appointments.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            New Appointment
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Past Appointments -->
        <div class="hidden p-4 rounded-lg bg-white shadow-sm" id="past" role="tabpanel" aria-labelledby="past-tab">
            @if($pastAppointments->count() > 0)
                <div class="space-y-4">
                    @foreach($pastAppointments as $appointment)
                        @include('customer.appointments.partials.appointment-card', ['appointment' => $appointment])
                    @endforeach
                </div>
                
                {{ $pastAppointments->links() }}
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No past appointments</h3>
                    <p class="mt-1 text-sm text-gray-500">Your past appointments will appear here.</p>
                </div>
            @endif
        </div>

        <!-- Cancelled Appointments -->
        <div class="hidden p-4 rounded-lg bg-white shadow-sm" id="cancelled" role="tabpanel" aria-labelledby="cancelled-tab">
            @if($cancelledAppointments->count() > 0)
                <div class="space-y-4">
                    @foreach($cancelledAppointments as $appointment)
                        @include('customer.appointments.partials.appointment-card', ['appointment' => $appointment])
                    @endforeach
                </div>
                
                {{ $cancelledAppointments->links() }}
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No cancelled appointments</h3>
                    <p class="mt-1 text-sm text-gray-500">Your cancelled appointments will appear here.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize tabs
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('[data-tabs-target]');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const target = document.querySelector(this.getAttribute('data-tabs-target'));
                const tabContents = document.querySelectorAll('[role="tabpanel"]');
                
                // Hide all tab contents
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Remove active styles from all tabs
                tabs.forEach(t => {
                    t.classList.remove('border-blue-600', 'text-blue-600');
                    t.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300');
                });
                
                // Show the selected tab content
                target.classList.remove('hidden');
                
                // Style the active tab
                this.classList.remove('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300');
                this.classList.add('border-blue-600', 'text-blue-600');
                
                // Update URL hash
                const tabId = this.getAttribute('id').replace('-tab', '');
                window.location.hash = tabId;
            });
        });
        
        // Check URL hash on page load
        const hash = window.location.hash.replace('#', '');
        if (hash) {
            const tab = document.getElementById(`${hash}-tab`);
            if (tab) tab.click();
        } else if (tabs.length > 0) {
            // Activate the first tab by default
            tabs[0].click();
        }
    });
</script>
@endpush

@push('styles')
<style>
    [role="tab"] {
        transition: all 0.2s ease-in-out;
    }
    
    [role="tab"].active {
        color: #2563eb;
        border-color: #2563eb;
    }
    
    [role="tab"]:not(.active) {
        color: #6b7280;
    }
</style>
@endpush
@endsection
