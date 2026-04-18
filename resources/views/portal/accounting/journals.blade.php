@extends('layouts.company')

@section('title', 'Journal Ledger')

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="p-2 bg-blue-600 rounded-lg shadow-sm">
        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Journal Ledger</h1>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-tight">Transaction Stream</h3>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-1">Chronological double-entry logs</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="relative">
                <button class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-gray-300 px-4 py-2.5 rounded-xl text-sm font-bold flex items-center hover:bg-gray-50 dark:hover:bg-slate-700 transition-all">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter Status
                </button>
            </div>
            <button class="bg-blue-600 text-white px-6 py-2.5 rounded-xl text-sm font-extrabold flex items-center hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Entry
            </button>
        </div>
    </div>

    <!-- Journals Stream -->
    <div class="space-y-4">
        @forelse($journals as $journal)
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden group">
            <!-- Journal Header -->
            <div class="px-6 py-4 bg-gray-50/50 dark:bg-slate-800/30 border-b border-gray-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="text-center min-w-[60px]">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ $journal->date->format('M') }}</p>
                        <p class="text-xl font-black text-gray-900 dark:text-white leading-none">{{ $journal->date->format('d') }}</p>
                        <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 mt-1">{{ $journal->date->format('Y') }}</p>
                    </div>
                    <div class="h-8 w-px bg-gray-200 dark:bg-slate-700"></div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-mono font-bold text-blue-600 dark:text-blue-400">#{{ str_pad($journal->id, 6, '0', STR_PAD_LEFT) }}</span>
                            <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-[9px] font-black rounded-full uppercase tracking-tighter">
                                {{ class_basename($journal->reference_type) }}
                            </span>
                        </div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white mt-0.5 line-clamp-1 italic opacity-80 decoration-slate-300">"{{ $journal->description }}"</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Post Status</p>
                        @if($journal->is_posted)
                        <span class="inline-flex items-center px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] font-black rounded-full uppercase">Posted</span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-[10px] font-black rounded-full uppercase">Draft</span>
                        @endif
                    </div>
                    <button class="p-2 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl text-gray-400 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Journal Lines -->
            <div class="p-0">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/30 dark:bg-slate-800/10 border-b border-gray-100 dark:border-slate-800 text-[9px] font-black text-gray-400 uppercase tracking-widest">
                        <tr>
                            <th class="py-2 pl-20 pr-4">Account Description</th>
                            <th class="py-2 px-4 text-right w-32">Debit (AED)</th>
                            <th class="py-2 pr-6 pl-4 text-right w-32">Credit (AED)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-slate-800/50">
                        @foreach($journal->lines as $line)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800/20 transition-colors">
                            <td class="py-3 pl-20 pr-4">
                                <div class="flex items-center">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $line->account->account_name }}</span>
                                        <span class="text-[10px] text-gray-500 font-mono">[{{ $line->account->account_code }}]</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-right text-sm font-mono font-black {{ $line->debit > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-300 dark:text-slate-700' }}">
                                {{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}
                            </td>
                            <td class="py-3 pr-6 pl-4 text-right text-sm font-mono font-black {{ $line->credit > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-300 dark:text-slate-700' }}">
                                {{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Journal Footer (Creator) -->
            <div class="px-6 py-3 bg-gray-50/20 dark:bg-slate-800/10 border-t border-gray-100 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                    <svg class="h-3 w-3 mr-2 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                    Author: {{ $journal->creator->name ?? 'System' }}
                </div>
                <div class="text-[10px] text-gray-400 font-mono">
                    Updated: {{ $journal->updated_at->diffForHumans() }}
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-12 shadow-sm border border-gray-100 dark:border-slate-800 text-center">
            <div class="w-20 h-20 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-lg font-black text-gray-900 dark:text-white">Empty Transaction stream</h3>
            <p class="text-sm text-gray-500 max-w-xs mx-auto mt-2">No journal entries have been recorded for the selected period.</p>
        </div>
        @endforelse

        @if($journals->hasPages())
        <div class="pt-6">
            {{ $journals->links() }}
        </div>
        @endif
    </div>
</div>
@endsection