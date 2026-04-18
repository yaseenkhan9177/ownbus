@extends('layouts.company')

@section('title', 'Reports')

@section('header_title')
<h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Reports</h1>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Date Filter -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-4">
        <form action="{{ route('company.reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                <input type="date" name="start_date" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-800 dark:border-slate-700 text-sm" value="{{ $startDate->format('Y-m-d') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                <input type="date" name="end_date" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-800 dark:border-slate-700 text-sm" value="{{ $endDate->format('Y-m-d') }}">
            </div>
            @if(auth()->user()->hasRole('admin') || auth()->user()->isSuperAdmin())
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Branch</label>
                <select name="branch_id" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-800 dark:border-slate-700 text-sm">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div>
                <button type="submit" class="w-full flex justify-center items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Financial Summary -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Financial Summary</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-slate-800 rounded-lg">
                    <span class="text-gray-600 dark:text-gray-400">Total Income</span>
                    <span class="text-lg font-bold text-emerald-600">${{ number_format($pnl['total_income'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-slate-800 rounded-lg">
                    <span class="text-gray-600 dark:text-gray-400">Total Expenses</span>
                    <span class="text-lg font-bold text-rose-600">${{ number_format($pnl['total_expenses'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-blue-50 dark:bg-slate-800/50 rounded-lg border border-blue-100 dark:border-slate-700">
                    <span class="text-gray-800 dark:text-gray-200 font-medium">Net Profit</span>
                    <span class="text-xl font-bold {{ $pnl['net_profit'] >= 0 ? 'text-blue-600' : 'text-rose-600' }}">
                        ${{ number_format($pnl['net_profit'], 2) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Vehicle Performance Preview -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Top Performing Vehicles</h3>
                <form action="{{ route('company.reports.export') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="vehicle_performance">
                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    <button type="submit" class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export CSV
                    </button>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-slate-800 text-gray-500 dark:text-gray-400 font-medium">
                        <tr>
                            <th class="px-4 py-2">Vehicle</th>
                            <th class="px-4 py-2 text-center">Rentals</th>
                            <th class="px-4 py-2 text-right">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                        @forelse($vehiclePerformance->take(5) as $vehicle)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $vehicle['vehicle_number'] }}</div>
                                <div class="text-xs text-gray-500">{{ $vehicle['make_model'] }}</div>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $vehicle['rental_count'] }}</td>
                            <td class="px-4 py-3 text-right font-medium text-emerald-600">${{ number_format($vehicle['total_revenue'], 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-gray-500">No data available.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection