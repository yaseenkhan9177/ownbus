@extends('layouts.company')

@section('content')
<div class="space-y-6" x-data="{ openFilter: false }">
    <!-- Ledger Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white mt-1 uppercase tracking-tighter">TACTICAL_TRANSACTION_LEDGER</h1>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Atomic Economic Event Stream</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="openFilter = !openFilter" class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-200 dark:hover:bg-slate-700 transition-all flex items-center gap-2">
                <i class="bi bi-filter"></i>
                FILTER_PROTOCOLS
            </button>
            <a href="{{ route('company.finance.dashboard') }}" class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:scale-105 transition-all">
                BACK_TO_HQ
            </a>
        </div>
    </div>

    <!-- Triple-Entry Filter Stack -->
    <div x-show="openFilter" x-collapse
        class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm">
        <form action="{{ route('company.finance.transactions') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Search_Event</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="DESCRIPTION_OR_REF..."
                    class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Protocol_Type</label>
                <select name="reference_type" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    <option value="">ALL_PROTOCOLS</option>
                    <option value="App\Models\Rental" {{ request('reference_type') == 'App\Models\Rental' ? 'selected' : '' }}>MISSION_RENTAL</option>
                    <option value="App\Models\VehicleUnavailability" {{ request('reference_type') == 'App\Models\VehicleUnavailability' ? 'selected' : '' }}>MAINTENANCE_OPS</option>
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Temporal_Window_START</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                    EXECUTE_QUERY
                </button>
            </div>
        </form>
    </div>

    <!-- Atomic Event Grid -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-800/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-800">
                        <th class="px-8 py-4">Temporal_Stamp</th>
                        <th class="px-8 py-4">Event_Profile</th>
                        <th class="px-8 py-4">Reference_Link</th>
                        <th class="px-8 py-4">Accounting_Hits (Triple-Entry)</th>
                        <th class="px-8 py-4 text-right">Yield_Delta</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    @forelse($transactions as $transaction)
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
                            <div class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $transaction->description }}</div>
                            <div class="text-[9px] font-bold text-slate-400 mt-1 uppercase">ID: TXN_{{ str_pad($transaction->id, 8, '0', STR_PAD_LEFT) }}</div>
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
                        <td class="px-8 py-4">
                            <div class="flex flex-col gap-1">
                                @foreach($transaction->journalEntries as $entry)
                                <div class="flex items-center gap-2 text-[9px] font-bold">
                                    <span class="text-slate-400 uppercase tracking-tighter">{{ $entry->account->account_name ?? 'UNKNOWN' }}</span>
                                    <span class="flex-1 border-b border-dotted border-slate-200 dark:border-slate-700 mb-0.5"></span>
                                    @if($entry->debit > 0)
                                    <span class="text-rose-500 font-black">Dr {{ number_format($entry->debit, 2) }}</span>
                                    @else
                                    <span class="text-emerald-500 font-black">Cr {{ number_format($entry->credit, 2) }}</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-8 py-4 text-right">
                            <span class="text-sm font-black {{ $isIncome ? 'text-emerald-500' : 'text-rose-500' }} tracking-tighter font-mono">
                                {{ $isIncome ? '+' : '-' }}{{ number_format($displayAmount, 2) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            NO_ECONOMIC_EVENTS_FOUND_IN_SELECTED_Temporal_WINDOW
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="px-8 py-4 border-t border-slate-50 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/30">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection