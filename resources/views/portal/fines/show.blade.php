@extends('layouts.company')

@section('title', 'Fine Details — ' . $fine->fine_number)

@section('header_title')
<div class="flex items-center space-x-3">
    <a href="{{ route('company.fines.index') }}" class="p-2 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl text-slate-400 hover:text-amber-500 transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <h1 class="text-lg font-bold text-gray-900 dark:text-white tracking-tight uppercase">Fine Details</h1>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Ticket #{{ $fine->fine_number }}</span>
            @php
                $statusColors = [
                    'unpaid'           => 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
                    'paid'             => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
                    'under-processing' => 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
                    'appealed'         => 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
                    'cancelled'        => 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400',
                ];
                $sc = $statusColors[$fine->status] ?? $statusColors['unpaid'];
            @endphp
            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $sc }}">
                {{ strtoupper($fine->status) }}
            </span>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Vehicle</p>
                    <p class="text-sm font-black text-slate-900 dark:text-white">{{ $fine->vehicle?->vehicle_number ?? '—' }}</p>
                    @if($fine->vehicle?->plate_number)
                    <p class="font-mono text-xs text-slate-500 dark:text-slate-400">{{ $fine->vehicle->plate_number }}</p>
                    @endif
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Authority</p>
                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $fine->authority ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Violation Type</p>
                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $fine->fine_type ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Responsibility</p>
                    <p class="text-sm font-bold text-slate-900 dark:text-white capitalize">{{ $fine->responsible_type ?? '—' }}</p>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Penalty Amount</p>
                    <p class="text-2xl font-black {{ $fine->status === 'paid' ? 'text-emerald-500' : 'text-red-500' }}">AED {{ number_format($fine->amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Fine Date</p>
                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $fine->fine_date?->format('d M Y') ?? '—' }}</p>
                </div>
                @if($fine->due_date)
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Due Date</p>
                    <p class="text-sm font-bold {{ $fine->due_date->isPast() && $fine->status === 'unpaid' ? 'text-red-500' : 'text-slate-900 dark:text-white' }}">
                        {{ $fine->due_date->format('d M Y') }}
                        @if($fine->due_date->isPast() && $fine->status === 'unpaid')
                        <span class="text-[9px] font-black text-red-500 ml-1">OVERDUE</span>
                        @endif
                    </p>
                </div>
                @endif
                @if($fine->paid_at)
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Paid At</p>
                    <p class="text-sm font-bold text-emerald-500">{{ $fine->paid_at->format('d M Y H:i') }}</p>
                </div>
                @endif
            </div>

            @if($fine->description)
            <div class="md:col-span-2">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Notes</p>
                <p class="text-sm text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4">{{ $fine->description }}</p>
            </div>
            @endif
        </div>

        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/30 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3">
            <a href="{{ route('company.fines.index') }}"
               class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                ← Back to Fines
            </a>
            <div class="flex items-center space-x-3">
                @if($fine->status === 'unpaid')
                <form method="POST" action="{{ route('company.fines.checker.paid', $fine) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black uppercase rounded-xl transition-all">
                        Mark as Paid
                    </button>
                </form>
                <form method="POST" action="{{ route('company.fines.checker.dispute', $fine) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white text-xs font-black uppercase rounded-xl transition-all">
                        Dispute
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
