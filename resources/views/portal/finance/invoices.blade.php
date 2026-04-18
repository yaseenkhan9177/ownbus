@extends('layouts.company')

@section('content')
<div class="space-y-6">
    <!-- Settlement Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white mt-1 uppercase tracking-tighter">SETTLEMENT_MONITOR</h1>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Accounts Receivable & Mission Invoicing</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('company.finance.dashboard') }}" class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                BACK_TO_HQ
            </a>
            <a href="{{ route('company.reports.export.invoices.pdf') }}" class="bg-rose-600 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-rose-500/20 flex items-center gap-2">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
            <a href="{{ route('company.reports.export.invoices.excel') }}" class="bg-emerald-600 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                <i class="bi bi-file-earmark-excel"></i> EXCEL
            </a>
        </div>
    </div>

    <!-- Settlement Grid -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-800/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-800">
                        <th class="px-8 py-4">Protocol_REF</th>
                        <th class="px-8 py-4">Entity_Alias</th>
                        <th class="px-8 py-4 text-center">Temporal_Settlement</th>
                        <th class="px-8 py-4 text-right">Mission_Valuation</th>
                        <th class="px-8 py-4 text-center">Settlement_State</th>
                        <th class="px-8 py-4 text-right">Execution</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    @forelse($invoices as $rental)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                        <td class="px-8 py-4">
                            <div class="text-[10px] font-black text-blue-500 font-mono">#{{ $rental->contract_number }}</div>
                            <div class="text-[8px] font-bold text-slate-400 uppercase mt-0.5 tracking-tighter">ID: {{ str_pad($rental->id, 6, '0', STR_PAD_LEFT) }}</div>
                        </td>
                        <td class="px-8 py-4">
                            <div class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $rental->customer->name }}</div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase flex items-center gap-1">
                                <i class="bi bi-building text-[8px]"></i>
                                {{ $rental->customer->company_name ?? 'INDIVIDUAL_CLIENT' }}
                            </div>
                        </td>
                        <td class="px-8 py-4 text-center">
                            <div class="text-[10px] font-black text-slate-900 dark:text-white">{{ $rental->end_date->format('d M Y') }}</div>
                            <div class="text-[8px] font-bold text-slate-400 uppercase">FINAL_PHASE_END</div>
                        </td>
                        <td class="px-8 py-4 text-right">
                            <div class="text-sm font-black text-slate-900 dark:text-white tracking-tighter font-mono">
                                AED {{ number_format($rental->final_amount, 2) }}
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            <div class="flex justify-center">
                                @php
                                $badges = [
                                'pending_payment' => 'bg-amber-500/10 text-amber-500 ring-amber-500/20',
                                'partially_paid' => 'bg-blue-500/10 text-blue-500 ring-blue-500/20',
                                'paid' => 'bg-emerald-500/10 text-emerald-500 ring-emerald-500/20',
                                'overdue' => 'bg-rose-500/10 text-rose-500 ring-rose-500/20',
                                ];
                                $badgeStyle = $badges[$rental->payment_status] ?? 'bg-slate-500/10 text-slate-500 ring-slate-500/20';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest ring-1 {{ $badgeStyle }}">
                                    {{ str_replace('_', ' ', $rental->payment_status) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-8 py-4 text-right">
                            @if($rental->payment_status !== 'paid')
                            <button class="inline-flex items-center gap-2 text-[10px] font-black text-emerald-500 hover:text-emerald-600 uppercase tracking-widest transition-all"
                                onclick="window.dispatchEvent(new CustomEvent('open-payment-protocol', { detail: { id: '{{ $rental->id }}', ref: '{{ $rental->contract_number }}', amount: '{{ (float)$rental->final_amount }}' } }))">
                                RECORD_SETTLEMENT
                                <i class="bi bi-cash-stack"></i>
                            </button>
                            @else
                            <span class="text-[9px] font-black text-slate-300 uppercase italic tracking-widest">SETTLED_ALPHA</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            ZERO_OUTSTANDING_INVOICES_DETECTED
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
        <div class="px-8 py-4 border-t border-slate-50 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/30">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Tactical Settlement Protocol (Modal) -->
<div x-data="{ open: false, rentalId: null, rentalRef: '', amount: 0 }"
    @open-payment-protocol.window="open = true; rentalId = $event.detail.id; rentalRef = $event.detail.ref; amount = $event.detail.amount"
    x-show="open"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
    x-cloak>

    <div @click.away="open = false"
        class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 w-full max-w-lg overflow-hidden shadow-2xl">

        <form :action="'{{ route('company.finance.payments.store', '__ID__') }}'.replace('__ID__', rentalId)" method="POST">
            @csrf

            <div class="px-8 py-6 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tighter">ECONOMIC_SETTLEMENT_PROTOCOL</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Executing Financial Settle for <span x-text="'#' + rentalRef" class="text-blue-500"></span></p>
            </div>

            <div class="p-8 space-y-6">
                <!-- Amount Input -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Settlement_Quantum (AED)</label>
                    <input type="number" step="0.01" name="amount" x-model="amount" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-6 py-4 text-2xl font-black text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 transition-all">
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Protocol_Date</label>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                            class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Settlement_Method</label>
                        <select name="payment_method" required
                            class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="cash">LEGAL_TENDER_CASH</option>
                            <option value="bank_transfer">WIRE_TRANSFER</option>
                            <option value="cheque">NEGOTIABLE_INSTRUMENT_CHEQUE</option>
                            <option value="online">DIGITAL_CLEARANCE</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Audit_Reference / RECEIPT_ID</label>
                    <input type="text" name="reference" placeholder="OPTIONAL_AUDIT_STRING..."
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
            </div>

            <div class="px-8 py-6 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-4">
                <button type="button" @click="open = false" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">
                    ABORT_PROTOCOL
                </button>
                <button type="submit" class="bg-emerald-500 text-white px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-500/20">
                    COMMIT_SETTLEMENT
                </button>
            </div>
        </form>
    </div>
</div>
@endsection