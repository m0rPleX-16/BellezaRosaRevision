@extends('layouts.dashboard')

@section('title', 'Commission Details - Belleza Rosa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Commission Details</h1>
            <p class="text-gray-600">View detailed information about this commission</p>
        </div>
        <a href="{{ route('dashboard.commissions.index') }}"
            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-xl transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Commissions
        </a>
    </div>

    <!-- Commission Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Commission Details -->
        <div class="card">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Commission Information</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Status</label>
                    <div class="mt-1">
                        <span class="px-3 py-1 text-sm rounded-full
                            {{ $commission->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $commission->status == 'paid' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $commission->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($commission->status) }}
                        </span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Service Amount</label>
                    <p class="mt-1 text-lg font-semibold text-gray-900">₱{{ number_format($commission->service_amount, 2) }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Commission Rate</label>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($commission->commission_rate, 1) }}%</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Commission Amount</label>
                    <p class="mt-1 text-2xl font-bold text-green-600">₱{{ number_format($commission->amount, 2) }}</p>
                </div>
                
                @if($commission->payment_date)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Payment Date</label>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $commission->payment_date->format('M j, Y') }}</p>
                </div>
                @endif
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Created At</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $commission->created_at->format('M j, Y g:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Staff & Appointment Details -->
        <div class="card">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Staff & Appointment Details</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Staff Member</label>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $commission->staff->user->full_name }}</p>
                    <p class="text-sm text-gray-500">{{ $commission->staff->formatted_specialty }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Service</label>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $commission->appointment->service->name ?? 'Service Unavailable' }}</p>
                    <p class="text-sm text-gray-500">{{ $commission->appointment->service->duration_minutes ?? 0 }} minutes</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Customer</label>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $commission->appointment->customer->full_name ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-500">{{ $commission->appointment->customer->phone ?? 'N/A' }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Appointment Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $commission->appointment->start_datetime->format('M j, Y g:i A') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes -->
    @if($commission->notes)
    <div class="card">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Notes</h2>
        <div class="prose max-w-none">
            <p class="text-gray-700 whitespace-pre-line">{{ $commission->notes }}</p>
        </div>
    </div>
    @endif

    <!-- Actions -->
    @if($commission->status == 'pending')
    <div class="card">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Actions</h2>
        <div class="flex space-x-4">
            <form action="{{ route('dashboard.commissions.pay', $commission) }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                    onclick="return confirm('Mark this commission as paid?')"
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-xl transition">
                    <i class="fas fa-check-circle mr-2"></i> Mark as Paid
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
