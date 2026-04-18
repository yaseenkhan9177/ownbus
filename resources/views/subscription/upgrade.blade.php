@extends('layouts.company')

@section('title', 'Select Plan')
@section('header_title', 'Subscription Plans')

@section('content')
<div class="py-10">
    <div class="text-center mb-16">
        <h2 class="text-4xl font-black text-white tracking-tighter">Choose the perfect plan for your fleet</h2>
        <p class="text-slate-400 mt-4 text-lg">Scale your operations with advanced SaaS features</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-7xl mx-auto">
        @foreach($plans as $plan)
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 flex flex-col relative overflow-hidden group hover:border-blue-500/50 transition-all duration-300 {{ $currentPlan?->id === $plan->id ? 'ring-2 ring-blue-600' : '' }}">

            @if($currentPlan?->id === $plan->id)
            <div class="absolute top-0 right-0 bg-blue-600 text-white text-[10px] font-black px-4 py-1.5 rounded-bl-xl tracking-widest uppercase">
                Current Plan
            </div>
            @endif

            <div class="mb-8">
                <h3 class="text-2xl font-black text-white italic uppercase tracking-tight">{{ $plan->name }}</h3>
                <p class="text-slate-500 text-sm mt-1">{{ $plan->description ?? 'Ideal for growing transportation businesses.' }}</p>
            </div>

            <div class="mb-8">
                <div class="flex items-baseline gap-1">
                    <span class="text-4xl font-black text-white tracking-tighter">AED {{ number_format($plan->price_monthly, 0) }}</span>
                    <span class="text-slate-500 text-sm">/month</span>
                </div>
                <p class="text-[10px] font-bold text-blue-400 mt-1 uppercase tracking-widest">or AED {{ number_format($plan->price_yearly, 0) }} /year</p>
            </div>

            <ul class="space-y-4 mb-10 flex-1">
                @php
                    $features = is_array($plan->features) ? $plan->features : json_decode($plan->features, true) ?? [];
                @endphp
                @foreach($features as $feature)
                <li class="flex items-center gap-3 text-sm text-slate-300">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ $feature }}
                </li>
                @endforeach
            </ul>

            <form action="{{ route('subscription.checkout') }}" method="POST">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                <input type="hidden" name="billing_cycle" value="monthly">

                <button type="submit" class="w-full py-4 rounded-2xl text-sm font-black uppercase tracking-widest transition-all
                    {{ $currentPlan?->id === $plan->id ? 'bg-slate-800 text-slate-400 cursor-default' : 'bg-blue-600 hover:bg-blue-500 text-white shadow-lg shadow-blue-900/30' }}"
                    {{ $currentPlan?->id === $plan->id ? 'disabled' : '' }}>
                    {{ $currentPlan?->id === $plan->id ? 'Active Plan' : 'Select Plan' }}
                </button>
            </form>
        </div>
        @endforeach
    </div>

    <div class="mt-16 text-center text-slate-500 text-xs">
        <p>All plans include a 14-day free trial. Cancel anytime via the dashboard.</p>
        <div class="mt-4 flex items-center justify-center gap-8 opacity-50 grayscale">
            <span class="font-black text-lg italic tracking-tighter">VISA</span>
            <span class="font-black text-lg italic tracking-tighter">Mastercard</span>
            <span class="font-black text-lg italic tracking-tighter">Stripe</span>
        </div>
    </div>
</div>
@endsection
