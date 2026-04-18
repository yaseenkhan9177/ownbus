@extends('layouts.company')

@section('title', 'Chart of Accounts')

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="p-2 bg-indigo-600 rounded-lg shadow-sm">
        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Chart of Accounts</h1>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-tight">Ledger Structure</h3>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-1">Hierarchical classification & management</p>
        </div>
        <div class="flex items-center space-x-3">
            <button class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-gray-300 px-4 py-2.5 rounded-xl text-sm font-bold flex items-center hover:bg-gray-50 dark:hover:bg-slate-700 transition-all">
                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export Ledger
            </button>
            <button class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl text-sm font-extrabold flex items-center hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20">
                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Account
            </button>
        </div>
    </div>

    <!-- COA Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                        <th class="py-4 pl-6 pr-4 text-[10px] font-black text-gray-500 uppercase tracking-widest">Account & Hierarchy</th>
                        <th class="py-4 px-4 text-[10px] font-black text-gray-500 uppercase tracking-widest">Ledger Code</th>
                        <th class="py-4 px-4 text-[10px] font-black text-gray-500 uppercase tracking-widest text-center">Classification</th>
                        <th class="py-4 px-4 text-[10px] font-black text-gray-500 uppercase tracking-widest text-center">Status</th>
                        <th class="py-4 pl-4 pr-6 text-[10px] font-black text-gray-500 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $account)
                    @include('portal.accounting.partials.coa_row', ['account' => $account, 'level' => 0])
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-6 bg-gray-50 dark:bg-slate-800/30 border-t border-gray-100 dark:border-slate-800">
            <div class="flex items-center text-xs text-gray-500 font-bold uppercase tracking-widest">
                <svg class="h-4 w-4 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                System protected accounts cannot be deleted to ensure structural integrity.
            </div>
        </div>
    </div>
</div>
@endsection