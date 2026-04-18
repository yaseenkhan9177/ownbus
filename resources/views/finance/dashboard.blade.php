@extends('layouts.company')

@section('title', 'Financial Dashboard')

@section('header_title')
<div class="flex items-center justify-between w-full">
    <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Financial Overview</h1>
    <div class="flex gap-3">
        <button type="button" x-data @click="document.getElementById('dateFilter').classList.toggle('hidden')" class="flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Date Filter
        </button>
        <a href="{{ route('finance.transactions') }}" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition shadow-sm">
            View All Transactions
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Date Filter -->
    <div id="dateFilter" class="hidden bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-4 transition-all duration-300">
        <form action="{{ route('finance.dashboard') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                <input type="date" name="start_date" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-800 dark:border-slate-700 text-sm" value="{{ $startDate->format('Y-m-d') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                <input type="date" name="end_date" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-800 dark:border-slate-700 text-sm" value="{{ $endDate->format('Y-m-d') }}">
            </div>
            <div>
                <button type="submit" class="w-full flex justify-center items-center px-4 py-2 bg-gray-900 dark:bg-slate-700 text-white rounded-lg text-sm font-medium hover:bg-gray-800 dark:hover:bg-slate-600 transition">
                    Apply Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Income -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-16 h-16 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Income</h3>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($summary['income'], 2) }}</span>
            </div>
            <div class="mt-1 text-sm text-emerald-600 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                {{ $summary['transaction_count_income'] }} transactions
            </div>
        </div>

        <!-- Expenses -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-16 h-16 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Expenses</h3>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($summary['expense'], 2) }}</span>
            </div>
            <div class="mt-1 text-sm text-rose-600 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                </svg>
                {{ $summary['transaction_count_expense'] }} transactions
            </div>
        </div>

        <!-- Net Profit -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-16 h-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Net Profit</h3>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold {{ $summary['net_profit'] >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-rose-600' }}">
                    ${{ number_format($summary['net_profit'], 2) }}
                </span>
            </div>
            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                {{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }}
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center bg-gray-50/50 dark:bg-slate-800/50 rounded-t-xl">
            <h3 class="font-semibold text-gray-800 dark:text-gray-200">Recent Transactions</h3>
            <a href="{{ route('finance.transactions') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-slate-800 text-gray-500 dark:text-gray-400 font-medium">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Description</th>
                        <th class="px-6 py-3">Reference</th>
                        <th class="px-6 py-3 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($recentTransactions as $transaction)
                    @php
                    $isIncome = $transaction->journalEntries->contains(fn($je) => $je->account && $je->account->account_type === 'revenue' && $je->credit > 0);
                    $amount = $transaction->journalEntries->sum(fn($je) => $isIncome ? $je->credit : $je->debit) / 2;
                    $displayAmount = $transaction->journalEntries->max(fn($je) => max($je->debit, $je->credit));
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition">
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $transaction->transaction_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-gray-900 dark:text-gray-100 font-medium">{{ $transaction->description }}</td>
                        <td class="px-6 py-4">
                            @if($transaction->reference_type === 'App\Models\Rental')
                            <a href="{{ route('rentals.show', $transaction->reference_id) }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 hover:bg-blue-200">
                                Rental #{{ $transaction->reference_id }}
                            </a>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-slate-700 dark:text-gray-300">
                                {{ class_basename($transaction->reference_type) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-bold {{ $isIncome ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $isIncome ? '+' : '-' }}${{ number_format($displayAmount, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            No recent transactions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection