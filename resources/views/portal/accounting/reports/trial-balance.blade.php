@extends('layouts.company')

@section('title', 'Trial Balance')

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="p-2 bg-slate-800 rounded-lg shadow-sm font-black text-white">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Trial Balance</h1>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 d-print-none">
        <form method="GET" action="{{ route('company.accounting.reports.trial-balance') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ $start->format('Y-m-d') }}"
                    class="block w-full rounded-xl border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800 text-sm focus:ring-slate-500 focus:border-slate-500 dark:text-gray-300">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ $end->format('Y-m-d') }}"
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
                <button type="submit" class="flex-1 bg-slate-900 dark:bg-slate-800 hover:bg-black text-white font-black py-2.5 px-4 rounded-xl transition-all">
                    Run Verification
                </button>
                <a href="{{ route('company.reports.export.trial-balance.pdf', request()->all()) }}" class="p-2.5 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl hover:bg-rose-200 transition-colors" title="Export PDF">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </a>
                <a href="{{ route('company.reports.export.trial-balance.excel', request()->all()) }}" class="p-2.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl hover:bg-emerald-200 transition-colors" title="Export Excel">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </a>
                <button type="button" onclick="window.print()" class="p-2.5 bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-400 rounded-xl hover:bg-gray-200 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <!-- Integrity Status -->
    <div class="animate-fade-in px-2">
        @if($report['is_balanced'])
        <div class="bg-emerald-50 dark:bg-emerald-900/10 border-l-4 border-emerald-500 p-5 rounded-r-2xl flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="p-2 bg-emerald-500 rounded-full text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-emerald-800 dark:text-emerald-400 uppercase tracking-widest">Double-Entry Verified</h4>
                    <p class="text-[10px] font-bold text-emerald-600/70 dark:text-emerald-400/60 uppercase">Structural integrity confirmed. Debits match Credits.</p>
                </div>
            </div>
            <span class="text-[10px] font-black bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 px-3 py-1 rounded-full uppercase tracking-tighter">Balanced Ledger</span>
        </div>
        @else
        <div class="bg-rose-50 dark:bg-rose-900/10 border-l-4 border-rose-500 p-5 rounded-r-2xl flex items-center justify-between animate-pulse">
            <div class="flex items-center space-x-4">
                <div class="p-2 bg-rose-500 rounded-full text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-rose-800 dark:text-rose-400 uppercase tracking-widest">Integrity Breach Detected</h4>
                    <p class="text-[10px] font-bold text-rose-600/70 dark:text-rose-400/60 uppercase">Mismatch of AED {{ number_format($report['difference'], 2) }} identified.</p>
                </div>
            </div>
            <span class="text-[10px] font-black bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 px-3 py-1 rounded-full uppercase tracking-tighter">Out of Balance</span>
        </div>
        @endif
    </div>

    <!-- Trial Balance Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden print:shadow-none print:border-none">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-800/50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-slate-800">
                        <th class="py-4 pl-8 pr-4 min-w-[200px]" rowspan="2">Account Matrix</th>
                        <th class="py-4 px-4 text-center border-l border-gray-100 dark:border-slate-800" rowspan="2">Type</th>
                        <th class="py-4 px-4 text-right border-l border-gray-100 dark:border-slate-800" rowspan="2">Opening</th>
                        <th class="py-2 px-4 text-center border-l border-gray-100 dark:border-slate-800" colspan="2">Period Activity</th>
                        <th class="py-2 px-4 text-center border-l border-gray-100 dark:border-slate-800 bg-gray-100/30 dark:bg-slate-800/20" colspan="2">Closing Balance</th>
                    </tr>
                    <tr class="bg-gray-50 dark:bg-slate-800/50 text-[9px] font-black text-gray-500 uppercase tracking-tighter border-b border-gray-100 dark:border-slate-800">
                        <th class="py-2 px-4 text-right border-l border-gray-100 dark:border-slate-800">Dr</th>
                        <th class="py-2 px-4 text-right border-l border-gray-100 dark:border-slate-800">Cr</th>
                        <th class="py-2 px-4 text-right border-l border-gray-100 dark:border-slate-800 text-blue-600">Debit</th>
                        <th class="py-2 px-4 text-right border-l border-gray-100 dark:border-slate-800 text-emerald-600">Credit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-800/50">
                    @forelse($report['rows'] as $row)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800/20 transition-colors group">
                        <td class="py-4 pl-8 pr-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-black transition-colors">{{ $row['account_name'] }}</span>
                                <span class="text-[10px] font-mono text-gray-400 tracking-tighter">{{ $row['account_code'] }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest
                                @if($row['account_type'] == 'asset') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                @elseif($row['account_type'] == 'liability') bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400
                                @elseif($row['account_type'] == 'equity') bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400
                                @elseif($row['account_type'] == 'income') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400
                                @elseif($row['account_type'] == 'expense') bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400
                                @else bg-gray-100 text-gray-700 dark:bg-slate-800 dark:text-gray-400 @endif">
                                {{ $row['account_type'] }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-right text-xs font-mono font-bold @if($row['opening_balance'] < 0) text-rose-500 @else text-gray-600 @endif">
                            {{ number_format(abs($row['opening_balance']), 2) }}{{ $row['opening_balance'] < 0 ? ' Cr' : '' }}
                        </td>
                        <td class="py-4 px-4 text-right text-xs font-mono text-gray-500">
                            {{ $row['period_debit'] > 0 ? number_format($row['period_debit'], 2) : '-' }}
                        </td>
                        <td class="py-4 px-4 text-right text-xs font-mono text-gray-500">
                            {{ $row['period_credit'] > 0 ? number_format($row['period_credit'], 2) : '-' }}
                        </td>
                        <td class="py-4 px-4 text-right text-sm font-mono font-black text-blue-600 bg-blue-50/10 dark:bg-blue-950/10">
                            {{ $row['closing_debit'] > 0 ? number_format($row['closing_debit'], 2) : '-' }}
                        </td>
                        <td class="py-4 px-4 text-right text-sm font-mono font-black text-emerald-600 bg-emerald-50/10 dark:bg-emerald-950/10">
                            {{ $row['closing_credit'] > 0 ? number_format($row['closing_credit'], 2) : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-20 text-center">
                            <p class="text-sm text-gray-400 italic font-black uppercase tracking-widest">No structural activity recorded.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t-2 border-slate-900 dark:border-white">
                    <tr class="bg-slate-950 text-white group">
                        <td colspan="5" class="py-5 pl-8 text-sm font-black uppercase tracking-[0.3em]">Consolidated Sequence Totals</td>
                        <td class="py-5 px-4 text-right text-lg font-black font-mono tabular-nums text-blue-400">
                            {{ number_format($report['total_debit'], 2) }}
                        </td>
                        <td class="py-5 px-8 text-right text-lg font-black font-mono tabular-nums text-emerald-400">
                            {{ number_format($report['total_credit'], 2) }}
                        </td>
                    </tr>
                    @if(!$report['is_balanced'])
                    <tr class="bg-rose-600 text-white">
                        <td colspan="5" class="py-2 pl-8 text-[10px] font-black uppercase tracking-widest italic">Integrity gap detection (Out of Balance)</td>
                        <td colspan="2" class="py-2 px-8 text-right text-sm font-black font-mono">{{ number_format($report['difference'], 2) }}</td>
                    </tr>
                    @endif
                </tfoot>
            </table>
        </div>

        <div class="px-8 py-6 bg-gray-50/50 dark:bg-slate-800/20 flex flex-col md:flex-row justify-between items-center gap-4 text-[9px] font-black text-gray-400 uppercase tracking-widest border-t border-gray-100 dark:border-slate-800">
            <div class="flex items-center space-x-2">
                <svg class="h-3 w-3 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>Verification ID #TB-VER-{{ time() }}</span>
            </div>
            <div>Real-time double-entry engine v2.0 • Audit Ready Statement</div>
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

        .shadow-sm,
        .shadow-xl {
            box-shadow: none !important;
        }

        .rounded-3xl {
            border-radius: 0 !important;
        }

        .bg-slate-950 {
            background: #000 !important;
            color: white !important;
        }
    }
</style>
@endsection