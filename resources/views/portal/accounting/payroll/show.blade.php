@extends('layouts.company')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('company.accounting.payroll.index') }}" class="p-2 bg-gray-100 dark:bg-slate-800 rounded-lg text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $batch->period_name }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Batch #PAY-{{ str_pad($batch->id, 6, '0', STR_PAD_LEFT) }} • {{ $batch->branch->name ?? 'All Branches' }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @if($batch->status === 'draft')
            <form action="{{ route('company.accounting.payroll.post', $batch->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to post this payroll to the General Ledger? This cannot be undone.');">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-md transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Post to Ledger
                </button>
            </form>
            @else
            <div class="px-4 py-2 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800 rounded-lg text-sm font-bold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Posted to Ledger
            </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-1">Total Gross Salary</p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">AED {{ number_format($batch->slips()->sum('base_salary'), 2) }}</h3>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700">
            <p class="text-xs uppercase tracking-wider text-rose-500 font-semibold mb-1">Total Deductions</p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">AED {{ number_format($batch->slips()->sum('total_deductions'), 2) }}</h3>
        </div>
        <div class="bg-indigo-600 p-6 rounded-xl shadow-md">
            <p class="text-xs uppercase tracking-wider text-indigo-100/70 font-semibold mb-1">Net Disbursement</p>
            <h3 class="text-2xl font-bold text-white">AED {{ number_format($batch->total_net, 2) }}</h3>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
        <div class="p-4 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between bg-gray-50/50 dark:bg-slate-800/50">
            <h3 class="font-semibold text-gray-900 dark:text-white uppercase tracking-wider text-xs">Salary Slips Breakdown</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-900/50 text-xs uppercase text-gray-500 dark:text-gray-400 font-semibold border-b border-gray-100 dark:border-slate-700">
                        <th class="px-6 py-4">Employee / Driver</th>
                        <th class="px-6 py-4 text-right">Base Salary</th>
                        <th class="px-6 py-4 text-right">Deductions</th>
                        <th class="px-6 py-4 text-right">Net Payable</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-700">
                    @foreach($batch->slips as $slip)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-slate-700 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold border border-gray-200 dark:border-slate-600">
                                    {{ strtoupper(substr($slip->user->name ?? 'D', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        {{ $slip->user->name ?? 'Driver #'.$slip->id }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $slip->user->email ?? 'Operational Staff' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-700 dark:text-gray-300 font-medium">
                            AED {{ number_format($slip->base_salary, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm {{ $slip->total_deductions > 0 ? 'text-rose-500 font-bold' : 'text-gray-400' }}">
                                - AED {{ number_format($slip->total_deductions, 2) }}
                            </span>
                            @if($slip->items->count() > 0)
                            <div class="text-[10px] text-gray-400 mt-0.5">
                                {{ $slip->items->count() }} items
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-base font-bold text-gray-900 dark:text-white">
                                AED {{ number_format($slip->net_salary, 2) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('company.accounting.payroll.slip', $slip->id) }}"
                                target="_blank"
                                class="inline-flex items-center px-3 py-1.5 bg-gray-50 hover:bg-indigo-50 text-indigo-600 text-xs font-bold rounded border border-indigo-100 hover:border-indigo-300 transition-all gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Print Slip
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection