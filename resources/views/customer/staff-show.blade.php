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
            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('customer.staff') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Staff List
                </a>
            </div>

            <!-- Staff Profile Card -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
                <div class="h-3" style="background-color: {{ $staff->color_code ?? '#3B82F6' }}"></div>
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center space-x-4 mb-4 md:mb-0">
                            <div class="h-20 w-20 rounded-full flex items-center justify-center text-white text-3xl font-bold"
                                 style="background: linear-gradient(135deg, {{ $staff->color_code ?? '#3B82F6' }}, {{ $staff->color_code ?? '#3B82F6' }}88);">
                                {{ strtoupper(substr($staff->user->full_name, 0, 1)) }}
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $staff->user->full_name }}</h1>
                                @if($staff->user->gender)
                                    <p class="text-gray-600 mt-1">{{ $staff->user->formatted_gender }}</p>
                                @endif
                            </div>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium"
                                  style="background-color: {{ $staff->color_code ?? '#3B82F6' }}20; color: {{ $staff->color_code ?? '#3B82F6' }};">
                                <i class="fas fa-star mr-2"></i>
                                {{ $staff->formatted_specialty }} Specialist
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services Section -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Available Services</h2>
                    <p class="text-gray-600 mt-1">Services offered by {{ $staff->user->full_name }}</p>
                </div>

                @if($services->count() > 0)
                    @php
                        $categoryColors = [
                            'Hair Services' => ['bg-gradient-to-r from-purple-500 to-pink-500', 'text-purple-700'],
                            'Nail Services' => ['bg-gradient-to-r from-pink-500 to-rose-500', 'text-pink-700'],
                            'Spa Services' => ['bg-gradient-to-r from-blue-500 to-cyan-500', 'text-blue-700'],
                            'Full Service' => ['bg-gradient-to-r from-indigo-500 to-purple-500', 'text-indigo-700'],
                        ];
                        $categoryIcons = [
                            'Hair Services' => 'fa-cut',
                            'Nail Services' => 'fa-hand-sparkles',
                            'Spa Services' => 'fa-spa',
                            'Full Service' => 'fa-star',
                        ];
                        $hasMultipleCategories = $services->count() > 1;
                    @endphp

                    @if($hasMultipleCategories)
                        <!-- Category Tabs Navigation -->
                        <div class="bg-gray-50 rounded-xl shadow-sm p-2 border border-gray-200 mb-6">
                            <div class="flex flex-wrap gap-2">
                                @foreach($services as $categoryName => $categoryServices)
                                    @php
                                        $colorClass = $categoryColors[$categoryName] ?? ['bg-gradient-to-r from-gray-500 to-gray-600', 'text-gray-700'];
                                        $iconClass = $categoryIcons[$categoryName] ?? 'fa-spa';
                                        $categoryId = 'category-' . strtolower(str_replace(' ', '-', $categoryName));
                                        $isFirst = $loop->first;
                                    @endphp
                                    <button onclick="showCategory('{{ $categoryId }}', event)" id="tab-{{ strtolower(str_replace(' ', '-', $categoryName)) }}"
                                        class="category-tab {{ $isFirst ? 'active' : '' }} px-6 py-3 rounded-lg font-semibold transition-all duration-300 {{ $colorClass[0] }} text-white shadow-md hover:shadow-lg transform hover:scale-105">
                                        <i class="fas {{ $iconClass }} mr-2"></i> {{ $categoryName }}
                                        <span class="ml-2 bg-white bg-opacity-30 px-2 py-1 rounded-full text-xs">{{ $categoryServices->count() }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Services by Category -->
                    @foreach($services as $categoryName => $categoryServices)
                        @php
                            $categoryId = 'category-' . strtolower(str_replace(' ', '-', $categoryName));
                            $isFirst = $loop->first;
                            $displayClass = $hasMultipleCategories ? ($isFirst ? '' : 'hidden') : '';
                        @endphp
                        <div id="{{ $categoryId }}" class="category-content {{ $displayClass }} mb-8">
                            @if(!$hasMultipleCategories)
                                <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-spa mr-2" style="color: {{ $staff->color_code ?? '#3B82F6' }};"></i>
                                    {{ $categoryName }}
                                    <span class="ml-2 text-sm font-normal text-gray-500">({{ $categoryServices->count() }} {{ $categoryServices->count() == 1 ? 'service' : 'services' }})</span>
                                </h3>
                            @endif
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($categoryServices as $service)
                                    <a href="{{ route('customer.appointments.create', ['service_id' => $service->id, 'staff_id' => $staff->id]) }}" 
                                       class="block border border-gray-200 rounded-lg p-4 hover:shadow-md hover:border-blue-400 transition-all cursor-pointer">
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="font-semibold text-gray-900">{{ $service->name }}</h4>
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                                        </div>
                                        
                                        @if($service->description)
                                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $service->description }}</p>
                                        @endif
                                        
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center text-gray-600">
                                                <i class="fas fa-clock mr-1"></i>
                                                <span>{{ $service->duration_minutes }} min</span>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-semibold text-gray-900">
                                                    ₱{{ number_format($service->price_regular, 2) }}
                                                </div>
                                                @if($service->price_premium)
                                                    <div class="text-xs text-gray-500">
                                                        Premium: ₱{{ number_format($service->price_premium, 2) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mt-3 pt-3 border-t border-gray-200">
                                            <p class="text-xs text-blue-600 font-medium text-center">
                                                <i class="fas fa-calendar-check mr-1"></i> Book This Service
                                            </p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-spa text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-500 mb-2">No Services Available</h3>
                        <p class="text-gray-400">This staff member doesn't have any services assigned yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($services->count() > 1)
<script>
    function showCategory(categoryId, event) {
        // Hide all category contents
        document.querySelectorAll('.category-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Show selected category content
        document.getElementById(categoryId).classList.remove('hidden');

        // Update active tab
        document.querySelectorAll('.category-tab').forEach(tab => {
            tab.classList.remove('active');
            tab.classList.add('opacity-75');
        });

        // Mark clicked tab as active
        if (event && event.currentTarget) {
            event.currentTarget.classList.add('active');
            event.currentTarget.classList.remove('opacity-75');
        }
    }

    // Activate first tab on page load
    document.addEventListener('DOMContentLoaded', function() {
        const firstTab = document.querySelector('.category-tab');
        if (firstTab) {
            firstTab.classList.add('active');
            firstTab.classList.remove('opacity-75');
        }
    });
</script>
@endif
@endsection
