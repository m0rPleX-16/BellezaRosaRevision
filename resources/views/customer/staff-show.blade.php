@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar -->
        @include('customer.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 min-w-0">
            <!-- Back Navigation -->
            <div class="mb-6">
                <a href="{{ route('customer.staff') }}" 
                   class="inline-flex items-center text-gray-600 hover:text-pink-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Staff List
                </a>
            </div>

            <!-- Staff Profile Card -->
            @php
                $staffColorCode = $staff->color_code ?? '#EC4899';
                $staffName = $staff->user->full_name ?? 'Staff Member';
                $staffInitial = strtoupper(substr($staffName, 0, 1));
                $staffColorLight = $staffColorCode . '88';
                $staffColorDark = $staffColorCode . 'cc';
                $staffColorBg = $staffColorCode . '20';
            @endphp
            <div class="staff-profile-card bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden mb-8"
                 data-color="{{ $staffColorCode }}"
                 data-color-light="{{ $staffColorLight }}"
                 data-color-dark="{{ $staffColorDark }}"
                 data-color-bg="{{ $staffColorBg }}">
                <!-- Header with Gradient -->
                <div class="staff-profile-header h-32 relative overflow-hidden">
                    <div class="absolute inset-0 bg-black/10"></div>
                    <div class="absolute -bottom-12 -right-12 w-48 h-48 bg-white/10 rounded-full"></div>
                    <div class="absolute -top-12 -left-12 w-32 h-32 bg-white/10 rounded-full"></div>
                </div>
                
                <div class="px-8 pb-8 -mt-12 relative">
                    <div class="flex flex-col md:flex-row md:items-end md:justify-between">
                        <!-- Avatar & Name -->
                        <div class="flex items-end space-x-5">
                            <div class="staff-profile-avatar w-24 h-24 rounded-2xl flex items-center justify-center text-white text-3xl font-bold shadow-xl border-4 border-white">
                                {{ $staffInitial }}
                            </div>
                            <div class="pb-2">
                                <h1 class="text-2xl font-bold text-gray-900">{{ $staffName }}</h1>
                                @if($staff->user && $staff->user->gender)
                                    <p class="text-gray-500">{{ $staff->user->formatted_gender ?? '' }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Specialty Badge -->
                        <div class="mt-4 md:mt-0">
                            <span class="staff-profile-badge inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold shadow-md">
                                <i class="fas fa-star mr-2"></i>
                                {{ $staff->formatted_specialty ?? 'All Services' }} Specialist
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Services Section -->
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Available Services</h2>
                            <p class="text-gray-500 mt-1">Select a service to book your appointment</p>
                        </div>
                        <div class="hidden sm:flex items-center text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-2"></i>
                            Click to book
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    @if($services && $services->isNotEmpty())
                        @php
                            $categoryIcons = [
                                'Hair Services' => 'fa-cut',
                                'Nail Services' => 'fa-hand-sparkles',
                                'Spa Services' => 'fa-spa',
                                'Full Service' => 'fa-star',
                            ];
                            $categoryColors = [
                                'Hair Services' => ['from-purple-500', 'to-pink-500'],
                                'Nail Services' => ['from-pink-500', 'to-rose-500'],
                                'Spa Services' => ['from-blue-500', 'to-cyan-500'],
                                'Full Service' => ['from-indigo-500', 'to-purple-500'],
                            ];
                            $categoryCount = $services->count();
                            $hasMultipleCategories = $categoryCount > 1;
                            
                            // Count total services across all categories
                            $totalServices = $services->flatten()->count();
                        @endphp

                        @if($hasMultipleCategories)
                            <!-- Category Filter Pills -->
                            <div class="flex flex-wrap gap-2 mb-8">
                                <button onclick="showCategory('all', event)" 
                                        class="category-btn active px-4 py-2 rounded-full text-sm font-medium bg-gray-900 text-white transition-all">
                                    All Services ({{ $totalServices }})
                                </button>
                                @foreach($services as $categoryName => $categoryServices)
                                    @php
                                        $iconClass = $categoryIcons[$categoryName] ?? 'fa-spa';
                                        $categoryId = Str::slug($categoryName);
                                    @endphp
                                    <button onclick="showCategory('{{ $categoryId }}', event)" 
                                            class="category-btn px-4 py-2 rounded-full text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition-all">
                                        <i class="fas {{ $iconClass }} mr-2"></i>
                                        {{ $categoryName }}
                                        <span class="ml-1 text-gray-400">({{ $categoryServices->count() }})</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        <!-- Services Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                            @foreach($services as $categoryName => $categoryServices)
                                @php
                                    $categoryId = Str::slug($categoryName);
                                    $colors = $categoryColors[$categoryName] ?? ['from-pink-500', 'to-rose-500'];
                                @endphp
                                @foreach($categoryServices as $service)
                                    <a href="{{ route('customer.appointments.create', ['service_id' => $service->id, 'staff_id' => $staff->id]) }}" 
                                       class="service-card group block bg-white rounded-2xl border-2 border-gray-100 overflow-hidden hover:border-pink-300 hover:shadow-lg transition-all duration-300"
                                       data-category="{{ $categoryId }}">
                                        
                                        <!-- Service Header -->
                                        <div class="h-2 bg-gradient-to-r {{ $colors[0] }} {{ $colors[1] }}"></div>
                                        
                                        <div class="p-5">
                                            <!-- Service Name & Status -->
                                            <div class="flex items-start justify-between mb-3">
                                                <h4 class="font-bold text-gray-900 group-hover:text-pink-600 transition-colors pr-2">
                                                    {{ $service->name }}
                                                </h4>
                                                <span class="flex-shrink-0 px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                                    <i class="fas fa-check mr-1"></i>Available
                                                </span>
                                            </div>
                                            
                                            <!-- Description -->
                                            @if($service->description)
                                                <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $service->description }}</p>
                                            @else
                                                <p class="text-sm text-gray-400 mb-4 italic">No description available</p>
                                            @endif
                                            
                                            <!-- Duration & Price -->
                                            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                                <div class="flex items-center text-gray-500 text-sm">
                                                    <i class="fas fa-clock mr-2"></i>
                                                    {{ $service->duration_minutes ?? 0 }} min
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-lg font-bold text-gray-900">
                                                        ₱{{ number_format($service->price_regular ?? 0, 2) }}
                                                    </div>
                                                    @if($service->price_premium && $service->price_premium != $service->price_regular)
                                                        <div class="text-xs text-gray-400">
                                                            Premium: ₱{{ number_format($service->price_premium, 2) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Book Button -->
                                            <div class="mt-4 pt-4 border-t border-gray-100">
                                                <span class="flex items-center justify-center w-full px-4 py-2.5 bg-gradient-to-r from-pink-500 to-rose-600 text-white rounded-xl font-medium group-hover:shadow-lg transition-all">
                                                    <i class="fas fa-calendar-plus mr-2"></i>
                                                    Book This Service
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @endforeach
                        </div>
                    @else
                        <!-- No Services -->
                        <div class="text-center py-16">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-spa text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">No Services Available</h3>
                            <p class="text-gray-500 max-w-sm mx-auto">This staff member doesn't have any services assigned yet. Please check back later or choose a different staff member.</p>
                            <a href="{{ route('customer.staff') }}" 
                               class="inline-flex items-center mt-6 px-5 py-2.5 bg-gradient-to-r from-pink-500 to-rose-600 text-white rounded-xl font-medium hover:shadow-lg transition-all">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Browse Other Staff
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Apply dynamic colors to staff profile card
    const profileCard = document.querySelector('.staff-profile-card');
    if (profileCard) {
        const color = profileCard.dataset.color;
        const colorLight = profileCard.dataset.colorLight;
        const colorDark = profileCard.dataset.colorDark;
        const colorBg = profileCard.dataset.colorBg;
        
        const header = profileCard.querySelector('.staff-profile-header');
        const avatar = profileCard.querySelector('.staff-profile-avatar');
        const badge = profileCard.querySelector('.staff-profile-badge');
        
        if (header) {
            header.style.background = 'linear-gradient(135deg, ' + color + ', ' + colorLight + ')';
        }
        if (avatar) {
            avatar.style.background = 'linear-gradient(135deg, ' + color + ', ' + colorDark + ')';
        }
        if (badge) {
            badge.style.backgroundColor = colorBg;
            badge.style.color = color;
        }
    }
    
    // Ensure all service cards are visible initially
    const cards = document.querySelectorAll('.service-card');
    cards.forEach(card => {
        card.style.opacity = '1';
        card.style.transform = 'scale(1)';
    });
});

function showCategory(category, event) {
    const cards = document.querySelectorAll('.service-card');
    const buttons = document.querySelectorAll('.category-btn');
    
    // Update buttons
    buttons.forEach(btn => {
        btn.classList.remove('bg-gray-900', 'text-white', 'active');
        btn.classList.add('bg-gray-100', 'text-gray-700');
    });
    
    if (event && event.currentTarget) {
        event.currentTarget.classList.remove('bg-gray-100', 'text-gray-700');
        event.currentTarget.classList.add('bg-gray-900', 'text-white', 'active');
    }
    
    // Filter cards with animation
    cards.forEach(card => {
        if (category === 'all' || card.dataset.category === category) {
            card.classList.remove('hidden');
            card.style.opacity = '0';
            card.style.transform = 'scale(0.95)';
            setTimeout(() => {
                card.style.transition = 'all 0.2s ease';
                card.style.opacity = '1';
                card.style.transform = 'scale(1)';
            }, 10);
        } else {
            card.style.transition = 'all 0.2s ease';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.95)';
            setTimeout(() => {
                card.classList.add('hidden');
            }, 200);
        }
    });
}
</script>
@endpush
@endsection
