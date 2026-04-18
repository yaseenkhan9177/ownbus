@extends('layouts.company')

@section('title', 'Subscription Expired')

@section('content')
<div class="flex flex-col items-center justify-center py-20 text-center max-w-2xl mx-auto">
    <div class="w-24 h-24 rounded-full bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400 mb-8">
        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>

    <h2 class="text-4xl font-black text-white tracking-tighter mb-4 italic uppercase">Access Restricted</h2>
    <p class="text-slate-400 text-lg mb-10">Your subscription has expired or your vehicle quota has been exceeded. Please renew your plan to continue using the platform.</p>

    <div class="space-y-4 w-full">
        @forelse($plans as $plan)
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl flex items-center justify-between hover:border-blue-500/50 transition">
            <div class="text-left">
                <h4 class="text-lg font-black text-white italic uppercase tracking-tight">{{ $plan->name }}</h4>
                <p class="text-xs text-slate-500">AED {{ number_format($plan->price_monthly, 0) }}/mo</p>
            </div>
            <form action="{{ route('subscription.checkout') }}" method="POST">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                <input type="hidden" name="billing_cycle" value="monthly">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl transition">
                    Select
                </button>
            </form>
        </div>
        @empty
            <p class="text-slate-600">No plans available.</p>
        @endforelse
    </div>

    <div class="mt-10">
        <p class="text-xs text-slate-600">Need help? Contact our support team at support@example.com</p>
    </div>
</div>
@endsection
