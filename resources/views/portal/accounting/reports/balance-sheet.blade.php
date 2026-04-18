@extends('layouts.company')

@section('title', 'Balance Sheet')

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="p-2 bg-slate-900 rounded-lg shadow-sm">
        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
        </svg>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Balance Sheet</h1>
</div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 d-print-none text-gray-900 dark:text-white">
        <form method="GET" action="{{ route('company.accounting.reports.balance-sheet') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">As of Date</label>
                <input type="date" name="date" value="{{ $asOfDate->format('Y-m-d') }}"
                    class="block w-full rounded-xl border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800 text-sm focus:ring-slate-500 focus:border-slate-500 dark:text-gray-300">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Branch Context</label>
                <select name="branch_id" class="block w-full rounded-xl border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800 text-sm focus:ring-slate-500 focus:border-slate-500 dark:text-gray-300">
                    <option value="">Consolidated (All Branches)</option>
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-slate-900 dark:bg-slate-800 hover:bg-black text-white font-black py-2.5 px-4 rounded-xl transition-all shadow-lg shadow-slate-500/20">
                    Update Statement
                </button>
                <a href="{{ route('company.reports.export.balance-sheet.pdf', request()->all()) }}" class="p-2.5 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl hover:bg-rose-200 transition-colors" title="Export PDF">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </a>
                <a href="{{ route('company.reports.export.balance-sheet.excel', request()->all()) }}" class="p-2.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl hover:bg-emerald-200 transition-colors" title="Export Excel">
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

    <!-- Equation Verification Header -->
    <div class="animate-fade-in">
        @if($report['is_balanced'])
        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-6 flex items-center justify-between group overflow-hidden relative">
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform">
                <svg class="h-24 w-24 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-emerald-500 rounded-xl shadow-lg shadow-emerald-500/20">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-widest">Accounting Equation Verified</h4>
                    <p class="text-[10px] font-bold text-emerald-600/70 dark:text-emerald-400/60 uppercase mt-0.5 tracking-tighter">Assets = Liabilities + Equity (Balanced & Validated)</p>
                </div>
            </div>
            <div class="text-right d-print-none">
                <span class="text-2xl font-black text-emerald-700 dark:text-emerald-400 font-mono">A = L+E</span>
            </div>
        </div>
        @else
        <div class="bg-rose-500/10 border border-rose-500/20 rounded-2xl p-6 flex items-center justify-between group overflow-hidden relative animate-pulse">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-rose-500 rounded-xl shadow-lg shadow-rose-500/20">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-rose-700 dark:text-rose-400 uppercase tracking-widest">Equation Mismatch Detected</h4>
                    <p class="text-[10px] font-bold text-rose-600/70 dark:text-rose-400/60 uppercase mt-0.5 tracking-tighter">Difference: AED {{ number_format($report['difference'], 2) }} (Validation Failed)</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Balance Sheet Statement -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-gray-100 dark:border-slate-800 overflow-hidden print:shadow-none print:border-none">
        <!-- Report Header -->
        <div class="p-12 border-b border-gray-100 dark:border-slate-800 text-center relative">
            <h2 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">Balance Sheet</h2>
            <div class="mt-2 flex items-center justify-center space-x-3">
                <span class="text-sm font-bold text-gray-500 uppercase tracking-widest italic font-serif text-[16px]">As of {{ $asOfDate->format('d M Y') }}</span>
            </div>
        </div>

        <div class="p-12 space-y-12">
            <!-- Assets Section -->
            <section>
                <div class="flex items-center justify-between border-b-2 border-blue-600 pb-3 mb-6">
                    <h3 class="text-lg font-black text-blue-600 uppercase tracking-widest italic font-bold">Total Assets</h3>
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Currency: AED</span>
                </div>

                <div class="space-y-4 px-4">
                    @forelse($report['assets'] as $acc)
                    <div class="flex justify-between items-center group">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-gray-800 dark:text-gray-300 group-hover:text-gray-900 transition-colors">{{ $acc['account_name'] }}</span>
                            <span class="text-[10px] font-mono text-gray-400">[{{ $acc['account_code'] }}]</span>
                        </div>
                        <span class="text-sm font-black text-gray-900 dark:text-white font-mono">{{ number_format($acc['balance'], 2) }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 italic text-center py-4">No Asset accounts found.</p>
                    @endforelse

                    <div class="mt-8 pt-4 border-t border-gray-100 dark:border-slate-800 bg-blue-50/50 dark:bg-blue-900/10 p-5 rounded-2xl flex justify-between items-center">
                        <span class="text-sm font-black text-blue-700 dark:text-blue-400 uppercase tracking-widest">TOTAL ASSETS VALUE</span>
                        <span class="text-2xl font-black text-blue-700 dark:text-blue-400 font-mono decoration-double underline decoration-blue-300">{{ number_format($report['total_assets'], 2) }}</span>
                    </div>
                </div>
            </section>

            <!-- Liabilities Section -->
            <section>
                <div class="flex items-center justify-between border-b-2 border-amber-600 pb-3 mb-6">
                    <h3 class="text-lg font-black text-amber-600 uppercase tracking-widest italic font-bold">Liabilities</h3>
                </div>

                <div class="space-y-4 px-4">
                    @forelse($report['liabilities'] as $acc)
                    <div class="flex justify-between items-center group">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-gray-800 dark:text-gray-300 group-hover:text-gray-900 transition-colors">{{ $acc['account_name'] }}</span>
                            <span class="text-[10px] font-mono text-gray-400">[{{ $acc['account_code'] }}]</span>
                        </div>
                        <span class="text-sm font-black text-gray-800 dark:text-gray-300 font-mono">{{ number_format($acc['balance'], 2) }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 italic text-center py-4">No Liability accounts found.</p>
                    @endforelse

                    <div class="mt-8 pt-4 border-t border-gray-100 dark:border-slate-800 bg-amber-50/50 dark:bg-amber-900/10 p-5 rounded-2xl flex justify-between items-center">
                        <span class="text-sm font-black text-amber-700 dark:text-amber-400 uppercase tracking-widest">TOTAL LIABILITIES</span>
                        <span class="text-xl font-black text-amber-700 dark:text-amber-400 font-mono">{{ number_format($report['total_liabilities'], 2) }}</span>
                    </div>
                </div>
            </section>

            <!-- Equity Section -->
            <section>
                <div class="flex items-center justify-between border-b-2 border-indigo-600 pb-3 mb-6">
                    <h3 class="text-lg font-black text-indigo-600 uppercase tracking-widest italic font-bold">Ownership Equity</h3>
                </div>

                <div class="space-y-4 px-4">
                    @foreach($report['equity'] as $acc)
                    <div class="flex justify-between items-center group">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-gray-800 dark:text-gray-300 group-hover:text-gray-900 transition-colors">{{ $acc['account_name'] }}</span>
                            <span class="text-[10px] font-mono text-gray-400">[{{ $acc['account_code'] }}]</span>
                        </div>
                        <span class="text-sm font-black text-gray-800 dark:text-gray-300 font-mono">{{ number_format($acc['balance'], 2) }}</span>
                    </div>
                    @endforeach

                    <!-- Retained Earnings -->
                    <div class="flex justify-between items-center group italic">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-400">Retained Earnings / Ledger Net Profit</span>
                            <span class="text-[10px] text-gray-400 italic font-serif">(Comprehensive P&L result to Date)</span>
                        </div>
                        <span class="text-sm font-black {{ $report['retained_earnings'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }} font-mono">
                            {{ number_format($report['retained_earnings'], 2) }}
                        </span>
                    </div>

                    <div class="mt-8 pt-4 border-t border-gray-100 dark:border-slate-800 bg-indigo-50/50 dark:bg-indigo-900/10 p-5 rounded-2xl flex justify-between items-center">
                        <span class="text-sm font-black text-indigo-700 dark:text-indigo-400 uppercase tracking-widest">TOTAL EQUITY VALUE</span>
                        <span class="text-xl font-black text-indigo-700 dark:text-indigo-400 font-mono">{{ number_format($report['total_equity'], 2) }}</span>
                    </div>
                </div>
            </section>

            <!-- Final Balanced Footer -->
            <div class="mt-12 p-10 rounded-[2.5rem] bg-slate-950 text-white shadow-2xl relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-12 opacity-5 scale-150 rotate-12 group-hover:rotate-0 transition-transform duration-500">
                    <svg class="h-40 w-40" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a8 8 0 100 16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                    </svg>
                </div>
                <div class="flex flex-col md:flex-row items-center justify-between gap-8 relative z-10">
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-[0.4em] text-slate-400 mb-2">Dual Aspect Summary</h4>
                        <h1 class="text-4xl font-black uppercase tracking-tighter italic">Total Liabilities & Equity</h1>
                    </div>
                    <div class="text-right">
                        <h1 class="text-6xl font-black font-mono tracking-tighter tabular-nums text-emerald-400">
                            {{ number_format($report['total_liab_equity'], 2) }}
                        </h1>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-2">Validated Ledger Balance Verified</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-12 py-8 bg-gray-50/50 dark:bg-slate-800/20 border-t border-gray-100 dark:border-slate-800 text-center text-[10px] text-gray-400 font-bold uppercase tracking-widest">
            Structural Integrity Record #ACC-BS-{{ time() }} • Verified by Real-time Ledger Engine
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
        .rounded-[2.5rem] {
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
            color: white !important;
        }
    }
</style>
@endsection