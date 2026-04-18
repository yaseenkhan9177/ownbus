@extends('layouts.company')

@section('title', 'Expense Details — #' . $expense->id)

@section('header_title')
<div class="flex items-center gap-3">
    <a href="{{ route('company.expenses.index') }}" class="flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
    </a>
    <div>
        <h1 class="text-base font-black text-slate-900 dark:text-white tracking-tight uppercase leading-none">Expense Details</h1>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Reference: {{ $expense->reference_no ?? 'No Reference' }}</p>
    </div>
</div>
@endsection

@section('content')
@php
$lockService = app(\App\Services\DataLockService::class);
$isLocked = $lockService->isLocked($expense);
@endphp

<div class="space-y-6 mb-6">
    @if($isLocked)
    <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-900/30 rounded-2xl flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-amber-500 text-white rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/20">
                <i class="bi bi-lock-fill text-lg"></i>
            </div>
            <div>
                <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Enterprise Data Lock Active</h3>
                <p class="text-[10px] text-amber-600 dark:text-amber-400 font-bold uppercase">{{ $lockService->lockReason($expense) }}</p>
            </div>
        </div>
        @can('override_data_lock')
        <span class="text-[9px] font-black text-amber-600 bg-amber-100 dark:bg-amber-800/50 px-3 py-1 rounded-lg uppercase tracking-widest">
            <i class="bi bi-shield-check mr-1"></i> Privilege: Can Override
        </span>
        @endcan
    </div>
    @endif
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Main Info --}}
    <div class="xl:col-span-2 space-y-6">
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 flex justify-between items-center">
                <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Transaction Overview</h2>
                <span class="px-2 py-0.5 bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400 rounded text-[9px] font-black uppercase tracking-widest">{{ $expense->category }}</span>
            </div>
            <div class="p-6 grid grid-cols-2 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Expense Date</p>
                    <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $expense->expense_date }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Payment Method</p>
                    <p class="text-xs font-bold text-slate-900 dark:text-white uppercase">{{ $expense->payment_method }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Recorded By</p>
                    <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $expense->creator->name ?? 'System' }}</p>
                </div>
                <div class="col-span-2 md:col-span-3">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Description</p>
                    <p class="text-xs font-medium text-slate-600 dark:text-slate-400">{{ $expense->description }}</p>
                </div>
            </div>
        </div>

        {{-- Accounting Integration --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 flex items-center gap-2">
                <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Journal Entries</h2>
            </div>
            <div class="p-0">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50">
                            <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Account</th>
                            <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">Debit</th>
                            <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">Credit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($expense->journalEntries as $entry)
                        @foreach($entry->lines as $line)
                        <tr>
                            <td class="px-6 py-3">
                                <p class="text-[11px] font-bold text-slate-900 dark:text-white">{{ $line->account->name }}</p>
                                <p class="text-[9px] text-slate-400 font-medium uppercase tracking-widest">{{ $line->account->code }}</p>
                            </td>
                            <td class="px-6 py-3 text-[11px] font-black text-slate-900 dark:text-white text-right">
                                {{ $line->debit > 0 ? number_format($line->debit, 2) : '—' }}
                            </td>
                            <td class="px-6 py-3 text-[11px] font-black text-slate-900 dark:text-white text-right">
                                {{ $line->credit > 0 ? number_format($line->credit, 2) : '—' }}
                            </td>
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Sidebar Details --}}
    <div class="space-y-6">
        <div class="bg-slate-900 dark:bg-slate-800 rounded-2xl p-6 shadow-xl space-y-6">
            <h3 class="text-[10px] font-black text-white/50 uppercase tracking-widest">Financial Summary</h3>

            <div class="space-y-3">
                <div class="flex justify-between items-center pb-2 border-b border-white/5">
                    <span class="text-[10px] font-bold text-white/40 uppercase tracking-widest">Subtotal</span>
                    <span class="text-xs font-black text-white">{{ number_format($expense->amount_ex_vat, 2) }} AED</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b border-white/5">
                    <span class="text-[10px] font-bold text-white/40 uppercase tracking-widest">VAT ({{ $expense->vat_percent }}%)</span>
                    <span class="text-xs font-black text-white/70">{{ number_format($expense->vat_amount, 2) }} AED</span>
                </div>
                <div class="flex justify-between items-center pt-2">
                    <span class="text-[10px] font-black text-cyan-400 uppercase tracking-widest">Total Paid</span>
                    <span class="text-xl font-black text-cyan-400">{{ number_format($expense->total_amount, 2) }} AED</span>
                </div>
            </div>
        </div>

        @if($expense->vehicle)
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Associated Vehicle</h3>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l1 1h8l1-1z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $expense->vehicle->vehicle_number }}</p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $expense->vehicle->make }} {{ $expense->vehicle->model }}</p>
                </div>
            </div>
        </div>
        @endif

        @if($expense->invoice_path)
        <a href="{{ Storage::url($expense->invoice_path) }}" target="_blank" class="block w-full py-4 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-2xl text-center text-[10px] font-black uppercase tracking-widest transition-colors border border-slate-200 dark:border-slate-700">
            View Attachment
        </a>
        @endif
    </div>
</div>
@endsection