@extends('layouts.company')

@section('title', 'Expenses — Explorer')

@section('header_title')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Expense Intelligence</h1>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Fleet & Operations Costs</p>
    </div>
    <a href="{{ route('company.expenses.create') }}" class="inline-flex items-center px-6 py-3 bg-cyan-600 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-cyan-700 transition-all shadow-lg shadow-cyan-600/20">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
        </svg>
        Record Expense
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Filters --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <form action="{{ route('company.expenses.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Vehicle</label>
                <select name="vehicle_id" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                    <option value="">All Vehicles</option>
                    @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>{{ $vehicle->vehicle_number }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Category</label>
                <select name="category" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                    <option value="">All Categories</option>
                    <option value="fuel" {{ request('category') == 'fuel' ? 'selected' : '' }}>Fuel</option>
                    <option value="maintenance" {{ request('category') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="salaries" {{ request('category') == 'salaries' ? 'selected' : '' }}>Salaries</option>
                    <option value="fines" {{ request('category') == 'fines' ? 'selected' : '' }}>Fines</option>
                    <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold text-slate-900 dark:text-white">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full py-2.5 bg-slate-900 dark:bg-slate-800 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-800 transition-colors">
                    Apply Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Expense List --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Category</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Description</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Vehicle</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Amount</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($expenses as $expense)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-6 py-4 text-[11px] font-bold text-slate-900 dark:text-white">{{ $expense->expense_date }}</td>
                    <td class="px-6 py-4">
                        @php
                        $categoryStyles = [
                        'fuel' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
                        'maintenance' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',
                        'fines' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400',
                        'default' => 'bg-slate-50 text-slate-600 dark:bg-slate-700 dark:text-slate-300'
                        ];
                        $style = $categoryStyles[$expense->category] ?? $categoryStyles['default'];
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest {{ $style }}">
                            {{ $expense->category }}
                        </span>
                        @if(app(\App\Services\DataLockService::class)->isLocked($expense))
                        <div class="mt-1 flex items-center text-[8px] font-bold text-slate-400 uppercase tracking-tighter" title="{{ app(\App\Services\DataLockService::class)->lockReason($expense) }}">
                            <i class="bi bi-lock-fill mr-1"></i> Locked
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-[11px] font-medium text-slate-500 dark:text-slate-400">{{ Str::limit($expense->description, 40) }}</td>
                    <td class="px-6 py-4 text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-tight">
                        {{ $expense->vehicle ? $expense->vehicle->vehicle_number : '—' }}
                    </td>
                    <td class="px-6 py-4 text-[12px] font-black text-slate-900 dark:text-white text-right">
                        {{ number_format($expense->total_amount, 2) }} <span class="text-[9px] text-slate-400 ml-1">AED</span>
                    </td>
                    <td class="px-6 py-4 text-right flex items-center justify-end space-x-2">
                        <a href="{{ route('company.expenses.show', $expense) }}" class="text-[10px] font-black text-cyan-600 dark:text-cyan-400 uppercase tracking-widest hover:underline">View</a>

                        @php $isLocked = app(\App\Services\DataLockService::class)->isLocked($expense); @endphp
                        <a href="{{ $isLocked ? '#' : route('company.expenses.edit', $expense) }}"
                            class="text-[10px] font-black {{ $isLocked ? 'text-slate-300 cursor-not-allowed' : 'text-slate-400 hover:text-emerald-500' }} uppercase tracking-widest"
                            {!! $isLocked ? 'title="Locked: ' . app(\App\Services\DataLockService::class)->lockReason($expense) . '"' : '' !!}>
                            Edit
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center text-slate-400 mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">No expenses recorded yet.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($expenses->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
            {{ $expenses->links() }}
        </div>
        @endif
    </div>
</div>
@endsection