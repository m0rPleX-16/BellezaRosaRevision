@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar -->
        @include('customer.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 min-w-0">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Book a Service</h1>
                <p class="text-gray-500">Select a staff member to view their available services and book your appointment.</p>
            </div>

            @if($staffMembers && $staffMembers->count() > 0)
                <!-- Staff Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
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
                           class="staff-card group block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:border-pink-200 transition-all duration-300 hover:-translate-y-1"
                           data-color="{{ $staffColor }}"
                           data-color-light="{{ $staffColorLight }}"
                           data-color-dark="{{ $staffColorDark }}"
                           data-color-bg="{{ $staffColorBg }}">
                            
                            <!-- Staff Card Header with Gradient -->
                            <div class="staff-card-header h-24 relative overflow-hidden">
                                <div class="absolute inset-0 bg-black/10"></div>
                                <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-white/20 rounded-full"></div>
                                <div class="absolute -top-6 -left-6 w-16 h-16 bg-white/10 rounded-full"></div>
                            </div>
                            
                            <div class="p-6 -mt-10 relative">
                                <!-- Avatar -->
                                <div class="staff-card-avatar w-20 h-20 rounded-2xl flex items-center justify-center text-white text-2xl font-bold shadow-lg mb-4 border-4 border-white">
                                    {{ $staffInitial }}
                                </div>
                                
                                <!-- Staff Info -->
                                <h3 class="text-lg font-bold text-gray-900 mb-1 group-hover:text-pink-600 transition-colors">
                                    {{ $staffName }}
                                </h3>
                                
                                @if(!empty($staff['gender']))
                                    <p class="text-sm text-gray-500 mb-3">{{ $staff['gender'] }}</p>
                                @endif
                                
                                <!-- Specialty Badge -->
                                <div class="staff-card-badge inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium mb-4">
                                    <i class="fas fa-star mr-2"></i>
                                    {{ $staffSpecialty }} Specialist
                                </div>
                                
                                <!-- View Services Link -->
                                <div class="pt-4 border-t border-gray-100">
                                    <span class="inline-flex items-center text-sm font-semibold text-pink-600 group-hover:text-pink-700">
                                        View Available Services
                                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-tie text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">No Staff Available</h3>
                    <p class="text-gray-500 max-w-sm mx-auto">There are currently no staff members available. Please check back later.</p>
                </div>
            @endif

            <!-- Info Card -->
            <div class="mt-8 bg-gradient-to-br from-pink-50 to-rose-50 rounded-2xl p-6 border border-pink-100">
                <div class="flex items-start">
                    <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
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
