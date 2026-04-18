@extends('layouts.company')

@section('title', 'Bill Intelligence #' . $vendorBill->bill_number)

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="w-2 h-2 rounded-full bg-slate-500"></div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Bill Intelligence</h1>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-in fade-in slide-in-from-bottom-2 duration-500" x-data="{ showPaymentModal: false, showCancelModal: false }">

    {{-- 1. Status & Hero Bar --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="p-3 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl shadow-sm">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Bill Number</p>
                <p class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">#{{ $vendorBill->bill_number }}</p>
            </div>

            <div class="p-3 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl shadow-sm">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Status Vector</p>
                <div class="inline-flex items-center text-{{ $vendorBill->statusColor() }}-500 space-x-1.5 mt-0.5">
                    <div class="w-2 h-2 rounded-full bg-current {{ $vendorBill->status === 'draft' ? 'animate-pulse' : '' }}"></div>
                    <span class="text-sm font-black uppercase tracking-widest">{{ $vendorBill->status }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            @if($vendorBill->status === 'draft')
            <form action="{{ route('company.vendor-bills.approve', $vendorBill) }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-emerald-500/20 transition-all flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                    Validate & Post to Ledger
                </button>
            </form>
            @endif

            @if(in_array($vendorBill->status, ['approved', 'partially_paid']))
            <button @click="showPaymentModal = true" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-50 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-500/20 transition-all flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" />
                </svg>
                Liquidate Liability
            </button>
            @endif

            @if($vendorBill->status !== 'cancelled' && $vendorBill->status !== 'paid')
            <button @click="showCancelModal = true" class="px-4 py-2.5 text-xs font-black uppercase tracking-widest text-slate-400 hover:text-rose-500 transition-colors">
                Void Record
            </button>
            @endif

            @if(in_array($vendorBill->status, ['draft', 'cancelled']))
            <form action="{{ route('company.vendor-bills.destroy', $vendorBill) }}" method="POST" onsubmit="return confirm('Purge this record from registry?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2.5 text-xs font-black uppercase tracking-widest text-rose-500/50 hover:text-rose-600 transition-colors">
                    Purge Artifact
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- 2. Core Intel Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- Vendor Context --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Vendor Relationship</h3>
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 font-bold">
                    {{ substr($vendorBill->vendor->name, 0, 1) }}
                </div>
                <div>
                    <a href="{{ route('company.vendors.show', $vendorBill->vendor) }}" class="text-sm font-black text-slate-900 dark:text-white hover:text-blue-500 transition-colors uppercase tracking-tight">
                        {{ $vendorBill->vendor->name }}
                    </a>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">{{ $vendorBill->vendor->vendor_code }}</p>
                </div>
            </div>
        </div>

        {{-- Financial Metrics --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <div class="flex justify-between items-start mb-2">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Aggregate Amount</h3>
                @if($vendorBill->tax_amount > 0)
                <span class="text-[9px] font-bold text-slate-400 uppercase bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">Incl. AED {{ number_format($vendorBill->tax_amount, 2) }} VAT</span>
                @endif
            </div>
            <p class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">AED {{ number_format($vendorBill->total_amount, 2) }}</p>
            <div class="mt-2 w-full h-1 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                <div class="bg-emerald-500 h-full" style="width: 100%"></div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Liquidated (Paid)</h3>
            <p class="text-xl font-black text-blue-600 dark:text-blue-400 uppercase tracking-tighter">AED {{ number_format($paid, 2) }}</p>
            <div class="mt-2 w-full h-1 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                @php $paidPct = ($vendorBill->total_amount > 0) ? ($paid / $vendorBill->total_amount) * 100 : 0; @endphp
                <div class="bg-blue-500 h-full" style="width:{{ round($paidPct, 2) }}%"></div>
            </div>
        </div>

        <div class="bg-slate-900 dark:bg-slate-950 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Net Payable</h3>
            <p class="text-xl font-black text-white uppercase tracking-tighter">AED {{ number_format($remaining, 2) }}</p>
            <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-1 italic">
                {{ $remaining > 0 ? 'Residual Liability' : 'Fully Liquidated' }}
            </p>
        </div>
    </div>

    {{-- 3. Artifact Details & Journal --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Items Artifact --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800">
                    <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Expense Distribution Matrix</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left font-sans">
                        <thead>
                            <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                                <th class="py-3 px-6">Ledger Account</th>
                                <th class="py-3 px-4">Description</th>
                                <th class="py-3 px-4 text-center">Qty</th>
                                <th class="py-3 px-4 text-right">Unit</th>
                                <th class="py-3 px-6 text-right">Row Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-slate-800/50 text-xs font-bold text-slate-700 dark:text-slate-300">
                            @foreach($vendorBill->items as $item)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/20 transition-colors">
                                <td class="py-3 px-6">
                                    <div class="flex flex-col">
                                        <span class="text-slate-900 dark:text-white">{{ $item->expenseAccount->account_name }}</span>
                                        <span class="text-[9px] text-slate-400 uppercase">{{ $item->expenseAccount->account_code }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4">{{ $item->description }}</td>
                                <td class="py-3 px-4 text-center">{{ number_format($item->quantity, 1) }}</td>
                                <td class="py-3 px-4 text-right">{{ number_format($item->unit_cost, 2) }}</td>
                                <td class="py-3 px-6 text-right font-black text-slate-900 dark:text-white uppercase">AED {{ number_format($item->total_cost, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50/50 dark:bg-slate-800/50">
                                <td colspan="4" class="py-4 px-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Final Aggregate</td>
                                <td class="py-4 px-6 text-right text-sm font-black text-slate-900 dark:text-white uppercase tracking-tighter">AED {{ number_format($vendorBill->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Journal Proofs --}}
            @if($vendorBill->journalEntries->isNotEmpty())
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Financial Ledger Proofs (Double Entry)</h3>
                </div>
                <div class="p-6 space-y-6">
                    @foreach($vendorBill->journalEntries as $entry)
                    <div class="border border-slate-100 dark:border-slate-800 rounded-xl overflow-hidden">
                        <div class="px-4 py-2 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">JE-{{ $entry->id }} &bull; {{ $entry->entry_date->format('d M Y') }}</span>
                            <span class="text-[9px] font-bold text-slate-400 italic">"{{ $entry->description }}"</span>
                        </div>
                        <table class="w-full text-[10px] font-bold text-left">
                            <thead class="bg-slate-50/30 dark:bg-slate-800/20 text-slate-400 uppercase tracking-tighter">
                                <tr>
                                    <th class="py-2 px-4">Account Artifact</th>
                                    <th class="py-2 px-4 text-right">Debit</th>
                                    <th class="py-2 px-4 text-right">Credit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-800/30 text-slate-600 dark:text-slate-400">
                                @foreach($entry->lines as $line)
                                <tr>
                                    <td class="py-2 px-4">{{ $line->account->account_code }} - {{ $line->account->account_name }}</td>
                                    <td class="py-2 px-4 text-right {{ $line->debit > 0 ? 'text-emerald-600 font-black' : '' }}">{{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}</td>
                                    <td class="py-2 px-4 text-right {{ $line->credit > 0 ? 'text-rose-600 font-black' : '' }}">{{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Right: Technical Sidebar --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest mb-6">Traceability Metadata</h3>

                <div class="space-y-6">
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Maturity Check (Due Date)</p>
                        @if($vendorBill->due_date)
                        <p class="text-xs font-bold {{ $vendorBill->due_date->isPast() && $vendorBill->status !== 'paid' ? 'text-rose-500' : 'text-slate-700 dark:text-slate-300' }}">
                            {{ $vendorBill->due_date->format('F d, Y') }}
                            @if($vendorBill->due_date->isPast() && $vendorBill->status !== 'paid')
                            <span class="ml-1 text-[9px] font-black uppercase tracking-tighter">[EXPIRED]</span>
                            @endif
                        </p>
                        @else
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">IMMEDIATE PAYABLE</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Registry Artifacts</p>
                        <div class="space-y-2 mt-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tight">Created By</span>
                                <span class="text-[10px] font-black text-slate-900 dark:text-white">{{ $vendorBill->creator->name ?? 'System' }}</span>
                            </div>
                            @if($vendorBill->approver)
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tight">Approved By</span>
                                <span class="text-[10px] font-black text-emerald-500">{{ $vendorBill->approver->name }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($vendorBill->description)
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Internal Strategic Note</p>
                        <p class="text-[11px] font-bold text-slate-600 dark:text-slate-400 leading-relaxed italic border-l-2 border-slate-100 pl-3">"{{ $vendorBill->description }}"</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- PAYMENT MODAL --}}
    <div x-show="showPaymentModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm" x-cloak>
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 max-w-md w-full shadow-2xl border border-white/10" @click.away="showPaymentModal = false">
            <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tighter mb-2">Liquidate Liability</h2>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mb-8">Record financial outflow to vendor</p>

            <form action="{{ route('company.vendor-bills.pay', $vendorBill) }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Transaction Amount (AED)</label>
                    <input type="number" step="0.01" name="amount" value="{{ $remaining }}" max="{{ $remaining }}" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-4 py-4 text-xl font-black text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Outflow Vector (Method)</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative block cursor-pointer">
                            <input type="radio" name="payment_method" value="cash" checked class="sr-only peer">
                            <div class="p-4 bg-slate-50 dark:bg-slate-800 border-2 border-transparent rounded-2xl peer-checked:border-blue-500 peer-checked:bg-blue-500/10 transition-all text-center">
                                <span class="text-xs font-black uppercase tracking-widest text-slate-600 dark:text-slate-400 peer-checked:text-blue-500">Petty Cash</span>
                            </div>
                        </label>
                        <label class="relative block cursor-pointer">
                            <input type="radio" name="payment_method" value="bank" class="sr-only peer">
                            <div class="p-4 bg-slate-50 dark:bg-slate-800 border-2 border-transparent rounded-2xl peer-checked:border-blue-500 peer-checked:bg-blue-500/10 transition-all text-center">
                                <span class="text-xs font-black uppercase tracking-widest text-slate-600 dark:text-slate-400 peer-checked:text-blue-500">Bank Transfer</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex items-center space-x-4 pt-4">
                    <button type="button" @click="showPaymentModal = false" class="flex-1 px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">Abort</button>
                    <button type="submit" class="flex-2 px-6 py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-xl shadow-blue-500/20 transition-all font-inter">
                        Excute Payment
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- CANCEL MODAL --}}
    <div x-show="showCancelModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-rose-900/20 backdrop-blur-sm" x-cloak>
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 max-w-md w-full shadow-2xl border border-rose-500/20" @click.away="showCancelModal = false">
            <h2 class="text-xl font-black text-rose-500 uppercase tracking-tighter mb-2">Void Record</h2>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mb-8">Financial reversal will be generated</p>

            <form action="{{ route('company.vendor-bills.cancel', $vendorBill) }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Reason for Cancellation</label>
                    <textarea name="reason" rows="3" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-4 py-4 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500"
                        placeholder="State why this artifact is being voided..."></textarea>
                </div>

                <div class="flex items-center space-x-4 pt-4">
                    <button type="button" @click="showCancelModal = false" class="flex-1 px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">Abort</button>
                    <button type="submit" class="flex-2 px-6 py-4 bg-rose-500 hover:bg-rose-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-xl shadow-rose-500/20 transition-all">
                        Void Artifact
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection