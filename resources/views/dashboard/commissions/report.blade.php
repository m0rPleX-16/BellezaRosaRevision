@extends('layouts.dashboard')

@section('title', 'Commission Report - Belleza Rosa')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Commission Report</h1>
                <p class="text-gray-600 mt-1">
                    @if($request->report_type == 'monthly')
                        Monthly Report for {{ DateTime::createFromFormat('!m', $request->month)->format('F') }} {{ $request->year }}
                    @elseif($request->report_type == 'staff')
                        Staff Report for {{ $request->year }}
                    @else
                        Detailed Report for {{ $request->year }}
                    @endif
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('dashboard.commissions.report') }}" 
                    class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-xl transition">
                    <i class="fas fa-redo mr-2"></i> Generate New Report
                </a>
                <a href="{{ route('dashboard.commissions.index') }}" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Commissions
                </a>
            </div>
        </div>

        <!-- Report Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="card border-l-4 border-blue-500">
                <div class="flex">
                    <div class="p-3 bg-blue-100 rounded-xl h-fit">
                        <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-sm font-medium text-gray-500">Total Commission</h3>
                        <p class="text-2xl font-bold text-gray-900">₱{{ number_format($reportData['total_commissions'], 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="card border-l-4 border-green-500">
                <div class="flex">
                    <div class="p-3 bg-green-100 rounded-xl h-fit">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-sm font-medium text-gray-500">Paid Commission</h3>
                        <p class="text-2xl font-bold text-gray-900">₱{{ number_format($reportData['paid_commissions'], 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="card border-l-4 border-yellow-500">
                <div class="flex">
                    <div class="p-3 bg-yellow-100 rounded-xl h-fit">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-sm font-medium text-gray-500">Pending Commission</h3>
                        <p class="text-2xl font-bold text-gray-900">₱{{ number_format($reportData['pending_commissions'], 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="card border-l-4 border-purple-500">
                <div class="flex">
                    <div class="p-3 bg-purple-100 rounded-xl h-fit">
                        <i class="fas fa-concierge-bell text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-sm font-medium text-gray-500">Total Services</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ $reportData['total_services'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff Breakdown -->
        @if($reportData['commissions_by_staff']->count() > 0)
            <div class="card">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Commission by Staff</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Staff Name</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Total Services</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Total Commission</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Paid Amount</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Pending Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($reportData['commissions_by_staff'] as $staffId => $staffData)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $staffData['staff_name'] }}</td>
                                    <td class="px-4 py-3 text-gray-900">{{ $staffData['total_services'] }}</td>
                                    <td class="px-4 py-3 font-semibold text-gray-900">₱{{ number_format($staffData['total_amount'], 2) }}</td>
                                    <td class="px-4 py-3 text-green-600 font-semibold">₱{{ number_format($staffData['paid_amount'], 2) }}</td>
                                    <td class="px-4 py-3 text-yellow-600 font-semibold">₱{{ number_format($staffData['pending_amount'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Report Actions -->
        <div class="card">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Report generated on {{ now()->format('F j, Y \a\t g:i A') }}
                </div>
                <div class="flex space-x-3">
                    <button onclick="window.print()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-xl transition">
                        <i class="fas fa-print mr-2"></i> Print Report
                    </button>
                    <form action="{{ route('dashboard.commissions.report.generate') }}" method="POST" class="inline">
                        @csrf
                        @foreach($request->only(['report_type', 'month', 'year', 'staff_id']) as $key => $value)
                            @if($value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <input type="hidden" name="download" value="1">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-xl transition">
                            <i class="fas fa-download mr-2"></i> Download PDF
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
