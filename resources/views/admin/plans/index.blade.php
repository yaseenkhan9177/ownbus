@extends('layouts.super-admin')

@section('title', 'Subscription Plans | SaaS Admin')

@section('header_title')
<div class="flex flex-col md:flex-row md:items-center md:justify-between w-full">
    <h1 class="text-xl font-bold text-slate-100 uppercase tracking-widest text-shadow-[0_0_10px_rgba(255,255,255,0.3)]">
        Subscription Pricing Tiers
    </h1>

    <!-- Action buttons -->
    <div class="mt-4 md:mt-0 flex space-x-3">
        <a href="{{ route('admin.plans.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white text-sm font-medium rounded-lg shadow-[0_0_15px_rgba(147,51,234,0.4)] transition-all">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create New Plan
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">

    @if(session('error'))
    <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-4 rounded-lg flex items-center">
        <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    @if(session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-lg flex items-center">
        <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        @forelse($plans as $plan)
        <div class="relative bg-[#0f1524] rounded-2xl border border-slate-800 p-6 flex flex-col justify-between transition-all duration-300 hover:border-purple-500/30 hover:shadow-[0_0_20px_rgba(147,51,234,0.1)] group overflow-hidden">

            <!-- Inactive overlay mask -->
            @if(!$plan->is_active)
            <div class="absolute inset-0 bg-slate-900/60 z-10 backdrop-blur-[1px]"></div>
            @endif

            <div class="relative z-20">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-bold text-slate-100 group-hover:text-purple-400 transition-colors uppercase tracking-wider">{{ $plan->name }}</h3>
                    @if($plan->is_active)
                    <span class="px-2 py-1 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] uppercase font-bold tracking-wider shadow-[0_0_5px_rgba(16,185,129,0.2)]">Active</span>
                    @else
                    <span class="px-2 py-1 rounded bg-slate-800 text-slate-500 text-[10px] uppercase font-bold tracking-wider">Draft</span>
                    @endif
                </div>

                <div class="mb-6 pb-6 border-b border-slate-800/80">
                    <div class="flex items-baseline">
                        <span class="text-3xl font-bold text-white">${{ number_format($plan->price_monthly, 0) }}</span>
                        <span class="text-slate-500 ml-2 text-sm font-medium">/ month</span>
                    </div>
                </div>

                <div class="space-y-4 mb-8">
                    <!-- Vehicles Limit -->
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-purple-500 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-sm text-slate-300">
                            @if(isset($plan->features['max_vehicles']) && $plan->features['max_vehicles'] == 99999)
                            Unlimited Fleet Capacity
                            @else
                            <span class="font-bold text-cyan-400">{{ $plan->features['max_vehicles'] ?? 0 }}</span> Maximum Vehicles
                            @endif
                        </span>
                    </div>

                    <!-- Users Limit -->
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-purple-500 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-sm text-slate-300">
                            @if(isset($plan->features['max_users']) && $plan->features['max_users'] == 99999)
                            Unlimited User Accounts
                            @else
                            <span class="font-bold text-cyan-400">{{ $plan->features['max_users'] ?? 0 }}</span> Staff / Drivers
                            @endif
                        </span>
                    </div>

                    <!-- Trial Data -->
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-purple-500 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-sm text-slate-300">
                            {{ $plan->trial_days }} Days Free Trial Included
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Footer -->
            <div class="relative z-20 flex items-center justify-between border-t border-slate-800 pt-4">
                <div class="text-xs font-mono text-slate-500">
                    <span class="text-emerald-400 font-bold">{{ $plan->subscriptions_count }}</span> Subscriptions
                </div>

                <div class="flex space-x-2">
                    <a href="{{ route('admin.plans.edit', $plan->id) }}" class="p-2 bg-slate-800 hover:bg-slate-700 text-cyan-400 transition-colors rounded-md" title="Configure Plan">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </a>

                    @if($plan->subscriptions_count === 0)
                    <form method="POST" action="{{ route('admin.plans.destroy', $plan->id) }}" class="inline" onsubmit="return confirm('Delete this plan permanently?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 bg-slate-800 hover:bg-rose-900/50 text-rose-500 transition-colors rounded-md" title="Delete Plan Structure">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                    @endif
                </div>
            </div>

        </div>
        @empty
        <div class="col-span-full bg-[#0f1524] p-12 rounded-xl border border-slate-800 shadow-lg text-center">
            <svg class="h-16 w-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <h3 class="text-xl font-bold text-slate-300 mb-2">No Plans Configured</h3>
            <p class="text-slate-500 mb-6">Create your first subscription tier to allow companies to onboard onto the platform safely.</p>
            <a href="{{ route('admin.plans.create') }}" class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-500 text-white font-medium rounded-lg shadow-[0_0_15px_rgba(147,51,234,0.4)] transition-all uppercase tracking-wider text-sm">
                Create Baseline Plan
            </a>
        </div>
        @endforelse
    </div>

</div>
@endsection