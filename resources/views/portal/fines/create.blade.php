@extends('layouts.company')

@section('title', 'Record Fine — Create')

@section('header_title')
<h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Record Fine</h1>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm p-8">
        <h2 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Fine Details</h2>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-8 italic">Fine recording form is being finalized. This will automatically sync with the Expense module and Driver Risk Score.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 opacity-30 pointer-events-none">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Vehicle</label>
                <div class="h-10 bg-slate-100 dark:bg-slate-800 rounded-xl"></div>
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Authority</label>
                <div class="h-10 bg-slate-100 dark:bg-slate-800 rounded-xl"></div>
            </div>
        </div>
    </div>
</div>
@endsection