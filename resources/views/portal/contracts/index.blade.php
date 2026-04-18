@extends('layouts.company')

@section('title', 'Fleet Contracts — Command')

@section('header_title')
<div class="flex items-center space-x-3">
    <div class="p-2 bg-slate-100 dark:bg-slate-800 text-slate-400 rounded-xl">
        <i class="bi bi-file-earmark-text-fill"></i>
    </div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Fleet Contracts Command</h1>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700 pb-20">

    {{-- Header Actions --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('company.contracts.create') }}" class="px-6 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-2xl text-[10px] font-black uppercase tracking-widest flex items-center space-x-2 shadow-2xl hover:scale-105 transition-all">
                <i class="bi bi-plus-lg"></i>
                <span>Configure New Contract</span>
            </a>
        </div>

        <form action="{{ route('company.contracts.index') }}" method="GET" class="flex items-center space-x-2">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="SEARCH PROTOCOL..." class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl px-5 py-3 pl-12 text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-900 dark:focus:ring-white transition-all outline-none shadow-sm">
                <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            </div>
            <select name="status" onchange="this.form.submit()" class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl px-5 py-3 text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-900 dark:focus:ring-white transition-all outline-none shadow-sm cursor-pointer">
                <option value="">ALL VECTORS</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>DRAFT</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>ACTIVE</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>EXPIRED</option>
                <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>TERMINATED</option>
            </select>
        </form>
    </div>

    {{-- Contracts Table --}}
    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-gray-50 dark:border-slate-800">
                        <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Contract #</th>
                        <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Asset / Agent</th>
                        <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Entity</th>
                        <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Temporal Span</th>
                        <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Fiscal Value</th>
                        <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Matrix</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
                    @forelse($contracts as $contract)
                    <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-8 py-6">
                            <p class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-widest">{{ $contract->contract_number }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tight mt-1">ID: {{ $contract->id }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-center text-slate-500">
                                    <i class="bi bi-bus-front-fill text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-slate-900 dark:text-white uppercase">{{ $contract->vehicle->vehicle_number }}</p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase truncate max-w-[120px]">{{ $contract->driver ? $contract->driver->name : 'SELF-DRIVE' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-[11px] font-black text-slate-900 dark:text-white uppercase">{{ $contract->customer->company_name ?: $contract->customer->name }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tight">{{ $contract->customer->customer_code }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-slate-900 dark:text-white uppercase">{{ $contract->start_date->format('d/m/y') }}</span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">TO {{ $contract->end_date->format('d/m/y') }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-[11px] font-black text-slate-900 dark:text-white uppercase">{{ number_format($contract->contract_value, 2) }} AED</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tight">{{ $contract->billing_cycle }} Engine</p>
                        </td>
                        <td class="px-8 py-6">
                            @php
                            $statusColors = [
                            'draft' => 'bg-slate-100 text-slate-600',
                            'active' => 'bg-emerald-100 text-emerald-600',
                            'expired' => 'bg-amber-100 text-amber-600',
                            'terminated' => 'bg-rose-100 text-rose-600',
                            ];
                            $color = $statusColors[$contract->status] ?? 'bg-slate-100 text-slate-600';
                            @endphp
                            <span class="px-3 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest {{ $color }}">
                                {{ $contract->status }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('company.contracts.show', $contract) }}" class="p-2 bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-lg transition-colors">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('company.contracts.edit', $contract) }}" class="p-2 bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-cyan-500 rounded-lg transition-colors">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-8 py-20 text-center">
                            <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800 rounded-3xl flex items-center justify-center mx-auto mb-6 text-slate-300">
                                <i class="bi bi-file-earmark-lock2-fill text-3xl"></i>
                            </div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest mb-1">Zero Deployment Vectors</h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">No contracts satisfy current search parameters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($contracts->hasPages())
        <div class="p-8 border-t border-gray-50 dark:border-slate-800">
            {{ $contracts->links() }}
        </div>
        @endif
    </div>

</div>
@endsection