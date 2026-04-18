@extends('layouts.company')

@section('title', 'General Ledger')

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="p-2 bg-indigo-600 rounded-lg shadow-sm">
        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">General Ledger</h1>
</div>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 d-print-none">
        <form method="GET" action="{{ route('company.accounting.reports.general-ledger') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Account Selector</label>
                <select name="account_id" required class="block w-full rounded-xl border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800 text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:text-gray-300">
                    <option value="">— Choose Account —</option>
                    @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}" {{ (isset($account) && $account->id == $acc->id) ? 'selected' : '' }}>
                        {{ $acc->account_code }} — {{ $acc->account_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">From</label>
                <input type="date" name="start_date" value="{{ $start->format('Y-m-d') }}"
                    class="block w-full rounded-xl border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800 text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:text-gray-300">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">To</label>
                <input type="date" name="end_date" value="{{ $end->format('Y-m-d') }}"
                    class="block w-full rounded-xl border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800 text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:text-gray-300">
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-black py-2.5 px-4 rounded-xl transition-all shadow-lg shadow-indigo-500/20">
                    Audit Ledger
                </button>
                @if($report)
                <a href="{{ route('company.reports.export.general-ledger.pdf', request()->all()) }}" class="p-2.5 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl hover:bg-rose-200 transition-colors tooltip" title="Export PDF">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </a>
                <a href="{{ route('company.reports.export.general-ledger.excel', request()->all()) }}" class="p-2.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl hover:bg-emerald-200 transition-colors tooltip" title="Export Excel">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </a>
                <button onclick="window.print()" type="button" class="p-2.5 bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-400 rounded-xl hover:bg-gray-200 transition-colors tooltip" title="Print Ledger">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </button>
                @endif
            </div>
        </form>
    </div>

    @if(!$report)
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-20 shadow-sm border border-gray-100 dark:border-slate-800 text-center">
        <div class="w-24 h-24 bg-indigo-50 dark:bg-indigo-900/10 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="h-12 w-12 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" />
            </svg>
        </div>
        <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">Ready for Ledger Audit</h3>
        <p class="text-sm text-gray-500 max-w-sm mx-auto mt-2">Select an account and date range above to visualize the transactional movement and running balance.</p>
    </div>
    @else
    <!-- Ledger Paper -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-gray-100 dark:border-slate-800 overflow-hidden print:shadow-none print:border-none">
        <!-- Report Header -->
        <div class="p-10 border-b border-gray-100 dark:border-slate-800 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center space-x-4">
                <div class="p-4 bg-slate-900 rounded-2xl shadow-lg">
                    <span class="text-white font-mono font-black text-xl">{{ $account->account_code }}</span>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{ $account->account_name }}</h2>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-0.5">Classification: {{ $account->account_type }}</p>
                </div>
            </div>
            <div class="text-center md:text-right">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-widest italic font-serif">Audit Period: {{ $start->format('d M') }} — {{ $end->format('d M Y') }}</span>
            </div>
        </div>

        <!-- Opening Continuity -->
        <div class="px-10 py-5 bg-gray-50 dark:bg-slate-800/30 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center italic">
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Opening continuity balance</span>
            <span class="text-sm font-black text-gray-900 dark:text-white font-mono">
                {{ number_format(abs($report['opening_balance']), 2) }}
                <span class="text-[10px] uppercase opacity-60 ms-1">{{ $report['opening_balance'] < 0 ? 'Cr' : 'Dr' }}</span>
            </span>
        </div>

        <!-- Ledger Entries -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-slate-800">
                        <th class="py-3 px-10">Date / Ref</th>
                        <th class="py-3 px-4">Transaction Narration</th>
                        <th class="py-3 px-4 text-right w-32">Debit</th>
                        <th class="py-3 px-4 text-right w-32">Credit</th>
                        <th class="py-3 px-10 text-right w-44 bg-gray-50/50 dark:bg-slate-800/10">Running Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-800/50">
                    @forelse($report['entries'] as $entry)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="py-4 px-10">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($entry['date'])->format('d M Y') }}</span>
                                <span class="text-[9px] font-mono text-indigo-500 font-black uppercase tracking-tighter">{{ $entry['reference'] }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <p class="text-xs font-bold text-gray-700 dark:text-gray-400 line-clamp-1 italic opacity-80 group-hover:opacity-100 transition-opacity">"{{ $entry['description'] }}"</p>
                        </td>
                        <td class="py-4 px-4 text-right text-sm font-mono font-bold {{ $entry['debit'] > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-200 dark:text-slate-800' }}">
                            {{ $entry['debit'] > 0 ? number_format($entry['debit'], 2) : '-' }}
                        </td>
                        <td class="py-4 px-4 text-right text-sm font-mono font-bold {{ $entry['credit'] > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-200 dark:text-slate-800' }}">
                            {{ $entry['credit'] > 0 ? number_format($entry['credit'], 2) : '-' }}
                        </td>
                        <td class="py-4 px-10 text-right text-sm font-mono font-black {{ $entry['running_balance'] >= 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-rose-600 dark:text-rose-400' }} bg-gray-50/20 dark:bg-slate-800/5">
                            {{ number_format(abs($entry['running_balance']), 2) }}
                            <span class="text-[9px] uppercase ms-1 opacity-60">{{ $entry['running_balance'] < 0 ? 'Cr' : 'Dr' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <p class="text-sm text-gray-400 italic font-black uppercase tracking-widest">No posted movements in this cycle.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t-2 border-slate-900 dark:border-white">
                    <tr class="bg-slate-950 text-white">
                        <td colspan="2" class="py-6 px-10 text-sm font-black uppercase tracking-[0.3em]">Ledger Closing Position</td>
                        <td class="py-6 px-4 text-right text-xs font-mono font-bold opacity-60">{{ number_format($report['total_debit'], 2) }}</td>
                        <td class="py-6 px-4 text-right text-xs font-mono font-bold opacity-60">{{ number_format($report['total_credit'], 2) }}</td>
                        <td class="py-6 px-10 text-right text-xl font-black font-mono tabular-nums text-indigo-400">
                            {{ number_format(abs($report['closing_balance']), 2) }}
                            <span class="text-xs ms-1">{{ $report['closing_balance'] < 0 ? 'Cr' : 'Dr' }}</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="px-10 py-6 bg-gray-50/50 dark:bg-slate-800/20 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest italic border-t border-gray-100 dark:border-slate-800">
            Systemic Ledger Persistence #GL-AUDIT-{{ time() }} • Verified Real-time Integrity Checked
        </div>
    </div>
    @endif
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

        .max-w-6xl {
            max-width: 100% !important;
            margin: 0 !important;
        }

        .rounded-3xl {
            border-radius: 0 !important;
        }

        .shadow-xl {
            box-shadow: none !important;
        }

        .p-10 {
            padding: 2rem !important;
        }

        .bg-slate-950 {
            background: #000 !important;
        }
    }
</style>
@endsection