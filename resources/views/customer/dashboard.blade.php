@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar -->
        @include('customer.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 min-w-0">
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-[var(--primary)] via-[var(--primary-light)] to-[var(--gold)] rounded-3xl p-8 mb-8 text-white relative overflow-hidden">
                <div class="absolute right-0 top-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute left-1/2 bottom-0 w-32 h-32 bg-white/10 rounded-full translate-y-1/2"></div>
                <div class="relative z-10">
                    <h1 class="text-3xl font-bold mb-2">Welcome back, {{ auth()->user()->full_name ?? auth()->user()->name }}! 👋</h1>
                    <p class="text-blue-100 text-lg mb-6">Ready for your next relaxation session?</p>
                    <a href="{{ route('customer.staff') }}" 
                       class="inline-flex items-center px-6 py-3 bg-white text-[var(--primary)] rounded-xl font-semibold hover:bg-blue-50 transition-colors shadow-lg">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        Book an Appointment
                    </a>
                </div>
            </div>

            <!-- Upcoming Appointments Section -->
            @php
                $customer = auth()->user()->customer ?? null;
                
                $upcomingAppointments = collect();
                $upcomingCount = 0;
                
                if ($customer) {
                    $upcomingAppointments = $customer->appointments()
                        ->with(['service', 'staff.user'])
                        ->whereIn('status', ['scheduled', 'confirmed'])
                        ->where('start_datetime', '>=', now())
                        ->orderBy('start_datetime', 'asc')
                        ->take(3)
                        ->get();
                    
                    $upcomingCount = $customer->appointments()
                        ->whereIn('status', ['scheduled', 'confirmed'])
                        ->where('start_datetime', '>=', now())
                        ->count();
                }
            @endphp

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-[var(--primary)] to-[var(--primary-dark)] rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-calendar-alt text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Upcoming Appointments</h2>
                            <p class="text-sm text-gray-500">Your next scheduled visits</p>
                        </div>
                    </div>
                    @if ($upcomingCount > 3)
                        <a href="{{ route('customer.appointments.index') }}" 
                           class="text-sm text-[var(--primary)] hover:text-[var(--primary-dark)] font-medium flex items-center">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    @endif
                </div>

                <div class="p-6">
                    @if ($upcomingAppointments->count() > 0)
                        <div class="space-y-4">
                            @foreach ($upcomingAppointments as $appointment)
                                @include('customer.appointments.partials.appointment-card', [
                                    'appointment' => $appointment,
                                ])
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-calendar-times text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">No Upcoming Appointments</h3>
                            <p class="text-gray-500 mb-6">You don't have any appointments scheduled yet.</p>
                            <a href="{{ route('customer.staff') }}" 
                               class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-[var(--primary)] to-[var(--primary-dark)] text-white rounded-xl font-medium hover:shadow-lg transition-all">
                                <i class="fas fa-plus mr-2"></i>
                                Book Your First Appointment
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                <!-- Book Appointment Card -->
                <a href="{{ route('customer.staff') }}" 
                   class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-[var(--primary)] hover:shadow-md transition-all">
                    <div class="w-14 h-14 bg-gradient-to-br from-[var(--primary)] to-[var(--primary-dark)] rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-plus text-white text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">Book New Appointment</h3>
                    <p class="text-sm text-gray-500">Schedule your next spa visit</p>
                </a>

                <!-- View All Appointments Card -->
                <a href="{{ route('customer.appointments.index') }}" 
                   class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-[var(--gold)] hover:shadow-md transition-all">
                    <div class="w-14 h-14 bg-gradient-to-br from-[var(--gold)] to-[var(--gold-light)] rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-list text-white text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">View All Appointments</h3>
                    <p class="text-sm text-gray-500">Manage your bookings</p>
                </a>

                <!-- Profile Settings Card -->
                <a href="{{ route('customer.profile.edit') }}" 
                   class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-[var(--primary-light)] hover:shadow-md transition-all">
                    <div class="w-14 h-14 bg-gradient-to-br from-[var(--primary-light)] to-[var(--primary)] rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-cog text-white text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">Profile Settings</h3>
                    <p class="text-sm text-gray-500">Update your information</p>
                </a>
            </div>

            <!-- Recent Activity / Tips Section -->
            <div class="bg-gradient-to-br from-[var(--gold-light)] to-[var(--gold)] rounded-2xl p-6 border border-[var(--gold-dark)]">
                <div class="flex items-start">
                    <div class="w-12 h-12 bg-gradient-to-br from-[var(--gold)] to-[var(--gold-dark)] rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-lightbulb text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-1">Spa Tip of the Day</h3>
                        <p class="text-gray-600 text-sm">
                            Stay hydrated before and after your spa treatments for the best results. Drinking water helps your body flush out toxins and enhances the benefits of your massage therapy.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
