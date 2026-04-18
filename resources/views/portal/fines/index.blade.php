@extends('layouts.company')

@section('title', 'Traffic Fines — Explorer')

@section('header_title')
<h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Compliance & Fines</h1>
@endsection

@section('content')
<div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm p-8 text-center">
    <div class="w-16 h-16 bg-amber-50 dark:bg-amber-900/20 rounded-2xl flex items-center justify-center mx-auto mb-4 text-amber-500">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
    </div>
    <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight mb-2">Fine Management Hub</h2>
    <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">Track traffic violations, authority fines, and safety compliance across the fleet.</p>
    <a href="{{ route('company.fines.create') }}" class="inline-flex items-center px-6 py-3 bg-amber-500 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-amber-600 transition-colors">
        Record New Fine
    </a>
</div>
@endsection