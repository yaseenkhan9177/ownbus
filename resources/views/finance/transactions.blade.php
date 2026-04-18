@extends('layouts.company')

@section('title', 'Financial Transactions')

@section('header_title')
<div class="flex items-center justify-between w-full">
    <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Financial Transactions</h1>
    <a href="{{ route('finance.dashboard') }}" class="px-4 py-2 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition">
        &larr; Back to Dashboard
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-4">
        <form action="{{ route('finance.transactions') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" name="search" class="pl-10 w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-800 dark:border-slate-700 text-sm" value="{{ request('search') }}" placeholder="Description...">
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                <select name="reference_type" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-800 dark:border-slate-700 text-sm">
                    <option value="">All Types</option>
                    <option value="App\Models\Rental" {{ request('reference_type') == 'App\Models\Rental' ? 'selected' : '' }}>Rental</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Date</label>
                <input type="date" name="date_from" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-800 dark:border-slate-700 text-sm" value="{{ request('date_from') }}">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To Date</label>
                <input type="date" name="date_to" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-800 dark:border-slate-700 text-sm" value="{{ request('date_to') }}">
            </div>
            <div class="md:col-span-3">
                <button type="submit" class="w-full flex justify-center items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition shadow-sm">
                    Filter Transactions
                </button>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-slate-800 text-gray-500 dark:text-gray-400 font-medium">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Description</th>
                        <th class="px-6 py-3">Reference</th>
                        <th class="px-6 py-3">Accounts Hit</th>
                        <th class="px-6 py-3 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition">
                        <td class="px-6 py-4">
                            <div class="text-gray-900 dark:text-gray-100 font-medium">{{ $transaction->transaction_date->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $transaction->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $transaction->description }}</td>
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
                        <td class="px-6 py-4">
                            <ul class="space-y-1">
                                @foreach($transaction->journalEntries as $entry)
                                <li class="text-xs">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ $entry->account->account_name ?? 'Unknown' }}</span>:
                                    @if($entry->debit > 0) <span class="text-rose-600 dark:text-rose-400">Dr {{ number_format($entry->debit, 2) }}</span>
                                    @else <span class="text-emerald-600 dark:text-emerald-400">Cr {{ number_format($entry->credit, 2) }}</span>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-6 py-4 text-right font-bold">
                            @php
                            $isIncome = $transaction->journalEntries->contains(fn($je) => $je->account && $je->account->account_type === 'revenue' && $je->credit > 0);
                            $displayAmount = $transaction->journalEntries->max(fn($je) => max($je->debit, $je->credit));
                            @endphp
                            <span class="{{ $isIncome ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $isIncome ? '+' : '-' }}${{ number_format($displayAmount, 2) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            No transactions found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-800">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection