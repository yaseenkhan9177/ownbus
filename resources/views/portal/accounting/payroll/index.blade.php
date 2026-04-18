@extends('layouts.company')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Salary & Payroll Processing</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage monthly salary batches and generate employee slips.</p>
        </div>
        <div>
            <a href="{{ route('company.accounting.payroll.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Process New Payroll
            </a>
        </div>
    </div>

    <!-- Payroll Batches Table Sleevel -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
        <div class="p-4 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between bg-gray-50/50 dark:bg-slate-800/50">
            <h3 class="font-semibold text-gray-900 dark:text-white uppercase tracking-wider text-xs">Payroll History</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-900/50 text-xs uppercase text-gray-500 dark:text-gray-400 font-semibold">
                        <th class="px-6 py-4">Batch Period</th>
                        <th class="px-6 py-4">Branch</th>
                        <th class="px-6 py-4 text-right">Net Amount</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Created By</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($batches as $batch)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $batch->period_name }}</div>
                            <div class="text-xs text-gray-500">#PAY-{{ str_pad($batch->id, 6, '0', STR_PAD_LEFT) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600 dark:text-gray-300">
                                {{ $batch->branch->name ?? 'All Branches' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-bold text-gray-900 dark:text-white">
                                AED {{ number_format($batch->total_net, 2) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                            $statusClasses = [
                            'draft' => 'bg-gray-100 text-gray-600 dark:bg-gray-900/30 dark:text-gray-400',
                            'posted' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                            'paid' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                            ];
                            $cls = $statusClasses[$batch->status] ?? $statusClasses['draft'];
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $cls }}">
                                {{ ucfirst($batch->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900/40 border border-indigo-200 dark:border-indigo-800 flex items-center justify-center text-xs font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ strtoupper(substr($batch->creator->name ?? '?', 0, 1)) }}
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-300">{{ $batch->creator->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $batch->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('company.accounting.payroll.show', $batch->id) }}"
                                    class="p-2 text-gray-400 hover:text-indigo-600 dark:text-gray-500 dark:hover:text-indigo-400 transition-colors"
                                    title="View Details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center mb-4 text-gray-300 dark:text-gray-600 border border-dashed border-gray-200 dark:border-slate-600">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">No payroll batches found</h4>
                                <p class="text-xs text-gray-500 mt-1 max-w-[200px]">Start by processing your first payroll batch for this month.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($batches->hasPages())
        <div class="p-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50/30 dark:bg-slate-800/30">
            {{ $batches->links() }}
        </div>
        @endif
    </div>
</div>
@endsection