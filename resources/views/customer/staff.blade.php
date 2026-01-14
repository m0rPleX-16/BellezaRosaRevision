@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Sidebar -->
        @include('customer.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 min-w-0">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Book a Service</h1>
                <p class="text-gray-500 text-sm">Select a staff member to view their available services and book your appointment.</p>
            </div>

            @if($staffMembers && $staffMembers->count() > 0)
                <!-- Compact Staff Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($staffMembers as $staff)
                        @php
                            $staffName = $staff['name'] ?? 'Staff Member';
                            $staffColor = $staff['color_code'] ?? '#EC4899';
                            $staffInitial = strtoupper(substr($staffName, 0, 1));
                            $staffSpecialty = $staff['specialty'] ?? 'All Services';
                            $staffColorLight = $staffColor . '88';
                            $staffColorDark = $staffColor . 'cc';
                            $staffColorBg = $staffColor . '15';
                        @endphp
                        <a href="{{ route('customer.staff.show', $staff['id']) }}" 
                           class="staff-card group block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:border-[var(--primary)] transition-all duration-300 hover:-translate-y-1"
                           data-color="{{ $staffColor }}"
                           data-color-light="{{ $staffColorLight }}"
                           data-color-dark="{{ $staffColorDark }}"
                           data-color-bg="{{ $staffColorBg }}">
                            
                            <!-- Compact Staff Card -->
                            <div class="p-4">
                                <!-- Compact Header -->
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-[var(--primary)] to-[var(--primary-dark)] rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900">{{ $staffName }}</h3>
                                            @if(!empty($staff['gender']))
                                                <p class="text-sm text-gray-500">{{ $staff['gender'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <!-- Compact Badge -->
                                        <div class="staff-card-badge inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-[var(--primary)]">
                                            <i class="fas fa-star mr-1"></i>
                                            {{ $staffSpecialty }} Specialist
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Compact Actions -->
                                <div class="pt-2 border-t border-gray-100">
                                    <span class="inline-flex items-center text-sm font-semibold text-[var(--primary)] group-hover:text-[var(--primary-dark)]">
                                        View Available Services
                                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <!-- Compact Empty State -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
                    <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-tie text-3xl text-blue-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">No Staff Available</h3>
                    <p class="text-gray-500 max-w-sm mx-auto">There are currently no staff members available. Please check back later.</p>
                </div>
            @endif

            <!-- Compact Info Card -->
            <div class="mt-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-100">
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-gradient-to-br from-[var(--primary)] to-[var(--primary-dark)] rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-info text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-1">How to Book</h3>
                        <p class="text-gray-600 text-sm">
                            Click on a staff member to view their available services. Select your preferred service, pick a date and time that works for you, and confirm your booking. It's that simple!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.staff-card').forEach(function(card) {
        const color = card.dataset.color;
        const colorLight = card.dataset.colorLight;
        const colorDark = card.dataset.colorDark;
        const colorBg = card.dataset.colorBg;
        
        const header = card.querySelector('.staff-card-header');
        const avatar = card.querySelector('.staff-card-avatar');
        const badge = card.querySelector('.staff-card-badge');
        
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
    });
});
</script>
@endpush
@endsection
