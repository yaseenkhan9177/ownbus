@extends('layouts.super-admin')

@section('title', 'Super Admin Command Center | OwnBus')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Outfit', sans-serif;
        background-color: #050811;
    }

    .premium-gradient {
        background: radial-gradient(circle at top right, rgba(212, 168, 71, 0.08), transparent),
            radial-gradient(circle at bottom left, rgba(10, 15, 30, 0.5), transparent);
    }

    .gold-glow {
        text-shadow: 0 0 15px rgba(212, 168, 71, 0.3);
    }

    .premium-table tr:hover {
        background: rgba(212, 168, 71, 0.03);
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.02);
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(212, 168, 71, 0.2);
        border-radius: 10px;
    }
</style>
@endpush

@section('content')
<div class="px-6 py-8 premium-gradient min-h-screen text-slate-200">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight gold-glow uppercase">
                System <span class="text-[#D4A847]">Overview</span>
            </h1>
            <p class="text-slate-500 text-sm mt-1 font-medium tracking-wide">Command Center &middot; UAE Multi-Tenant Portal</p>
        </div>
        <div class="flex items-center space-x-3">
            <button class="bg-[#D4A847] hover:bg-[#c2983b] text-[#0A0F1E] px-5 py-2.5 rounded-xl font-bold text-sm transition-all duration-300 shadow-lg shadow-[#D4A847]/10 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Create Announcement
            </button>
            <div class="bg-[#0A0F1E] border border-[#D4A847]/20 p-2 rounded-xl text-[#D4A847] animate-pulse">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
            </div>
        </div>
    </div>

    <!-- Top Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-10">
        <x-premium-stat 
            title="Monthly Revenue" 
            :value="$kpis['mrr']" 
            prefix="$" 
            trend="12.5%" 
            :trendUp="true"
            icon='<path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />' />
        
        <x-premium-stat 
            title="Annual Revenue" 
            :value="$kpis['arr']" 
            prefix="$" 
            trend="8.2%" 
            :trendUp="true"
            icon='<path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />' />
        
        <x-premium-stat 
            title="Total Companies" 
            :value="$kpis['total_companies']" 
            trend="2 New" 
            :trendUp="true"
            icon='<path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />' />
        
        <x-premium-stat 
            title="Active Subs" 
            :value="$kpis['active_subscriptions']" 
            trend="94%" 
            :trendUp="true"
            icon='<path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />' />
        
        <x-premium-stat 
            title="Churn Rate" 
            :value="$kpis['churn_rate']" 
            suffix="%" 
            :trendUp="false"
            trend="0.5%" 
            icon='<path d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />' />
        
        <x-premium-stat 
            title="Lifetime Value" 
            :value="$kpis['ltv']" 
            prefix="$" 
            trend="Stable" 
            icon='<path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />' />
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-10">
        <!-- Revenue Chart -->
        <div class="lg:col-span-8 bg-[#0A0F1E] border border-[#D4A847]/10 rounded-2xl p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-[#D4A847]/50"></div>
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-bold text-white tracking-tight uppercase">Revenue Analytics</h3>
                    <p class="text-slate-500 text-xs font-semibold tracking-wider mt-1">LAST 12 MONTHS PERFORMANCE</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="flex items-center text-[10px] font-bold text-[#D4A847] bg-[#D4A847]/10 px-3 py-1 rounded-full border border-[#D4A847]/20 uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#D4A847] mr-2"></span> Paid Invoices
                    </span>
                </div>
            </div>
            <div class="h-[350px]">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>

        <!-- Plan Distribution -->
        <div class="lg:col-span-4 bg-[#0A0F1E] border border-[#D4A847]/10 rounded-2xl p-8 shadow-2xl relative overflow-hidden">
             <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-bold text-white tracking-tight uppercase">Plan Distribution</h3>
                    <p class="text-slate-500 text-xs font-semibold tracking-wider mt-1">SUBSCRIPTION SEGMENTS</p>
                </div>
            </div>
            <div class="h-[350px] flex items-center justify-center">
                <canvas id="planPieChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Health and Subscription Overview Row -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-10">
        <!-- System Health Monitor -->
        <div class="lg:col-span-3 bg-[#0A0F1E] border border-[#D4A847]/10 rounded-2xl p-6 shadow-2xl">
            <h3 class="text-sm font-bold text-white tracking-tight uppercase mb-6 flex items-center">
                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-3 animate-pulse"></span>
                System Health
            </h3>
            <div class="space-y-4">
                <!-- DB Status -->
                <div class="flex items-center justify-between p-3 rounded-xl bg-white/5 border border-white/5">
                    <div>
                        <p class="text-[9px] font-bold text-[#D4A847] uppercase tracking-wider">DB Status</p>
                        <p class="text-xs text-white font-bold">{{ $systemHealth['db_status'] }}</p>
                    </div>
                    <div class="text-emerald-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>
                    </div>
                </div>
                <!-- Queue Status -->
                <div class="flex items-center justify-between p-3 rounded-xl bg-white/5 border border-white/5">
                    <div>
                        <p class="text-[9px] font-bold text-[#D4A847] uppercase tracking-wider">Queues</p>
                        <p class="text-xs text-white font-bold">{{ $systemHealth['queue_status'] }} ({{ $systemHealth['queue_size'] }})</p>
                    </div>
                    <div class="text-[#D4A847]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Overview -->
        <div class="lg:col-span-3 bg-[#0A0F1E] border border-[#D4A847]/10 rounded-2xl p-6 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#D4A847]/5 rounded-full blur-2xl"></div>
            <h3 class="text-sm font-bold text-white tracking-tight uppercase mb-6 flex items-center">
                <svg class="w-4 h-4 mr-3 text-[#D4A847]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                Subscription Overview
            </h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-400 font-medium">Active</span>
                    <span class="flex items-center gap-2">
                        <span class="text-xs font-bold text-white">{{ $subscriptionsDetails['active'] }}</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-400 font-medium">On Trial</span>
                    <span class="flex items-center gap-2">
                        <span class="text-xs font-bold text-white">{{ $subscriptionsDetails['trial'] }}</span>
                        <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-400 font-medium">Expiring Soon</span>
                    <span class="flex items-center gap-2">
                        <span class="text-xs font-bold text-white">{{ $subscriptionsDetails['expiring'] }}</span>
                        <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                    </span>
                </div>
                <div class="flex items-center justify-between border-t border-white/5 pt-4">
                    <span class="text-xs text-slate-400 font-medium">Expired</span>
                    <span class="flex items-center gap-2">
                        <span class="text-xs font-bold text-white">{{ $subscriptionsDetails['expired'] }}</span>
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    </span>
                </div>
            </div>

            <a href="{{ route('admin.companies.index') }}" class="mt-6 block text-center py-2 bg-white/5 hover:bg-white/10 rounded-lg text-[10px] font-bold text-[#D4A847] uppercase tracking-widest transition-all">
                View All Companies →
            </a>
        </div>

        <!-- Broadcast Message Panel -->
        <div class="lg:col-span-6 bg-[#0A0F1E] border border-[#D4A847]/10 rounded-2xl p-6 shadow-2xl relative">
            <h3 class="text-sm font-bold text-white tracking-tight uppercase mb-6 flex items-center">
                <svg class="w-4 h-4 mr-3 text-[#D4A847]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                System-Wide Broadcast
            </h3>
            <form action="{{ route('admin.broadcasts.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <textarea name="message" rows="2" class="w-full bg-black/40 border border-[#D4A847]/20 rounded-xl p-3 text-sm text-white placeholder-slate-600 focus:outline-none focus:border-[#D4A847] transition-colors" placeholder="Type message..."></textarea>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="urgent" class="hidden peer">
                            <div class="w-4 h-4 border border-[#D4A847]/30 rounded flex items-center justify-center mr-2 peer-checked:bg-[#D4A847]">
                                <svg class="w-2.5 h-2.5 text-[#0A0F1E] opacity-0 peer-checked:opacity-100" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 group-hover:text-slate-200 uppercase">Urgent</span>
                        </label>
                        <button type="submit" class="bg-[#242938] hover:bg-[#D4A847] hover:text-[#0A0F1E] text-[#D4A847] px-6 py-2 rounded-lg font-bold text-[10px] transition-all border border-[#D4A847]/20 uppercase">
                            Send Broadcast
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Tables Row -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
        <!-- Recent Registrations -->
        <div class="xl:col-span-8 bg-[#0A0F1E] border border-[#D4A847]/10 rounded-2xl p-8 shadow-2xl relative">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-bold text-white tracking-tight uppercase">Recent Company Registrations</h3>
                <a href="{{ route('admin.companies.index') }}" class="text-[#D4A847] text-xs font-bold uppercase tracking-widest hover:gold-glow transition-all">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full premium-table">
                    <thead>
                        <tr class="text-left border-b border-white/5">
                            <th class="pb-4 text-[10px] font-bold text-[#D4A847] uppercase tracking-[0.2em]">Company / Owner</th>
                            <th class="pb-4 text-[10px] font-bold text-[#D4A847] uppercase tracking-[0.2em]">Plan</th>
                            <th class="pb-4 text-[10px] font-bold text-[#D4A847] uppercase tracking-[0.2em]">Registered</th>
                            <th class="pb-4 text-[10px] font-bold text-[#D4A847] uppercase tracking-[0.2em]">Status</th>
                            <th class="pb-4 text-[10px] font-bold text-[#D4A847] uppercase tracking-[0.2em] text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($recentSignups as $company)
                        <tr class="text-sm transition-colors group">
                            <td class="py-5">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-[#D4A847] font-bold mr-4 group-hover:border-[#D4A847]/50 transition-all">
                                        {{ substr($company->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-white font-bold tracking-wide">{{ $company->name }}</p>
                                        <p class="text-slate-500 text-xs mt-0.5">{{ $company->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-5">
                                <span class="px-3 py-1 rounded-lg bg-indigo-500/10 text-indigo-400 text-[11px] font-bold uppercase tracking-widest border border-indigo-500/20">
                                    {{ $company->subscription->plan->name ?? 'No Plan' }}
                                </span>
                            </td>
                            <td class="py-5 text-slate-400 text-xs font-medium">
                                {{ $company->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-5">
                                <span class="flex items-center text-[10px] font-bold {{ $company->status === 'active' ? 'text-emerald-400' : 'text-amber-400' }} uppercase tracking-widest">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $company->status === 'active' ? 'bg-emerald-400' : 'bg-amber-400' }} mr-2 animate-pulse"></span>
                                    {{ $company->status }}
                                </span>
                            </td>
                            <td class="py-5 text-right">
                                <a href="{{ route('admin.companies.show', $company->id) }}" class="text-slate-400 hover:text-white transition-colors">
                                    <svg class="w-5 h-5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Side: Feeds & Approvals -->
        <div class="xl:col-span-4 space-y-8">
            <!-- Pending Approvals -->
            <div class="bg-[#0A0F1E] border border-[#D4A847]/10 rounded-2xl p-8 shadow-2xl">
                <h3 class="text-lg font-bold text-white tracking-tight uppercase mb-6 flex items-center">
                    <span class="w-2 h-2 rounded-full bg-amber-500 mr-3 animate-pulse"></span>
                    Access Requests
                </h3>
                <div class="space-y-4">
                    @forelse($pendingApprovals as $request)
                    <div class="p-4 rounded-xl bg-white/5 border border-white/5 group hover:border-amber-500/50 transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-500 flex items-center justify-center font-bold text-xs mr-3">
                                    {{ substr($request->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white tracking-wide">{{ $request->name }}</p>
                                    <p class="text-[10px] text-slate-500">{{ $request->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <form action="{{ route('admin.requests.approve', $request->id) }}" method="POST" class="flex-1">
                                @csrf
                                <button class="w-full bg-[#D4A847] text-[#0A0F1E] text-[10px] font-bold uppercase py-2 rounded-lg hover:shadow-lg hover:shadow-[#D4A847]/20 transition-all">
                                    Approve
                                </button>
                            </form>
                            <form action="{{ route('admin.requests.reject', $request->id) }}" method="POST" class="flex-1">
                                @csrf
                                <button class="w-full bg-rose-500/10 text-rose-500 border border-rose-500/20 text-[10px] font-bold uppercase py-2 rounded-lg hover:bg-rose-500 hover:text-white transition-all">
                                    Reject
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <p class="text-slate-600 text-xs font-bold uppercase tracking-widest">No Pending Requests</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Top Revenue Companies -->
            <div class="bg-[#0A0F1E] border border-[#D4A847]/10 rounded-2xl p-8 shadow-2xl">
                <h3 class="text-lg font-bold text-white tracking-tight uppercase mb-6 flex items-center justify-between">
                    Top Producers
                    <svg class="w-5 h-5 text-[#D4A847]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                </h3>
                <div class="space-y-5">
                    @foreach($topRevenueCompanies as $topComp)
                    <div class="flex items-center justify-between p-3 rounded-xl hover:bg-white/5 transition-all">
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-[#D4A847] mr-4"></div>
                            <span class="text-sm font-bold text-white tracking-wide truncate max-w-[120px]">{{ $topComp->name }}</span>
                        </div>
                        <span class="text-xs font-mono font-bold text-[#D4A847]">
                            ${{ number_format($topComp->subscription->plan->price_monthly ?? 0, 0) }}/mo
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const primaryGold = '#D4A847';
        const darkNavy = '#0A0F1E';
        
        // Revenue Chart
        const trendCtx = document.getElementById('revenueTrendChart').getContext('2d');
        const trendGradient = trendCtx.createLinearGradient(0, 0, 0, 400);
        trendGradient.addColorStop(0, 'rgba(212, 168, 71, 0.3)');
        trendGradient.addColorStop(1, 'rgba(212, 168, 71, 0.0)');

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: @json($revenueTrend['labels'] ?? []),
                datasets: [{
                    label: 'Revenue',
                    data: @json($revenueTrend['data'] ?? []),
                    borderColor: primaryGold,
                    backgroundColor: trendGradient,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: darkNavy,
                    pointBorderColor: primaryGold,
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#161B2E',
                        titleColor: '#D4A847',
                        bodyColor: '#FFF',
                        padding: 15,
                        borderColor: 'rgba(212, 168, 71, 0.3)',
                        borderWidth: 1,
                        callbacks: {
                            label: (context) => '$' + context.parsed.y.toLocaleString()
                        }
                    }
                },
                scales: {
                    y: {
                        grid: { color: 'rgba(255, 255, 255, 0.03)' },
                        ticks: { color: '#64748b', font: { family: 'Outfit' }, callback: v => '$' + v }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { family: 'Outfit' } }
                    }
                }
            }
        });

        // Plan Distribution Pie
        const pieCtx = document.getElementById('planPieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: @json($planDistribution['labels'] ?? []),
                datasets: [{
                    data: @json($planDistribution['data'] ?? []),
                    backgroundColor: [
                        '#D4A847',
                        '#9E7E36',
                        '#6B5525',
                        '#382C14',
                        '#1B150A'
                    ],
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '80%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#94a3b8',
                            usePointStyle: true,
                            padding: 25,
                            font: { family: 'Outfit', size: 11 }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush