@php
    $user = auth()->user();
    $customer = $user->customer ?? null;
    
    // Calculate counts with null safety
    $upcomingCount = 0;
    $completedCount = 0;
    
    if ($customer) {
        $upcomingCount = $customer->appointments()
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->where('start_datetime', '>=', now())
            ->count();
            
        $completedCount = $customer->appointments()
            ->where('status', 'completed')
            ->count();
    }
    
    $userName = $user->first_name ?? $user->name ?? 'Guest';
    $userInitial = strtoupper(substr($user->name ?? 'G', 0, 1));
@endphp

<div class="w-full md:w-72 flex-shrink-0">
    <!-- User Welcome Card -->
    <div class="bg-gradient-to-br from-pink-500 to-rose-600 p-6 rounded-2xl shadow-lg mb-6 text-white">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-2xl font-bold">
                {{ $userInitial }}
            </div>
            <div>
                <h3 class="font-bold text-lg">{{ $userName }}</h3>
            </div>
        </div>
    </div>

    <!-- Navigation Card -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <nav class="space-y-1">
            <a href="{{ route('customer.dashboard') }}"
                class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group
                {{ request()->routeIs('customer.dashboard') 
                    ? 'bg-gradient-to-r from-pink-50 to-rose-50 text-pink-600 shadow-sm' 
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 transition-colors
                    {{ request()->routeIs('customer.dashboard') 
                        ? 'bg-pink-100 text-pink-600' 
                        : 'bg-gray-100 text-gray-500 group-hover:bg-pink-50 group-hover:text-pink-500' }}">
                    <i class="fas fa-home"></i>
                </div>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="{{ route('customer.appointments.index') }}"
                class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group
                {{ request()->routeIs('customer.appointments.*') 
                    ? 'bg-gradient-to-r from-pink-50 to-rose-50 text-pink-600 shadow-sm' 
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 transition-colors
                    {{ request()->routeIs('customer.appointments.*') 
                        ? 'bg-pink-100 text-pink-600' 
                        : 'bg-gray-100 text-gray-500 group-hover:bg-pink-50 group-hover:text-pink-500' }}">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <span class="font-medium flex-1">My Appointments</span>
                @if ($upcomingCount > 0)
                    <span class="px-2 py-0.5 text-xs font-bold rounded-full 
                        {{ request()->routeIs('customer.appointments.*') 
                            ? 'bg-pink-200 text-pink-700' 
                            : 'bg-blue-100 text-blue-700' }}">
                        {{ $upcomingCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('customer.staff') }}"
                class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group
                {{ request()->routeIs('customer.staff*') 
                    ? 'bg-gradient-to-r from-pink-50 to-rose-50 text-pink-600 shadow-sm' 
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 transition-colors
                    {{ request()->routeIs('customer.staff*') 
                        ? 'bg-pink-100 text-pink-600' 
                        : 'bg-gray-100 text-gray-500 group-hover:bg-pink-50 group-hover:text-pink-500' }}">
                    <i class="fas fa-spa"></i>
                </div>
                <span class="font-medium">Book a Service</span>
            </a>

            <a href="{{ route('customer.profile.edit') }}"
                class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group
                {{ request()->routeIs('customer.profile.*') 
                    ? 'bg-gradient-to-r from-pink-50 to-rose-50 text-pink-600 shadow-sm' 
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 transition-colors
                    {{ request()->routeIs('customer.profile.*') 
                        ? 'bg-pink-100 text-pink-600' 
                        : 'bg-gray-100 text-gray-500 group-hover:bg-pink-50 group-hover:text-pink-500' }}">
                    <i class="fas fa-user-cog"></i>
                </div>
                <span class="font-medium">Profile Settings</span>
            </a>
        </nav>
    </div>

    <!-- Quick Stats Card -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mt-6">
        <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-chart-pie text-pink-500 mr-2"></i>
            Quick Stats
        </h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-clock text-blue-600 text-sm"></i>
                    </div>
                    <span class="text-sm text-gray-600">Upcoming</span>
                </div>
                <span class="px-3 py-1 text-sm font-bold bg-blue-50 text-blue-700 rounded-full">
                    {{ $upcomingCount }}
                </span>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-check text-green-600 text-sm"></i>
                    </div>
                    <span class="text-sm text-gray-600">Completed</span>
                </div>
                <span class="px-3 py-1 text-sm font-bold bg-green-50 text-green-700 rounded-full">
                    {{ $completedCount }}
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Book CTA -->
    <div class="mt-6">
        <a href="{{ route('customer.staff') }}" 
           class="flex items-center justify-center w-full px-6 py-4 bg-gradient-to-r from-pink-500 to-rose-600 text-white rounded-2xl font-semibold shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5 group">
            <i class="fas fa-plus-circle mr-2 group-hover:rotate-90 transition-transform duration-300"></i>
            Book New Appointment
        </a>
    </div>
</div>
