@extends('layouts.dashboard')

@section('title', 'My Commissions - ' . config('app.name'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Commissions</h1>
            <p class="text-gray-600">View your earned commissions and payment history</p>
        </div>
        
        <!-- Filter Form -->
        <form action="{{ route('staff.commission') }}" method="GET" class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <div class="flex-1">
                <select name="month" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex-1">
                <select name="year" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @for($i = date('Y'); $i >= 2020; $i--)
                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Filter
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-6 rounded-xl shadow">
            <div class="text-gray-500 text-sm font-medium">Total Commissions</div>
            <div class="text-2xl font-bold text-gray-900">₱{{ number_format($totalCommissions, 2) }}</div>
            <div class="text-sm text-gray-500">All Time</div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow">
            <div class="text-gray-500 text-sm font-medium">This Month</div>
            <div class="text-2xl font-bold text-gray-900">₱{{ number_format($monthlyCommissions, 2) }}</div>
            <div class="text-sm text-gray-500">{{ date('F Y') }}</div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow">
            <div class="text-gray-500 text-sm font-medium">Pending Payout</div>
            <div class="text-2xl font-bold text-yellow-600">₱{{ number_format($pendingCommissions, 2) }}</div>
            <div class="text-sm text-gray-500">Not yet paid out</div>
        </div>
    </div>

    <!-- Commissions Table -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Service Amount</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Commission</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($commissions as $commission)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $commission->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $commission->appointment->service->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $commission->appointment->service->duration_minutes ?? 0 }} mins</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $commission->appointment->customer->full_name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $commission->appointment->customer->phone ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                            ₱{{ number_format($commission->service_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                            {{ $commission->commission_rate }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <span class="text-green-600">₱{{ number_format($commission->amount, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($commission->status === 'paid')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Paid
                                </span>
                                @if($commission->payment_date)
                                    <div class="text-xs text-gray-500">{{ $commission->payment_date->format('M d, Y') }}</div>
                                @endif
                            @elseif($commission->status === 'pending')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    {{ ucfirst($commission->status) }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                            No commission records found for the selected period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($commissions->count() > 0)
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="px-6 py-3 text-right text-sm font-medium text-gray-500">
                            Total for {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}:
                        </td>
                        <td class="px-6 py-3 text-right text-sm font-semibold text-gray-900">
                            ₱{{ number_format($commissions->sum('amount'), 2) }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        
        <!-- Pagination -->
        @if($commissions->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $commissions->withQueryString()->links() }}
        </div>
        @endif
    </div>
    
    <!-- Help Text -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h2a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">About Commissions</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>• Commissions are calculated as a percentage of the service amount.</p>
                    <p>• Pending commissions will be paid out by the {{ date('jS', mktime(0, 0, 0, 0, 15)) }} of each month.</p>
                    <p>• For any discrepancies, please contact the salon manager.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
