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
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Profile Settings
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex-1">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Spa Receptionist</h1>
                    <p class="text-gray-600 mt-2">Meet our professional staff members</p>
                </div>

                @if($staffMembers->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($staffMembers as $staff)
                            <a href="{{ route('customer.staff.show', $staff['id']) }}" 
                               class="block bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-200 hover:border-blue-300 cursor-pointer">
                                <!-- Staff Card Header with Color -->
                                <div class="h-2" style="background-color: {{ $staff['color_code'] ?? '#3B82F6' }}"></div>
                                
                                <div class="p-6">
                                    <!-- Staff Avatar -->
                                    <div class="flex items-center mb-4">
                                        <div class="h-16 w-16 rounded-full flex items-center justify-center text-white text-2xl font-bold mr-4"
                                             style="background: linear-gradient(135deg, {{ $staff['color_code'] ?? '#3B82F6' }}, {{ $staff['color_code'] ?? '#3B82F6' }}88);">
                                            {{ strtoupper(substr($staff['name'], 0, 1)) }}
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="text-lg font-bold text-gray-900">{{ $staff['name'] }}</h3>
                                            @if($staff['gender'])
                                                <p class="text-sm text-gray-600">{{ $staff['gender'] }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Specialty Badge -->
                                    <div class="mb-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                              style="background-color: {{ $staff['color_code'] ?? '#3B82F6' }}20; color: {{ $staff['color_code'] ?? '#3B82F6' }};">
                                            <i class="fas fa-star mr-1"></i>
                                            {{ $staff['specialty'] }} Specialist
                                        </span>
                                    </div>

                                    <!-- Staff Info -->
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-briefcase mr-2 text-gray-400"></i>
                                            <span>Specialty: {{ $staff['specialty'] }}</span>
                                        </div>
                                    </div>

                                    <!-- Click hint -->
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <p class="text-xs text-blue-600 font-medium text-center">
                                            <i class="fas fa-arrow-right mr-1"></i> View Services
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-user-tie text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-500 mb-2">No Staff Available</h3>
                        <p class="text-gray-400">There are currently no staff members available.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
