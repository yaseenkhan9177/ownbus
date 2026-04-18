@extends('layouts.company')

@section('title', 'Cash Flow Statement')

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="p-2 bg-cyan-600 rounded-lg shadow-sm">
        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
        </svg>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Cash Flow</h1>
</div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 d-print-none">
        <form method="GET" action="{{ route('company.accounting.reports.cash-flow') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ $start->format('Y-m-d') }}"
                    class="block w-full rounded-xl border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800 text-sm focus:ring-cyan-500 focus:border-cyan-500 dark:text-gray-300">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ $end->format('Y-m-d') }}"
                    class="block w-full rounded-xl border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800 text-sm focus:ring-cyan-500 focus:border-cyan-500 dark:text-gray-300">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Branch Context</label>
                <select name="branch_id" class="block w-full rounded-xl border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800 text-sm focus:ring-cyan-500 focus:border-cyan-500 dark:text-gray-300">
                    <option value="">Consolidated (All Branches)</option>
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-cyan-600 hover:bg-cyan-700 text-white font-black py-2.5 px-4 rounded-xl transition-all shadow-lg shadow-cyan-500/20">
                    Update Report
                </button>
                <a href="{{ route('company.reports.export.cash-flow.pdf', request()->all()) }}" class="p-2.5 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl hover:bg-rose-200 transition-colors" title="Export PDF">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </a>
                <a href="{{ route('company.reports.export.cash-flow.excel', request()->all()) }}" class="p-2.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl hover:bg-emerald-200 transition-colors" title="Export Excel">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </a>
                <button onclick="window.print()" type="button" class="p-2.5 bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-400 rounded-xl hover:bg-gray-200 transition-colors" title="Print Report">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <!-- Statement Paper -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-gray-100 dark:border-slate-800 overflow-hidden print:shadow-none print:border-none">
        <!-- Header -->
        <div class="p-12 border-b border-gray-100 dark:border-slate-800 text-center relative">
            <h2 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">Statement of Cash Flows</h2>
            <div class="mt-2 flex items-center justify-center space-x-3">
                <span class="text-sm font-bold text-gray-500 uppercase tracking-widest italic font-serif text-[16px]">Period ended {{ $end->format('d M Y') }}</span>
            </div>
        </div>

        <div class="p-12 space-y-12">
            <!-- Opening Balance Card -->
            <div class="p-6 rounded-2xl bg-gray-50 dark:bg-slate-800/30 border border-gray-100 dark:border-slate-800 flex justify-between items-center group">
                <div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Liquidity Base</span>
                    <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase">Cash & Equivalents (Opening)</h4>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">At the commencement of {{ $start->format('d M Y') }}</p>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-black text-gray-900 dark:text-white font-mono">{{ number_format($report['opening_cash'], 2) }}</span>
                </div>
            </div>

            <!-- Operating Section -->
            <section>
                <div class="flex items-center justify-between border-b-2 border-emerald-600 pb-3 mb-6">
                    <h3 class="text-lg font-black text-emerald-600 uppercase tracking-widest italic font-bold">Operating Dynamics</h3>
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Impact: AED</span>
                </div>

                <div class="space-y-4 px-4">
                    @forelse($report['operating'] as $item)
                    <div class="flex justify-between items-center group">
                        <span class="text-sm font-bold text-gray-800 dark:text-gray-300 group-hover:text-gray-900 transition-colors">{{ $item['account_name'] }}</span>
                        <span class="text-sm font-black {{ $item['net'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }} font-mono">
                            {{ $item['net'] >= 0 ? '+' : '' }}{{ number_format($item['net'], 2) }}
                        </span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 italic text-center py-4">No operating cash movement.</p>
                    @endforelse

                    <div class="mt-8 pt-4 border-t border-gray-100 dark:border-slate-800 bg-emerald-50/50 dark:bg-emerald-900/10 p-5 rounded-2xl flex justify-between items-center">
                        <span class="text-sm font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-widest">NET OPERATING CASH</span>
                        <span class="text-xl font-black text-emerald-700 dark:text-emerald-400 font-mono">{{ $report['operating_total'] >= 0 ? '+' : '' }}{{ number_format($report['operating_total'], 2) }}</span>
                    </div>
                </div>
            </section>

            <!-- Investing Section -->
            <section>
                <div class="flex items-center justify-between border-b-2 border-cyan-600 pb-3 mb-6">
                    <h3 class="text-lg font-black text-cyan-600 uppercase tracking-widest italic font-bold">Investing Activities</h3>
                </div>

                <div class="space-y-4 px-4">
                    @forelse($report['investing'] as $item)
                    <div class="flex justify-between items-center group">
                        <span class="text-sm font-bold text-gray-800 dark:text-gray-300 group-hover:text-gray-900 transition-colors">{{ $item['account_name'] }}</span>
                        <span class="text-sm font-black {{ $item['net'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }} font-mono">
                            {{ $item['net'] >= 0 ? '+' : '' }}{{ number_format($item['net'], 2) }}
                        </span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 italic text-center py-4">No investing activity.</p>
                    @endforelse

                    <div class="mt-8 pt-4 border-t border-gray-100 dark:border-slate-800 bg-cyan-50/50 dark:bg-cyan-900/10 p-5 rounded-2xl flex justify-between items-center">
                        <span class="text-sm font-black text-cyan-700 dark:text-cyan-400 uppercase tracking-widest">NET INVESTING CASH</span>
                        <span class="text-xl font-black text-cyan-700 dark:text-cyan-400 font-mono">{{ $report['investing_total'] >= 0 ? '+' : '' }}{{ number_format($report['investing_total'], 2) }}</span>
                    </div>
                </div>
            </section>

            <!-- Financing Section -->
            <section>
                <div class="flex items-center justify-between border-b-2 border-amber-600 pb-3 mb-6">
                    <h3 class="text-lg font-black text-amber-600 uppercase tracking-widest italic font-bold">Financing & Capital</h3>
                </div>

                <div class="space-y-4 px-4">
                    @forelse($report['financing'] as $item)
                    <div class="flex justify-between items-center group">
                        <span class="text-sm font-bold text-gray-800 dark:text-gray-300 group-hover:text-gray-900 transition-colors">{{ $item['account_name'] }}</span>
                        <span class="text-sm font-black {{ $item['net'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }} font-mono">
                            {{ $item['net'] >= 0 ? '+' : '' }}{{ number_format($item['net'], 2) }}
                        </span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 italic text-center py-4">No financing activity.</p>
                    @endforelse

                    <div class="mt-8 pt-4 border-t border-gray-100 dark:border-slate-800 bg-amber-50/50 dark:bg-amber-900/10 p-5 rounded-2xl flex justify-between items-center">
                        <span class="text-sm font-black text-amber-700 dark:text-amber-400 uppercase tracking-widest">NET FINANCING CASH</span>
                        <span class="text-xl font-black text-amber-700 dark:text-amber-400 font-mono">{{ $report['financing_total'] >= 0 ? '+' : '' }}{{ number_format($report['financing_total'], 2) }}</span>
                    </div>
                </div>
            </section>

            <!-- Closing Summary -->
            <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="p-8 rounded-3xl bg-gray-50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-800">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Net Surplus/Deficit</span>
                        <span class="text-lg font-black font-mono {{ $report['net_movement'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $report['net_movement'] >= 0 ? '+' : '' }}{{ number_format($report['net_movement'], 2) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center border-t border-gray-200 dark:border-slate-700 pt-4 opacity-60">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Opening Reserves</span>
                        <span class="text-sm font-bold font-mono text-gray-900 dark:text-white">{{ number_format($report['opening_cash'], 2) }}</span>
                    </div>
                </div>

                <div class="p-8 rounded-3xl {{ $report['closing_cash'] >= 0 ? 'bg-slate-950' : 'bg-rose-950' }} text-white shadow-2xl flex flex-col justify-center">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] mb-2">Net Liquidity Position</span>
                    <h1 class="text-4xl font-black font-mono tabular-nums text-cyan-400 tracking-tighter">{{ number_format($report['closing_cash'], 2) }}</h1>
                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-2">Certified Cash Equivalents as of {{ $end->format('d M') }}</p>
                </div>
            </div>
        </div>

        <div class="px-12 py-8 bg-gray-50/50 dark:bg-slate-800/20 border-t border-gray-100 dark:border-slate-800 text-center text-[10px] text-gray-400 font-bold uppercase tracking-widest italic">
            Flow Validation Engine #CASH-{{ time() }} • Audited Periodical Statement
        </div>
    </div>
</div>

<style>
    @media print {

        header,
        .d-print-none {
            display: none !important;
        }

        body {
            background: white !important;
            -webkit-print-color-adjust: exact;
        }

        .max-w-5xl {
            max-width: 100% !important;
            margin: 0 !important;
        }

        .rounded-3xl {
            border-radius: 0 !important;
        }

        .shadow-xl,
        .shadow-2xl {
            box-shadow: none !important;
        }

        .p-12 {
            padding: 2rem !important;
        }

        .bg-slate-950 {
            background: #000 !important;
        }
    }
</style>
@endsection