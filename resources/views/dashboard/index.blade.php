@extends('layouts.dashboard')

@if (auth()->check() && auth()->user()->isStaff())
    @php
        header('Location: ' . route('staff.dashboard'));
        exit;
    @endphp
@endif

@php
function getStatusPillClass($status) {
    $classes = [
        'scheduled' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'confirmed' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'in_progress' => 'bg-amber-50 text-amber-800 ring-amber-600/20',
        'completed' => 'bg-slate-100 text-slate-700 ring-slate-600/20',
        'cancelled' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
        'no_show' => 'bg-orange-50 text-orange-700 ring-orange-600/20',
    ];
    return $classes[$status] ?? 'bg-gray-100 text-gray-700 ring-gray-600/20';
}
@endphp

@section('title', 'Dashboard - Belleza Rosa')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header with Date Filter -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Dashboard</span>
                    <span class="text-sm font-normal text-gray-500 bg-blue-50 px-3 py-1 rounded-full">
                        {{ now()->format('l, F j, Y') }}
                    </span>
                </h1>
                <p class="text-gray-600 mt-2 flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                    Welcome back, {{ auth()->user()->full_name }}! Here's what's happening with your salon today.
                </p>
            </div>
            
        <!-- Date Filter -->
        <div class="flex flex-wrap items-center justify-start lg:justify-end gap-3">
            <div class="relative">
                <select id="dateFilter" class="h-11 min-w-[180px] px-4 pr-10 border border-gray-200 rounded-xl bg-white shadow-sm text-sm text-gray-800 appearance-none cursor-pointer hover:border-gray-300 transition-all duration-200 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="today">Today</option>
                    <option value="this_week">This Week</option>
                    <option value="this_month">This Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="this_year">This Year</option>
                    <option value="custom">Custom Month</option>
                    <option value="custom_range">Custom Range</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                </div>
            </div>
            
            <!-- Custom Date Range (hidden by default) -->
            <div id="customRange" class="hidden flex flex-wrap gap-3 items-center transition-all duration-200 opacity-0 scale-95">
                <div class="relative group">
                    <input type="month" id="customDate" class="h-11 min-w-[180px] px-4 pr-10 border border-gray-200 rounded-xl bg-white shadow-sm text-sm text-gray-800 hover:border-gray-300 transition-all duration-200 appearance-none cursor-pointer group-hover:border-blue-400 group-hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Select month">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-calendar text-gray-400 text-sm group-hover:text-blue-500 transition-colors"></i>
                    </div>
                </div>
                <button onclick="applyCustomDate()" class="h-11 px-5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 font-medium shadow-sm hover:shadow-md inline-flex items-center justify-center gap-2 whitespace-nowrap">
                    <i class="fas fa-check text-sm"></i>
                    Apply
                </button>
            </div>
            
            <!-- Custom Range Picker (hidden by default) -->
            <div id="customRangePicker" class="hidden flex flex-wrap gap-3 items-center transition-all duration-200 opacity-0 scale-95">
                <div class="relative group">
                    <input type="date" id="dateFrom" class="h-11 min-w-[170px] px-4 pr-10 border border-gray-200 rounded-xl bg-white shadow-sm text-sm text-gray-800 hover:border-gray-300 transition-all duration-200 appearance-none cursor-pointer group-hover:border-blue-400 group-hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="From date">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-calendar-day text-gray-400 text-sm group-hover:text-blue-500 transition-colors"></i>
                    </div>
                </div>
                <div class="relative group">
                    <input type="date" id="dateTo" class="h-11 min-w-[170px] px-4 pr-10 border border-gray-200 rounded-xl bg-white shadow-sm text-sm text-gray-800 hover:border-gray-300 transition-all duration-200 appearance-none cursor-pointer group-hover:border-blue-400 group-hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="To date">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-calendar-day text-gray-400 text-sm group-hover:text-blue-500 transition-colors"></i>
                    </div>
                </div>
                <button onclick="applyCustomDateRange()" class="h-11 px-5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 font-medium shadow-sm hover:shadow-md inline-flex items-center justify-center gap-2 whitespace-nowrap">
                    <i class="fas fa-check text-sm"></i>
                    Apply Range
                </button>
            </div>
                
                <button id="mainApplyButton" onclick="applyDateFilter()" class="h-11 px-6 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-200 font-medium shadow-sm hover:shadow-md inline-flex items-center justify-center whitespace-nowrap">
                    <i class="fas fa-filter mr-2"></i> Apply Filter
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Appointments Card -->
            <div onclick="viewAppointments()" class="group bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p id="appointmentsLabel" class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Today's Appointments</p>
                        <p id="appointmentsCount" class="text-3xl font-bold text-gray-900 mt-2 group-hover:text-blue-600 transition-colors">{{ $stats['appointments_count'] ?? 0 }}</p>
                        <div class="mt-2 text-xs text-gray-500">
                            <span class="inline-flex items-center gap-1">
                                <i class="fas fa-arrow-up text-green-500"></i>
                                Active today
                            </span>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-4 rounded-2xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-calendar-check text-white text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Customers Card -->
            <div onclick="viewCustomerServices()" class="group bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p id="customersLabel" class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Today's Total Customers</p>
                        <p id="totalCustomers" class="text-3xl font-bold text-gray-900 mt-2 group-hover:text-green-600 transition-colors">{{ $stats['customers_count'] ?? 0 }}</p>
                        <div class="mt-2 text-xs text-gray-500">
                            <span class="inline-flex items-center gap-1">
                                <i class="fas fa-users text-green-500"></i>
                                Unique visitors
                            </span>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-emerald-600 p-4 rounded-2xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Revenue Card -->
            <div onclick="viewRevenue()" class="group bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p id="revenueLabel" class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Today's Revenue</p>
                        <p id="revenueAmount" class="text-3xl font-bold text-gray-900 mt-2 group-hover:text-purple-600 transition-colors">₱{{ number_format($stats['revenue'] ?? 0, 2) }}</p>
                        <div class="mt-2 text-xs text-gray-500">
                            <span class="inline-flex items-center gap-1">
                                <i class="fas fa-chart-line text-purple-500"></i>
                                Completed services
                            </span>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 to-pink-600 p-4 rounded-2xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-peso-sign text-white text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Staff Card -->
            <div onclick="viewStaff()" class="group bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Staff</p>
                        <p id="totalStaff" class="text-3xl font-bold text-gray-900 mt-2 group-hover:text-orange-600 transition-colors">{{ $stats['total_staff'] ?? 0 }}</p>
                        <div class="mt-2 text-xs text-gray-500">
                            <span class="inline-flex items-center gap-1">
                                <i class="fas fa-user-tie text-orange-500"></i>
                                Active members
                            </span>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-orange-500 to-red-600 p-4 rounded-2xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-user-tie text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Appointments -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <div class="bg-blue-100 p-2 rounded-lg">
                        <i class="fas fa-calendar-alt text-blue-600"></i>
                    </div>
                    Recent Appointments
                </h2>
                <button onclick="window.location.href='{{ route('dashboard.appointments.index') }}'" class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center gap-1">
                    View All <i class="fas fa-arrow-right"></i>
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 rounded-tl-lg">Customer</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700">Service</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700">Date & Time</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 rounded-tr-lg">Status</th>
                        </tr>
                    </thead>
                    <tbody id="appointmentsTableBody">
                        @forelse($appointments ?? [] as $appointment)
                        <tr class="border-b border-gray-100 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200">
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="bg-gray-100 p-2 rounded-full">
                                        <i class="fas fa-user text-gray-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $appointment->customer?->full_name ?? 'Unknown Customer' }}</div>
                                        <div class="text-xs text-gray-500">ID: {{ $appointment->customer_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="bg-purple-100 p-1.5 rounded">
                                        <i class="fas fa-spa text-purple-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $appointment->service?->name ?? 'Unknown Service' }}</div>
                                        <div class="text-xs text-gray-500">{{ $appointment->service?->duration ?? 'N/A' }} mins</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="text-gray-700">
                                    <div class="font-medium">{{ \Carbon\Carbon::parse($appointment->start_datetime)->format('M j, Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($appointment->start_datetime)->format('g:i A') }}</div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset {{ getStatusPillClass($appointment->status) }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>
                                    {{ str_replace('_', ' ', ucfirst($appointment->status)) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="bg-gray-100 p-4 rounded-full">
                                        <i class="fas fa-calendar-times text-3xl text-gray-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-700">No appointments found</p>
                                        <p class="text-sm text-gray-500">for the selected period</p>
                                    </div>
                                    <button onclick="window.location.href='{{ route('dashboard.appointments.create') }}'" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                        <i class="fas fa-plus mr-2"></i>Create First Appointment
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include modals -->
@include('dashboard.partials.customer-services-modal')

<script>
// Global variables
const filterUrl = "{{ route('dashboard.filter') }}";
const csrfToken = "{{ csrf_token() }}";
let currentFilterData = null;

// Toast notification function
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toast-container');
    const toast = document.createElement('div');
    
    const bgColor = type === 'success' ? 'bg-green-500' : 
                   type === 'error' ? 'bg-red-500' : 
                   type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
    
    const icon = type === 'success' ? 'fa-check-circle' : 
                 type === 'error' ? 'fa-exclamation-circle' : 
                 type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
    
    toast.className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 transform transition-all duration-300 translate-x-full`;
    toast.innerHTML = `
        <i class="fas ${icon} text-lg"></i>
        <span class="font-medium">${message}</span>
    `;
    
    toastContainer.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
        toast.classList.add('translate-x-0');
    }, 100);
    
    // Remove after 5 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            toastContainer.removeChild(toast);
        }, 300);
    }, 5000);
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    setupDateFilter();
    
    // Store initial data
    currentFilterData = {
        stats: @json($stats ?? []),
        appointments: @json($appointments ?? []),
        customer_services: @json($customersWithServices ?? []),
        label: "{{ $stats_label ?? "Today's" }}"
    };
});

// Setup date filter behavior
function setupDateFilter() {
    const dateFilter = document.getElementById('dateFilter');
    const customRange = document.getElementById('customRange');
    const customRangePicker = document.getElementById('customRangePicker');
    const mainApplyButton = document.getElementById('mainApplyButton');

    dateFilter.addEventListener('change', function() {
        const value = this.value;
        
        // Hide all custom inputs with fade out effect
        hideElementWithAnimation(customRange);
        hideElementWithAnimation(customRangePicker);
        
        // Show/hide main apply button based on selection
        if (value === 'custom' || value === 'custom_range') {
            hideElementWithAnimation(mainApplyButton);
        } else {
            showElementWithAnimation(mainApplyButton);
        }
        
        // Show appropriate input with fade in effect
        setTimeout(() => {
            if (value === 'custom') {
                showElementWithAnimation(customRange);
            } else if (value === 'custom_range') {
                showElementWithAnimation(customRangePicker);
            }
        }, 150);
    });

    // Set max date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('dateTo').max = today;
    document.getElementById('dateFrom').max = today;
}

// Helper functions for animations
function hideElementWithAnimation(element) {
    if (element) {
        element.classList.add('opacity-0', 'scale-95');
        element.classList.remove('opacity-100', 'scale-100');
        setTimeout(() => {
            element.classList.add('hidden');
        }, 150);
    }
}

function showElementWithAnimation(element) {
    if (element) {
        element.classList.remove('hidden');
        setTimeout(() => {
            element.classList.remove('opacity-0', 'scale-95');
            element.classList.add('opacity-100', 'scale-100');
        }, 10);
    }
}

// Apply date filter
function applyDateFilter() {
    const filter = document.getElementById('dateFilter').value;
    
    if (filter === 'custom' || filter === 'custom_range') {
        showToast('Please select custom dates first', 'error');
        return;
    }

    fetch(filterUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            date_range: filter
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            updateDashboard(data);
            showToast(`Filter applied: ${filter.replace('_', ' ')}`);
        } else {
            showToast(data.message || 'Error applying filter', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error applying filter', 'error');
    });
}

// Apply custom date range
function applyCustomDateRange() {
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    
    if (!dateFrom || !dateTo) {
        showToast('Please select both dates', 'error');
        return;
    }

    fetch(filterUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            date_range: 'custom_range',
            date_from: dateFrom,
            date_to: dateTo
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            updateDashboard(data);
            showToast(`Custom range applied: ${dateFrom} to ${dateTo}`);
        } else {
            showToast(data.message || 'Error applying filter', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error applying filter', 'error');
    });
}

// Apply custom month
function applyCustomDate() {
    const customDate = document.getElementById('customDate').value;
    
    if (!customDate) {
        showToast('Please select a month', 'error');
        return;
    }

    fetch(filterUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            date_range: 'custom',
            custom_date: customDate
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            updateDashboard(data);
            showToast(`Custom filter applied: ${customDate}`);
        } else {
            showToast(data.message || 'Error applying filter', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error applying filter', 'error');
    });
}

// Update dashboard with new data
function updateDashboard(data) {
    console.log('Dashboard data received:', data);
    
    // Store current data
    currentFilterData = data;
    
    // Update statistics cards
    document.getElementById('appointmentsLabel').textContent = data.label + ' Appointments';
    document.getElementById('appointmentsCount').textContent = data.stats.appointments_count;

    document.getElementById('customersLabel').textContent = data.label + ' Total Customers';
    document.getElementById('totalCustomers').textContent = data.stats.customers_count;

    document.getElementById('revenueLabel').textContent = data.label + ' Revenue';
    document.getElementById('revenueAmount').textContent = '₱' + Number(data.stats.revenue).toLocaleString('en-PH', {
        minimumFractionDigits: 2
    });

    document.getElementById('totalStaff').textContent = data.stats.total_staff;

    // Update appointments table
    const tableBody = document.getElementById('appointmentsTableBody');
    if (data.appointments && data.appointments.length > 0) {
        let tableHtml = '';
        data.appointments.forEach(appointment => {
            const appointmentDate = new Date(appointment.start_datetime);
            tableHtml += `
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="py-3 px-4">
                        <div class="font-medium text-gray-900">${appointment.customer.full_name}</div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="text-gray-700">${appointment.service.name}</div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="text-gray-700">${appointmentDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })} ${appointmentDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })}</div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset ${getStatusPillClass(appointment.status)}">
                            <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>
                            ${appointment.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
                        </span>
                    </td>
                </tr>
            `;
        });
        tableBody.innerHTML = tableHtml;
    } else {
        tableBody.innerHTML = `
            <tr>
                <td colspan="4" class="py-8 text-center text-gray-500">
                    <i class="fas fa-calendar-times text-4xl mb-3 text-gray-300"></i>
                    <p>No appointments found for the selected period.</p>
                </td>
            </tr>
        `;
    }
}

// Get status pill class
function getStatusPillClass(status) {
    const classes = {
        'scheduled': 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'confirmed': 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'in_progress': 'bg-amber-50 text-amber-800 ring-amber-600/20',
        'completed': 'bg-slate-100 text-slate-700 ring-slate-600/20',
        'cancelled': 'bg-rose-50 text-rose-700 ring-rose-600/20',
        'no_show': 'bg-orange-50 text-orange-700 ring-orange-600/20',
    };
    return classes[status] || 'bg-gray-100 text-gray-700 ring-gray-600/20';
}

// Navigation functions
function viewAppointments() {
    const url = "{{ route('dashboard.appointments.index') }}";
    if (currentFilterData && currentFilterData.date_range) {
        window.location.href = url + '?date_range=' + encodeURIComponent(currentFilterData.date_range);
    } else {
        window.location.href = url;
    }
}

function viewCustomerServices() {
    const modal = document.getElementById('customerServicesModal');
    modal.classList.remove('hidden');
    
    if (currentFilterData && currentFilterData.customer_services) {
        updateCustomerServicesModal(currentFilterData.customer_services);
    }
}

function viewRevenue() {
    const url = "{{ route('dashboard.reports.financial') }}";
    if (currentFilterData && currentFilterData.date_range) {
        window.location.href = url + '?date_range=' + encodeURIComponent(currentFilterData.date_range);
    } else {
        window.location.href = url;
    }
}

function viewStaff() {
    window.location.href = "{{ route('dashboard.users.index') }}";
}

// Update customer services modal
function updateCustomerServicesModal(data) {
    if (!data) return;
    
    // Update customer table
    const tableBody = document.querySelector('#customerServicesModal tbody');
    if (data.customers && data.customers.length > 0) {
        let tableHtml = '';
        data.customers.forEach(customer => {
            tableHtml += `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">${customer.full_name}</div>
                        <div class="text-sm text-gray-500">${customer.phone}</div>
                    </td>
                    <td class="px-4 py-3">
            `;

            if (customer.appointments && customer.appointments.length > 0) {
                tableHtml += '<div class="space-y-1">';
                customer.appointments.slice(0, 3).forEach(appointment => {
                    const appointmentDate = new Date(appointment.start_datetime);
                    tableHtml += `
                        <div class="text-sm text-gray-700">
                            • ${appointment.service.name}
                            <span class="text-xs text-gray-500">(${appointmentDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })})</span>
                        </div>
                    `;
                });
                
                if (customer.appointments.length > 3) {
                    tableHtml += `
                        <div class="text-xs text-blue-600">
                            +${customer.appointments.length - 3} more services
                        </div>`;
                }
                tableHtml += '</div>';
            } else {
                tableHtml += '<span class="text-gray-400 text-sm">No services availed</span>';
            }
            
            tableHtml += `
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        ${customer.total_visits || 0}
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold text-green-600">
                        ₱${customer.total_spent ? Number(customer.total_spent).toLocaleString('en-PH', {minimumFractionDigits: 2}) : '0.00'}
                    </td>
                </tr>
            `;
        });

        tableBody.innerHTML = tableHtml;
    } else {
        tableBody.innerHTML = `
            <tr>
                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                    <i class="fas fa-users text-4xl mb-3 text-gray-300"></i>
                    <p>No customers with services in the selected period.</p>
                </td>
            </tr>
        `;
    }
}

// Close modal function
function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Setup modal closing
document.addEventListener('click', function(event) {
    if (event.target.id === 'customerServicesModal') {
        closeModal('customerServicesModal');
    }
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal('customerServicesModal');
    }
});
</script>
@endsection
