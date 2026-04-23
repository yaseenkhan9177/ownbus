@extends('layouts.company')

@section('title', 'Subscription Management')
@section('header_title', 'Billing & Subscription')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">
    @php 
        $company = auth()->user()->company;
        $status = $company->subscription_status;
        $badgeColor = $company->subscription_badge_color;
        $isTrial = $status === 'trial';
        
        // Safety fallbacks for dates
        $endDate = $isTrial 
            ? ($company->trial_ends_at ?? now()->addDays(7)) 
            : ($company->subscription->current_period_end ?? now()->addMonth());
            
        $startDate = $isTrial 
            ? ($company->created_at ?? now()->subDays(7)) 
            : ($company->subscription->current_period_start ?? $company->created_at ?? now()->subMonth());

        $totalDays = max(1, (int) $startDate->diffInDays($endDate, false));
        $daysUsed = max(0, (int) $startDate->diffInDays(now(), false));
        $percentUsed = min(100, round(($daysUsed / $totalDays) * 100));
    @endphp

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 stagger-1">
        <div>
            <h2 class="text-4xl font-black text-white uppercase tracking-tighter italic bebas">Your Subscription</h2>
            <p class="text-slate-500 mt-1 uppercase tracking-widest text-[10px] font-bold">Manage your plan and billing details</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('subscription.upgrade') }}" class="px-6 py-3 bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-cyan-900/20 flex items-center gap-2">
                <span>🚀</span> Upgrade Plan
            </a>
            @if(env('OWNER_WHATSAPP'))
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', env('OWNER_WHATSAPP')) }}" target="_blank" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all border border-slate-700 flex items-center gap-2">
                <span>💬</span> Contact Support
            </a>
            @endif
        </div>
    </div>

    {{-- Main Subscription Card --}}
    <div class="bg-[#111827] border border-slate-800 rounded-3xl overflow-hidden shadow-2xl stagger-2">
        <div class="grid md:grid-cols-2">
            <!-- Left Side: Details -->
            <div class="p-8 md:p-10 border-b md:border-b-0 md:border-r border-slate-800">
                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Current Plan</p>
                        <div class="flex items-center gap-3">
                            <h3 class="text-3xl font-black text-white uppercase tracking-tight">{{ $company->subscription_label }}</h3>
                            @if($isTrial)
                            <span class="px-2 py-1 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded text-[10px] font-black uppercase tracking-widest">Trial</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Status</p>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full 
                                    {{ $badgeColor === 'red' ? 'bg-rose-500' : '' }}
                                    {{ $badgeColor === 'orange' ? 'bg-amber-600' : '' }}
                                    {{ $badgeColor === 'yellow' ? 'bg-yellow-500' : '' }}
                                    {{ $badgeColor === 'green' ? 'bg-emerald-500' : '' }}
                                    {{ $badgeColor === 'slate' ? 'bg-slate-500' : '' }}
                                "></span>
                                <span class="text-sm font-bold text-slate-200 uppercase">{{ $status }}</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Started</p>
                            <p class="text-sm font-bold text-slate-200">{{ $startDate ? $startDate->format('d M Y') : 'N/A' }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Expires / Renews</p>
                        <p class="text-sm font-bold text-slate-200">{{ $endDate ? $endDate->format('d M Y') : 'N/A' }}</p>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="pt-4">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Usage Progress</p>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $percentUsed }}% Used</p>
                        </div>
                        <div class="w-full bg-slate-800 h-3 rounded-full overflow-hidden border border-white/5 p-0.5">
                            <div class="h-full rounded-full transition-all duration-1000
                                {{ $badgeColor === 'red' ? 'bg-rose-500' : ($badgeColor === 'orange' ? 'bg-amber-500' : ($badgeColor === 'yellow' ? 'bg-yellow-500' : 'bg-emerald-500')) }}
                            " style="width: {{ $percentUsed }}%"></div>
                        </div>
                        <p class="text-[9px] text-slate-600 mt-2 italic">{{ $daysUsed }} of {{ $totalDays }} days consumed</p>
                    </div>
                </div>
            </div>

            <!-- Right Side: Countdown -->
            <div class="p-8 md:p-10 bg-slate-900/50 flex flex-col items-center justify-center text-center">
                <div class="mb-6">
                    <span class="text-2xl">⏱️</span>
                    <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-2">Time Remaining</h4>
                </div>

                <div x-data="countdown('{{ $endDate->toIso8601String() }}')" x-init="start()" class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-slate-800 border border-slate-700 rounded-2xl flex items-center justify-center text-2xl font-black text-white shadow-inner mb-2" x-text="days">0</div>
                        <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Days</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-slate-800 border border-slate-700 rounded-2xl flex items-center justify-center text-2xl font-black text-white shadow-inner mb-2" x-text="hours">0</div>
                        <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Hrs</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-slate-800 border border-slate-700 rounded-2xl flex items-center justify-center text-2xl font-black text-white shadow-inner mb-2" x-text="minutes">0</div>
                        <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Mins</span>
                    </div>
                </div>

                <div class="mt-10 w-full">
                    <div class="p-4 bg-slate-800/40 rounded-2xl border border-slate-700/50">
                        <p class="text-[10px] text-slate-400 font-medium">Need more time or features? Contact our team for immediate activation.</p>
                        <p class="text-sm font-black text-cyan-400 mt-2">{{ env('OWNER_WHATSAPP', '+923409172223') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quota Section --}}
    <div class="grid md:grid-cols-2 gap-6 stagger-3">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Vehicle Quota</p>
            <div class="flex items-end justify-between mb-2">
                <span class="text-2xl font-black text-white">{{ $quotaStatus['vehicles_used'] ?? 0 }} <span class="text-sm text-slate-600">/ {{ $quotaStatus['vehicle_limit'] ?? 10 }}</span></span>
                <span class="text-xs font-bold text-slate-400">{{ round(($quotaStatus['vehicles_used'] / ($quotaStatus['vehicle_limit'] ?: 1)) * 100) }}%</span>
            </div>
            <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                <div class="bg-blue-500 h-full transition-all" style="width: {{ min(100, ($quotaStatus['vehicles_used'] / ($quotaStatus['vehicle_limit'] ?: 1)) * 100) }}%"></div>
            </div>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">User Quota</p>
            <div class="flex items-end justify-between mb-2">
                <span class="text-2xl font-black text-white">{{ $quotaStatus['users_used'] ?? 0 }} <span class="text-sm text-slate-600">/ {{ $quotaStatus['user_limit'] ?? 5 }}</span></span>
                <span class="text-xs font-bold text-slate-400">{{ round(($quotaStatus['users_used'] / ($quotaStatus['user_limit'] ?: 1)) * 100) }}%</span>
            </div>
            <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                <div class="bg-purple-500 h-full transition-all" style="width: {{ min(100, ($quotaStatus['users_used'] / ($quotaStatus['user_limit'] ?: 1)) * 100) }}%"></div>
            </div>
        </div>
    </div>
</div>

<script>
function countdown(endDate) {
    return {
        days: '00', hours: '00', 
        minutes: '00', seconds: '00',
        start() {
            const update = () => {
                const end = new Date(endDate);
                const now = new Date();
                const diff = end - now;
                
                if (diff <= 0) {
                    this.days = this.hours = 
                    this.minutes = this.seconds = '00';
                    return;
                }
                
                const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const s = Math.floor((diff % (1000 * 60)) / 1000);

                this.days = d.toString().padStart(2, '0');
                this.hours = h.toString().padStart(2, '0');
                this.minutes = m.toString().padStart(2, '0');
                this.seconds = s.toString().padStart(2, '0');
            };
            update();
            setInterval(update, 1000);
        }
    }
}
</script>
@endsection
