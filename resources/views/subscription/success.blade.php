@extends('layouts.company')

@section('title', 'Payment Successful')

@section('content')
<div class="flex flex-col items-center justify-center py-20 text-center max-w-2xl mx-auto">
    <div class="w-24 h-24 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 mb-8 animate-bounce">
        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h2 class="text-4xl font-black text-white tracking-tighter mb-4 italic uppercase">Payment Successful!</h2>
    <p class="text-slate-400 text-lg mb-10">Your subscription has been activated. You now have full access to all premium fleet management tools.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full mb-10">
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl text-left">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Receipt Email</p>
            <p class="text-sm font-bold text-white">{{ auth()->user()->email }}</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl text-left">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Status</p>
            <p class="text-sm font-bold text-emerald-400 flex items-center gap-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                Active
            </p>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-4 w-full">
        <a href="{{ route('company.dashboard') }}" class="flex-1 py-4 bg-blue-600 hover:bg-blue-500 text-white font-black uppercase tracking-widest rounded-2xl transition shadow-lg shadow-blue-900/30">
            Go to Dashboard
        </a>
        <a href="{{ route('subscription.show') }}" class="flex-1 py-4 bg-slate-800 hover:bg-slate-700 text-white font-black uppercase tracking-widest rounded-2xl transition border border-slate-700">
            View Billing
        </a>
    </div>
</div>
@endsection
