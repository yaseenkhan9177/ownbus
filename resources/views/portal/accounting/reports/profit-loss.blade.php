@extends('layouts.company')

@section('title', 'Profit & Loss Statement')

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="p-2 bg-emerald-600 rounded-lg shadow-sm">
        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Profit & Loss</h1>
</div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 d-print-none">
        <form method="GET" action="{{ route('company.accounting.reports.pnl') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ $start->format('Y-m-d') }}"
                    class="block w-full rounded-xl border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800 text-sm focus:ring-emerald-500 focus:border-emerald-500 dark:text-gray-300">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ $end->format('Y-m-d') }}"
                    class="block w-full rounded-xl border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800 text-sm focus:ring-emerald-500 focus:border-emerald-500 dark:text-gray-300">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Branch</label>
                <select name="branch_id" class="block w-full rounded-xl border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800 text-sm focus:ring-emerald-500 focus:border-emerald-500 dark:text-gray-300">
                    <option value="">Consolidated (All Branches)</option>
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-black py-2.5 px-4 rounded-xl transition-all shadow-lg shadow-emerald-500/20">
                    Update Report
                </button>
                <a href="{{ route('company.reports.export.profit-loss.pdf', request()->all()) }}" class="p-2.5 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl hover:bg-rose-200 transition-colors" title="Export PDF">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </a>
                <a href="{{ route('company.reports.export.profit-loss.excel', request()->all()) }}" class="p-2.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl hover:bg-emerald-200 transition-colors" title="Export Excel">
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

    <!-- P&L Statement Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-gray-100 dark:border-slate-800 overflow-hidden print:shadow-none print:border-none">
        <!-- Report Header -->
        <div class="p-12 border-b border-gray-100 dark:border-slate-800 text-center relative">
            <div class="absolute top-0 right-0 p-8 d-print-none">
                <span class="inline-flex items-center px-4 py-1 bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-gray-400 text-[10px] font-black rounded-full uppercase tracking-widest italic">
                    Certified Accounting Log
                </span>
            </div>

            <div class="w-16 h-16 bg-gray-900 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                <span class="text-white font-black text-2xl uppercase">{{ substr(auth()->user()->company->name, 0, 1) }}</span>
            </div>

            <h2 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">Statement of Profit or Loss</h2>
            <div class="mt-2 flex items-center justify-center space-x-3">
                <span class="text-sm font-bold text-gray-500 uppercase tracking-widest">Period: {{ $start->format('d M') }} — {{ $end->format('d M Y') }}</span>
                @if($branchId)
                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                <span class="text-sm font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Branch Context Active</span>
                @endif
            </div>
        </div>

        <!-- Report Content -->
        <div class="p-12 space-y-12">
            <!-- Revenue Section -->
            <section>
                <div class="flex items-center justify-between border-b-2 border-gray-900 dark:border-white pb-3 mb-6">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-widest italic">Operating Revenue</h3>
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Currency: AED</span>
                </div>

                <div class="space-y-4 px-4">
                    @forelse($report['income'] as $acc)
                    <div class="flex justify-between items-center group">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-gray-800 dark:text-gray-300 group-hover:text-gray-900 transition-colors">{{ $acc['account_name'] }}</span>
                            <span class="text-[10px] font-mono text-gray-400">[{{ $acc['account_code'] }}]</span>
                        </div>
                        <span class="text-sm font-black text-gray-900 dark:text-white font-mono">{{ number_format($acc['balance'], 2) }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 italic text-center py-4">No income records found for this period.</p>
                    @endforelse

                    <div class="mt-8 pt-4 border-t border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/30 p-4 rounded-2xl flex justify-between items-center">
                        <span class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Total Operating Revenue</span>
                        <span class="text-xl font-black text-gray-900 dark:text-white font-mono decoration-double underline decoration-gray-400">{{ number_format($report['total_income'], 2) }}</span>
                    </div>
                </div>
            </section>

            <!-- Expense Section -->
            <section>
                <div class="flex items-center justify-between border-b-2 border-gray-900 dark:border-white pb-3 mb-6">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-widest italic">Operating Expenses</h3>
                </div>

                <div class="space-y-4 px-4">
                    @forelse($report['expenses'] as $acc)
                    <div class="flex justify-between items-center group">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-gray-800 dark:text-gray-300 group-hover:text-gray-900 transition-colors">{{ $acc['account_name'] }}</span>
                            <span class="text-[10px] font-mono text-gray-400">[{{ $acc['account_code'] }}]</span>
                        </div>
                        <span class="text-sm font-bold text-gray-500 font-mono">({{ number_format($acc['balance'], 2) }})</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 italic text-center py-4">No expense records found for this period.</p>
                    @endforelse

                    <div class="mt-8 pt-4 border-t border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/30 p-4 rounded-2xl flex justify-between items-center">
                        <span class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Total Operating Expenses</span>
                        <span class="text-xl font-black text-rose-600 dark:text-rose-400 font-mono decoration-double underline decoration-gray-400">({{ number_format($report['total_expenses'], 2) }})</span>
                    </div>
                </div>
            </section>

            <!-- Bottom Line Section -->
            <div class="mt-12 p-8 rounded-[2rem] {{ $report['net_profit'] >= 0 ? 'bg-emerald-600' : 'bg-rose-600' }} text-white shadow-2xl shadow-emerald-500/10">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <h4 class="text-sm font-black uppercase tracking-[0.3em] opacity-80 mb-1">Final Performance Indicator</h4>
                        <h1 class="text-4xl font-black uppercase tracking-tighter">Net Operating Income</h1>
                        <p class="text-xs font-bold opacity-60 mt-1 uppercase tracking-widest italic">Consolidated for the period ended {{ $end->format('d M Y') }}</p>
                    </div>
                    <div class="text-right">
                        <h1 class="text-5xl font-black font-mono tracking-tighter tabular-nums">
                            {{ $report['net_profit'] >= 0 ? '' : '(' }}{{ number_format(abs($report['net_profit']), 2) }}{{ $report['net_profit'] < 0 ? ')' : '' }}
                        </h1>
                        <div class="mt-2 flex items-center justify-end space-x-2">
                            <div class="p-1 px-2.5 bg-white/20 backdrop-blur-sm rounded-lg text-[9px] font-black uppercase tracking-widest">Currency: AED</div>
                            <div class="p-1 px-2.5 bg-white/20 backdrop-blur-sm rounded-lg text-[9px] font-black uppercase tracking-widest">Status: Validated</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Audit -->
        <div class="px-12 py-8 bg-gray-50/50 dark:bg-slate-800/20 border-t border-gray-100 dark:border-slate-800 text-center text-[10px] text-gray-400 font-bold uppercase tracking-widest">
            Generated by ERP Intelligence Module • Audit Index #ACC-PNL-{{ time() }} • {{ now()->toDateTimeString() }}
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

        .rounded-3xl,
        .rounded-[2rem] {
            border-radius: 0 !important;
        }

        .shadow-xl,
        .shadow-2xl {
            box-shadow: none !important;
        }

        .p-12 {
            padding: 2rem !important;
        }
    }
</style>
@endsection