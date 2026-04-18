@extends('layouts.company')

@section('title', 'Subscription Management')
@section('header_title', 'Billing & Subscription')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @if(!$subscription || !$subscription->isActive())
    <div class="bg-rose-500/10 border border-rose-500/30 rounded-2xl p-6 text-center">
        <h3 class="text-rose-400 font-black text-lg">Subscription Inactive</h3>
        <p class="text-slate-400 text-sm mt-1">Your access to premium features is currently restricted. Please upgrade to continue.</p>
        <a href="{{ route('subscription.upgrade') }}" class="mt-4 inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl transition shadow-lg shadow-blue-900/40">
            View Pricing Plans
        </a>
    </div>
    @endif

    {{-- Current Plan Card --}}
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <p class="text-xs font-black text-blue-400 uppercase tracking-widest mb-1">Current Plan</p>
                <h2 class="text-3xl font-black text-white uppercase tracking-tighter italic">
                    {{ $plan->name ?? 'None' }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    @if($subscription?->ends_at)
                        Renews on {{ $subscription->ends_at->format('d M Y') }}
                    @else
                        No active subscription found.
                    @endif
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('subscription.upgrade') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold rounded-xl transition border border-slate-700">
                    Change Plan
                </a>
                @if($subscription?->stripe_id)
                <button class="px-5 py-2.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-sm font-bold rounded-xl transition border border-rose-500/20">
                    Cancel
                </button>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 border-t border-slate-800">
            <div class="p-6 border-r border-slate-800">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Vehicle Quota</p>
                <div class="flex items-end justify-between mb-2">
                    <span class="text-2xl font-black text-white">{{ $quotaStatus['vehicles_used'] ?? 0 }} <span class="text-sm text-slate-500">/ {{ $quotaStatus['vehicle_limit'] ?? 10 }}</span></span>
                    <span class="text-xs font-bold text-slate-400">{{ round(($quotaStatus['vehicles_used'] / ($quotaStatus['vehicle_limit'] ?: 1)) * 100) }}%</span>
                </div>
                <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                    <div class="bg-blue-500 h-full transition-all" style="width: {{ min(100, ($quotaStatus['vehicles_used'] / ($quotaStatus['vehicle_limit'] ?: 1)) * 100) }}%"></div>
                </div>
            </div>
            <div class="p-6">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Billing Status</p>
                @if($subscription && $subscription->isActive())
                    <div class="flex items-center gap-2 text-emerald-400">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span class="text-sm font-bold">Active & Verified</span>
                    </div>
                @else
                    <div class="flex items-center gap-2 text-rose-400">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        <span class="text-sm font-bold">Action Required</span>
                    </div>
                @endif
                <p class="text-xs text-slate-500 mt-2">Next invoice amount: AED {{ number_format($subscription?->amount ?? 0, 2) }}</p>
            </div>
        </div>
    </div>

    {{-- Billing History --}}
    <div class="space-y-4">
        <h3 class="text-sm font-black text-white uppercase tracking-widest">Billing History</h3>
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-800/50 text-[10px] font-black text-slate-500 uppercase tracking-widest">
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Reference</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50">
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-xs text-slate-500">
                            No billing history records to display yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
