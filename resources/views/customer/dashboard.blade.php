<!-- resources/views/customer/dashboard.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Left sidebar navigation -->
            <div class="w-full md:w-1/4">
                <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                    <h2 class="text-xl font-semibold mb-4">My Account</h2>
                    <nav class="space-y-2">
                        <a href="{{ route('customer.dashboard') }}"
                            class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('customer.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-700' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('customer.appointments.index') }}"
                            class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('customer.appointments.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            My Appointments
                            @php
                                $upcomingCount = auth()
                                    ->user()
                                    ->customer->appointments()
                                    ->whereIn('status', ['scheduled', 'confirmed'])
                                    ->where('start_datetime', '>=', now())
                                    ->count();
                            @endphp
                            @if ($upcomingCount > 0)
                                <span
                                    class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                    {{ $upcomingCount }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('customer.staff') }}"
                            class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('customer.staff') ? 'bg-blue-50 text-blue-600' : 'text-gray-700' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Spa Receptionist
                        </a>
                        <a href="{{ route('customer.profile.edit') }}"
                            class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('profile.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Profile Settings
                        </a>
                    </nav>
                </div>

                <!-- Quick Stats Card -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="font-medium text-gray-900 mb-4">Appointment Stats</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Upcoming</span>
                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                {{ $upcomingCount }}
                            </span>
                        </div>
                        @php
                            $completedCount = auth()
                                ->user()
                                ->customer->appointments()
                                ->where('status', 'completed')
                                ->count();
                        @endphp
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Completed</span>
                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                {{ $completedCount }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="flex-1">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h1>
                    </div>

                    <!-- Upcoming Appointments Preview -->
                    @php
                        $upcomingAppointments = auth()
                            ->user()
                            ->customer->appointments()
                            ->with(['service', 'staff'])
                            ->whereIn('status', ['scheduled', 'confirmed'])
                            ->where('start_datetime', '>=', now())
                            ->orderBy('start_datetime', 'asc')
                            ->take(3)
                            ->get();
                    @endphp

                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">Upcoming Appointments</h2>
                            @if ($upcomingCount > 3)
                                <a href="{{ route('customer.appointments.index') }}"
                                    class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                            @endif
                        </div>

                        @if ($upcomingAppointments->count() > 0)
                            <div class="space-y-4">
                                @foreach ($upcomingAppointments as $appointment)
                                    @include('customer.appointments.partials.appointment-card', [
                                        'appointment' => $appointment,
                                    ])
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 bg-gray-50 rounded-lg">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No upcoming appointments</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by booking a new appointment.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Quick Actions -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <a href="{{ route('customer.appointments.create') }}"
                                class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-sm font-medium text-gray-900">Book New Appointment</h3>
                                        <p class="text-sm text-gray-500">Schedule your next visit</p>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('customer.appointments.index') }}"
                                class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-sm font-medium text-gray-900">View All Appointments</h3>
                                        <p class="text-sm text-gray-500">Manage your bookings</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Custom scrollbar for the sidebar */
        .sidebar-nav {
            scrollbar-width: thin;
            scrollbar-color: #9ca3af #f3f4f6;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 3px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background-color: #9ca3af;
            border-radius: 3px;
        }
    </style>
@endpush
