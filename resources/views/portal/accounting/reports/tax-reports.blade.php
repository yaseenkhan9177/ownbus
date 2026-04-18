@extends('layouts.company')

@section('title', 'Tax Reports')

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="p-2 bg-indigo-600 rounded-lg shadow-sm">
        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tax Intelligence</h1>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-800">
        <form action="{{ route('company.accounting.reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ $start->format('Y-m-d') }}"
                    class="block w-full rounded-xl border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800 text-sm focus:ring-blue-500 focus:border-blue-500 dark:text-gray-300">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ $end->format('Y-m-d') }}"
                    class="block w-full rounded-xl border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800 text-sm focus:ring-blue-500 focus:border-blue-500 dark:text-gray-300">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-xl transition-all shadow-lg shadow-blue-500/20">
                    Apply Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-800 relative overflow-hidden">
            <div class="absolute right-0 top-0 p-4 opacity-10">
                <svg class="h-16 w-16" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" />
                </svg>
            </div>
            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">VAT Collected</p>
            <p class="mt-2 text-3xl font-black text-gray-900 dark:text-white">AED {{ number_format($report['vat_collected'], 2) }}</p>
            <p class="mt-1 text-xs text-green-600 font-bold">Output VAT (on Sales)</p>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-800 relative overflow-hidden">
            <div class="absolute right-0 top-0 p-4 opacity-10 text-red-500">
                <svg class="h-16 w-16" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M11 2a1 1 0 011 1v1h3a1 1 0 011 1v3a1 1 0 11-2 0V6h-2v11a1 1 0 11-2 0V6H9v11a1 1 0 11-2 0V6H5v3a1 1 0 11-2 0V5a1 1 0 011-1h3V3a1 1 0 011-1h2z" />
                </svg>
            </div>
            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">VAT Paid</p>
            <p class="mt-2 text-3xl font-black text-gray-900 dark:text-white">AED {{ number_format($report['vat_paid'], 2) }}</p>
            <p class="mt-1 text-xs text-blue-600 font-bold">Input VAT (on Expenses)</p>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-800 relative overflow-hidden {{ $report['net_payable'] >= 0 ? 'bg-amber-50 dark:bg-amber-900/10' : 'bg-emerald-50 dark:bg-emerald-900/10' }}">
            <div class="absolute right-0 top-0 p-4 opacity-10 text-amber-500">
                <svg class="h-16 w-16" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                </svg>
            </div>
            <p class="text-sm font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Net Tax Payable</p>
            <p class="mt-2 text-3xl font-black text-gray-900 dark:text-white">AED {{ number_format($report['net_payable'], 2) }}</p>
            <p class="mt-1 text-xs font-bold {{ $report['net_payable'] >= 0 ? 'text-amber-700' : 'text-emerald-700' }}">
                {{ $report['net_payable'] >= 0 ? 'Payable to FBR' : 'Tax Refundable / Credit' }}
            </p>
        </div>
    </div>

    <!-- Details Section -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-tight">FBR Compliance Summary</h3>
            <span class="px-3 py-1 bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-400 text-[10px] font-black rounded-full">UAE VAT STANDARDS</span>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h4 class="text-sm font-bold text-gray-500 uppercase mb-4 tracking-widest">Revenue Breakdown</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-slate-800/50 rounded-xl">
                            <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Standard Rated Sales (5%)</span>
                            <span class="text-sm font-black text-gray-900 dark:text-white">AED {{ number_format($report['vat_collected'] / 0.05, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 rounded-xl border border-dashed border-gray-200 dark:border-slate-800">
                            <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Total VAT Collected</span>
                            <span class="text-sm font-black text-gray-900 dark:text-white">AED {{ number_format($report['vat_collected'], 2) }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-500 uppercase mb-4 tracking-widest">Input Tax Breakdown</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-slate-800/50 rounded-xl">
                            <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Eligible Purchases</span>
                            <span class="text-sm font-black text-gray-900 dark:text-white">AED {{ number_format($report['vat_paid'] / 0.05, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 rounded-xl border border-dashed border-gray-200 dark:border-slate-800">
                            <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Total Input Tax Paid</span>
                            <span class="text-sm font-black text-gray-900 dark:text-white">AED {{ number_format($report['vat_paid'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 p-6 bg-blue-600 rounded-2xl text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-lg font-bold">Tax Intelligence Summary</h4>
                        <p class="text-sm text-blue-100 opacity-80 mt-1">Based on posted journal entries from {{ $start->format('d M') }} to {{ $end->format('d M Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold uppercase opacity-60">Filing Required</p>
                        <p class="text-xl font-black">Monthly Return</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection