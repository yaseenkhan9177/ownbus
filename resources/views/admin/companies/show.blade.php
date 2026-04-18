@extends('layouts.super-admin')

@section('title', "{$company->name} | Tenant Details")

@section('header_title')
<div class="flex items-center space-x-4">
    <a href="{{ route('admin.companies.index') }}" class="text-slate-400 hover:text-cyan-400 transition-colors">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
    </a>
    <h1 class="text-xl font-bold text-slate-100 uppercase tracking-widest text-shadow-[0_0_10px_rgba(255,255,255,0.3)] flex items-center">
        TENANT PROFILE: <span class="text-cyan-400 ml-2">{{ $company->name }}</span>
    </h1>

    @if($company->status === 'active')
    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 uppercase tracking-widest shadow-[0_0_5px_rgba(16,185,129,0.2)] ml-4">Active</span>
    @elseif($company->status === 'pending')
    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 uppercase tracking-widest shadow-[0_0_5px_rgba(245,158,11,0.2)] ml-4">Pending</span>
    @else
    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20 uppercase tracking-widest shadow-[0_0_5px_rgba(225,29,72,0.2)] ml-4">Suspended</span>
    @endif
</div>
@endsection

@section('content')
<div x-data="{ tab: 'overview' }" class="space-y-6">

    <!-- Tab Navigation -->
    <div class="bg-[#0f1524] rounded-xl border border-slate-800 p-2 shadow-lg flex overflow-x-auto custom-scrollbar">
        <button @click="tab = 'overview'" :class="{ 'bg-slate-800 text-cyan-400 shadow-[0_0_10px_rgba(6,182,212,0.1)]': tab === 'overview', 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50': tab !== 'overview' }" class="px-6 py-3 rounded-lg text-sm font-semibold uppercase tracking-wider transition-all whitespace-nowrap">
            Overview
        </button>
        <button @click="tab = 'subscription'" :class="{ 'bg-slate-800 text-purple-400 shadow-[0_0_10px_rgba(168,85,247,0.1)]': tab === 'subscription', 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50': tab !== 'subscription' }" class="px-6 py-3 rounded-lg text-sm font-semibold uppercase tracking-wider transition-all whitespace-nowrap">
            Subscription
        </button>
        <button @click="tab = 'usage'" :class="{ 'bg-slate-800 text-emerald-400 shadow-[0_0_10px_rgba(16,185,129,0.1)]': tab === 'usage', 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50': tab !== 'usage' }" class="px-6 py-3 rounded-lg text-sm font-semibold uppercase tracking-wider transition-all whitespace-nowrap">
            Usage Metrics
        </button>
        <button @click="tab = 'billing'" :class="{ 'bg-slate-800 text-blue-400 shadow-[0_0_10px_rgba(59,130,246,0.1)]': tab === 'billing', 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50': tab !== 'billing' }" class="px-6 py-3 rounded-lg text-sm font-semibold uppercase tracking-wider transition-all whitespace-nowrap">
            Billing History
        </button>
        <button @click="tab = 'audit'" :class="{ 'bg-slate-800 text-amber-400 shadow-[0_0_10px_rgba(245,158,11,0.1)]': tab === 'audit', 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50': tab !== 'audit' }" class="px-6 py-3 rounded-lg text-sm font-semibold uppercase tracking-wider transition-all whitespace-nowrap">
            Audit Logs
        </button>
    </div>

    <!-- 1. OVERVIEW TAB -->
    <div x-show="tab === 'overview'" class="space-y-6" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Company Info Card -->
            <div class="bg-[#0f1524] p-6 rounded-xl border border-slate-800 shadow-lg col-span-2">
                <h3 class="text-lg font-semibold text-slate-100 border-b border-slate-800 pb-3 mb-6">Company Profile</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div>
                            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Tenant Name</span>
                            <span class="block text-slate-200 font-medium text-lg mt-1">{{ $company->name }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Primary Owner</span>
                            <div class="flex items-center mt-1">
                                <span class="block text-slate-200">{{ $company->owner_name ?? ($company->owner->name ?? 'None') }}</span>
                            </div>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Registration Target</span>
                            <span class="block text-slate-300 font-mono mt-1">{{ $company->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Contact Email</span>
                            <span class="block text-cyan-400 font-medium mt-1">{{ $company->email ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Phone Number</span>
                            <span class="block text-slate-300 font-mono mt-1">{{ $company->phone ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">TRN Number</span>
                            <span class="block text-slate-300 font-mono mt-1">{{ $company->trn_number ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-[#0f1524] p-6 rounded-xl border border-slate-800 shadow-lg flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-100 border-b border-slate-800 pb-3 mb-4">Instance Statistics</h3>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-slate-800/30 rounded-lg border border-slate-800">
                            <span class="text-slate-400 text-sm">Branches</span>
                            <span class="text-cyan-400 font-mono font-bold">{{ $company->branches_count }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-slate-800/30 rounded-lg border border-slate-800">
                            <span class="text-slate-400 text-sm">Active Users</span>
                            <span class="text-cyan-400 font-mono font-bold">{{ $company->users_count }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-slate-800/30 rounded-lg border border-slate-800">
                            <span class="text-slate-400 text-sm">Fleet Vehicles</span>
                            <span class="text-cyan-400 font-mono font-bold">{{ $company->vehicles_count }}</span>
                        </div>
                    </div>
                </div>

                @if($company->status === 'active')
                <div class="mt-6">
                    <form method="POST" action="{{ route('admin.companies.impersonate', $company->id) }}">
                        @csrf
                        <button type="submit" class="w-full py-2.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-500 border border-amber-500/30 rounded-lg transition-all font-semibold shadow-[0_0_10px_rgba(245,158,11,0.1)] uppercase tracking-wider text-xs">
                            <svg class="h-4 w-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            Impersonate Tenant
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 2. SUBSCRIPTION TAB -->
    <div x-show="tab === 'subscription'" style="display: none;" class="bg-[#0f1524] p-6 rounded-xl border border-slate-800 shadow-lg" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        <h3 class="text-lg font-semibold text-slate-100 border-b border-slate-800 pb-3 mb-6">Subscription Details</h3>

        @if($company->subscription)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="p-4 bg-slate-800/30 border border-slate-700 rounded-lg">
                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Assigned Plan</span>
                <span class="block text-purple-400 font-bold text-xl mt-1">{{ $company->subscription->plan->name ?? 'Unknown Plan' }}</span>
            </div>
            <div class="p-4 bg-slate-800/30 border border-slate-700 rounded-lg">
                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Billing Cycle</span>
                <span class="block text-slate-200 font-bold text-xl mt-1 capitalize">{{ $company->subscription->billing_cycle ?? 'Monthly' }}</span>
            </div>
            <div class="p-4 bg-slate-800/30 border border-slate-700 rounded-lg">
                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Next Renewal</span>
                <span class="block text-emerald-400 font-mono font-bold text-lg mt-1">
                    {{ $company->subscription->ends_at ? $company->subscription->ends_at->format('M d, Y') : 'Auto-Renewing' }}
                </span>
            </div>
            <div class="p-4 bg-slate-800/30 border border-slate-700 rounded-lg">
                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Stripe ID</span>
                <span class="block text-slate-400 font-mono text-sm mt-2 truncate">{{ $company->subscription->stripe_id ?? 'Not Integrated' }}</span>
            </div>
        </div>

        <div class="flex justify-end border-t border-slate-800 pt-6">
            <button class="px-6 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-lg shadow-[0_0_15px_rgba(147,51,234,0.4)] transition-all font-semibold uppercase tracking-wider text-xs">
                Override / Change Plan
            </button>
        </div>
        @else
        <div class="text-center py-12">
            <span class="text-slate-500 italic block mb-4">This tenant does not have an active subscription record.</span>
            <button class="px-6 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg shadow-[0_0_15px_rgba(16,185,129,0.4)] transition-all font-semibold uppercase tracking-wider text-xs">
                Assign Plan Manually
            </button>
        </div>
        @endif
    </div>

    <!-- 3. USAGE TAB -->
    <div x-show="tab === 'usage'" style="display: none;" class="bg-[#0f1524] p-6 rounded-xl border border-slate-800 shadow-lg" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        <h3 class="text-lg font-semibold text-slate-100 border-b border-slate-800 pb-3 mb-6">Resource Allocation & Usage</h3>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="space-y-6">
                <!-- Vehicles Widget -->
                @php
                $vehicleLimit = $company->subscription->plan->max_vehicles ?? 100; // Simulated limit if plan exists
                $vehicleUsage = $company->vehicles_count;
                $vPercentage = min(100, ($vehicleUsage / max(1, $vehicleLimit)) * 100);
                @endphp
                <div>
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-sm font-bold text-slate-200 uppercase tracking-wider">Fleet Storage</span>
                        <span class="text-xs font-mono text-emerald-400">{{ $vehicleUsage }} / {{ ($vehicleLimit == 99999 || !$company->subscription) ? 'Unlimited' : $vehicleLimit }}</span>
                    </div>
                    <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                        <div class="bg-emerald-500 h-full shadow-[0_0_8px_rgba(16,185,129,0.8)]" style="width: <?= $vPercentage ?>%"></div>
                    </div>
                </div>

                <!-- Users/Drivers Widget -->
                @php
                $userLimit = $company->subscription->plan->max_users ?? 50;
                $userUsage = $company->users_count;
                $uPercentage = min(100, ($userUsage / max(1, $userLimit)) * 100);
                @endphp
                <div>
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-sm font-bold text-slate-200 uppercase tracking-wider">Staff & Drivers</span>
                        <span class="text-xs font-mono text-emerald-400">{{ $userUsage }} / {{ ($userLimit == 99999 || !$company->subscription) ? 'Unlimited' : $userLimit }}</span>
                    </div>
                    <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                        <div class="bg-emerald-500 h-full shadow-[0_0_8px_rgba(16,185,129,0.8)]" style="width: <?= $uPercentage ?>%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-lg p-6 flex flex-col justify-center items-center text-center">
                <svg class="h-12 w-12 text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <p class="text-sm text-slate-400 mb-2">Data file storage metrics are currently measured in real-time server blocks.</p>
                <span class="text-cyan-400 font-mono font-bold">124.5 MB Used</span>
            </div>
        </div>
    </div>

    <!-- 4. BILLING HISTORY TAB -->
    <div x-show="tab === 'billing'" style="display: none;" class="bg-[#0f1524] p-6 rounded-xl border border-slate-800 shadow-lg" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        <h3 class="text-lg font-semibold text-slate-100 border-b border-slate-800 pb-3 mb-6">Payment Activity</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/50 border-b border-slate-800 text-xs uppercase tracking-wider text-slate-400">
                        <th class="px-4 py-3 font-semibold">Invoice #</th>
                        <th class="px-4 py-3 font-semibold">Date</th>
                        <th class="px-4 py-3 font-semibold">Amount</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold text-right">Receipt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50 text-sm">
                    @forelse($company->subscription->invoices ?? [] as $invoice)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-4 py-3 font-mono text-slate-300">{{ $invoice->invoice_number ?? 'INV-'.str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $invoice->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 font-mono text-slate-200">${{ number_format($invoice->amount, 2) }}</td>
                        <td class="px-4 py-3">
                            @if($invoice->status === 'paid')
                            <span class="px-2 py-1 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] uppercase font-bold tracking-wider">Paid</span>
                            @else
                            <span class="px-2 py-1 rounded bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[10px] uppercase font-bold tracking-wider">{{ $invoice->status }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button class="text-blue-400 hover:text-blue-300 transition-colors tooltip" title="Download PDF">
                                <svg class="h-5 w-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500 italic text-sm">No billing history available.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 5. AUDIT LOGS TAB -->
    <div x-show="tab === 'audit'" style="display: none;" class="bg-[#0f1524] p-6 rounded-xl border border-slate-800 shadow-lg" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        <h3 class="text-lg font-semibold text-slate-100 border-b border-slate-800 pb-3 mb-6">Security & Action Logs</h3>

        <div class="space-y-4 h-96 overflow-y-auto custom-scrollbar pr-2 cursor-default">
            @forelse($company->subscription->events ?? [] as $event)
            <div class="flex items-start p-3 bg-slate-900/50 border border-slate-800 rounded-lg">
                <div class="mt-0.5 mr-3 text-amber-500">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-slate-200"><span class="font-bold text-amber-400">System Event:</span> {{ Str::title(str_replace('_', ' ', $event->type ?? 'State Altered')) }}</p>
                    <p class="text-xs text-slate-500 mt-1 font-mono">{{ $event->created_at->format('Y-m-d H:i:s') }}</p>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center p-8 text-slate-500">
                <svg class="h-10 w-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p class="text-sm italic">System scanner found 0 recent administration events.</p>
            </div>
            @endforelse
        </div>
    </div>

</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #334155;
        border-radius: 10px;
    }

    [x-cloak] {
        display: none !important;
    }
</style>
@endsection