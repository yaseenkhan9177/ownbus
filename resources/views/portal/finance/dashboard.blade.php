@extends('layouts.company')

@section('content')
<div class="space-y-6">
    <!-- Economic Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white mt-1 uppercase tracking-tighter">ECONOMIC_DOMINANCE_CENTER</h1>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Financial Intelligence & Cashflow Velocity</p>
        </div>
        <div class="flex items-center gap-3">
            <button x-data @click="$dispatch('open-date-filter')" class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-200 dark:hover:bg-slate-700 transition-all flex items-center gap-2">
                <i class="bi bi-calendar3"></i>
                TEMPORAL_FILTER
            </button>
            <a href="{{ route('company.finance.transactions') }}" class="bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-blue-500/20">
                VIEW_FULL_LEDGER
            </a>
        </div>
    </div>

    <!-- P&L Matrix -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Revenue Inflow -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 rounded-full group-hover:scale-110 transition-transform"></div>
            <div class="relative">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                        <i class="bi bi-graph-up-arrow text-sm"></i>
                    </div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Revenue_Inflow</h3>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter">AED {{ number_format($summary['income'], 2) }}</span>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <span class="text-[10px] font-black text-emerald-500 bg-emerald-500/10 px-2 py-0.5 rounded uppercase tracking-tighter">
                        {{ $summary['transaction_count_income'] }} MISSIONS_SETTLED
                    </span>
                </div>
            </div>
        </div>

        <!-- Expense Outflow -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-500/5 rounded-full group-hover:scale-110 transition-transform"></div>
            <div class="relative">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-500">
                        <i class="bi bi-graph-down-arrow text-sm"></i>
                    </div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Expense_Outflow</h3>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter">AED {{ number_format($summary['expense'], 2) }}</span>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <span class="text-[10px] font-black text-rose-500 bg-rose-500/10 px-2 py-0.5 rounded uppercase tracking-tighter">
                        {{ $summary['transaction_count_expense'] }} OPS_DRAIN_EVENTS
                    </span>
                </div>
            </div>
        </div>

        <!-- Net Yield -->
        <div class="bg-slate-900 dark:bg-blue-600 rounded-3xl border border-slate-800 dark:border-blue-500 p-8 shadow-xl shadow-slate-900/10 dark:shadow-blue-500/20 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/5 rounded-full group-hover:scale-110 transition-transform"></div>
            <div class="relative">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center text-white">
                        <i class="bi bi-activity text-sm"></i>
                    </div>
                    <h3 class="text-[10px] font-black text-white/50 uppercase tracking-widest">Current_Net_Yield</h3>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-white tracking-tighter">AED {{ number_format($summary['net_profit'], 2) }}</span>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <span class="text-[10px] font-black text-white/70 uppercase tracking-widest">
                        ALPHA_OPERATIONAL_MARGIN
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tactical Ledger Widget -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="px-8 py-6 border-b border-slate-50 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/30">
            <div>
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tactical_Ledger_Shorties</h3>
                <p class="text-[8px] font-bold text-slate-400 uppercase mt-0.5">Real-time Stream of Economic Events</p>
            </div>
            <a href="{{ route('company.finance.transactions') }}" class="text-[10px] font-black text-blue-500 uppercase tracking-widest hover:underline transition-all">
                FULL_CHRONICLE_HISTORY
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/30 dark:bg-slate-800/20">
                        <th class="px-8 py-4 text-[9px] font-black text-slate-400 uppercase tracking-tighter">Temporal_Stamp</th>
                        <th class="px-8 py-4 text-[9px] font-black text-slate-400 uppercase tracking-tighter">Event_Description</th>
                        <th class="px-8 py-4 text-[9px] font-black text-slate-400 uppercase tracking-tighter">Protocol_REF</th>
                        <th class="px-8 py-4 text-[9px] font-black text-slate-400 uppercase tracking-tighter text-right">Yield_Impact</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    @forelse($recentTransactions as $transaction)
                    @php
                    $isIncome = $transaction->journalEntries->contains(fn($je) => $je->account && $je->account->account_type === 'revenue' && $je->credit > 0);
                    $displayAmount = $transaction->journalEntries->max(fn($je) => max($je->debit, $je->credit));
                    @endphp
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                        <td class="px-8 py-4">
                            <div class="text-[10px] font-black text-slate-900 dark:text-white">{{ $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') : 'N/A' }}</div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('H:i') . ' Zulu' : '' }}</div>
                        </td>
                        <td class="px-8 py-4">
                            <div class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight group-hover:text-blue-500 transition-colors">{{ $transaction->description }}</div>
                        </td>
                        <td class="px-8 py-4">
                            @if($transaction->reference_type === 'App\Models\Rental')
                            <a href="{{ route('company.rentals.show', $transaction->reference_id) }}" class="inline-flex items-center gap-2 px-2 py-0.5 rounded-lg text-[9px] font-black bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 ring-1 ring-blue-100 dark:ring-blue-900/50 uppercase">
                                MISSION: #{{ $transaction->reference_id }}
                            </a>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[9px] font-black bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 ring-1 ring-slate-200 dark:ring-slate-700 uppercase">
                                {{ class_basename($transaction->reference_type) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-8 py-4 text-right">
                            <span class="text-sm font-black {{ $isIncome ? 'text-emerald-500' : 'text-rose-500' }} tracking-tighter font-mono">
                                {{ $isIncome ? '+' : '-' }}{{ number_format($displayAmount, 2) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-200 dark:text-slate-700 border-2 border-dashed border-slate-200 dark:border-slate-800">
                                    <i class="bi bi-wallet2 text-3xl"></i>
                                </div>
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ZERO_TRANSACTION_ACTIVITY_LOGGED</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection