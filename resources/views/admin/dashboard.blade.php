@extends('layouts.super-admin')

@section('title', 'Admin SaaS Command Center')

@push('styles')
<style>
    .cyber-gradient {
        background: radial-gradient(circle at top right, rgba(6, 182, 212, 0.15), transparent),
            radial-gradient(circle at bottom left, rgba(59, 130, 246, 0.15), transparent);
    }

    .neon-text-cyan {
        text-shadow: 0 0 10px rgba(6, 182, 212, 0.5);
    }

    .neon-border-cyan {
        border-color: rgba(6, 182, 212, 0.3);
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.1);
    }
</style>
@endpush

@section('header_title')
<div class="flex items-center space-x-3">
    <h1 class="text-lg font-bold text-slate-100 uppercase tracking-widest text-shadow-[0_0_10px_rgba(255,255,255,0.3)]">
        SUPER ADMIN DASHBOARD - OVERVIEW
    </h1>
</div>
@endsection

@section('content')
<div class="space-y-8 cyber-gradient min-h-full">

    <!-- Top Row: KPI Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6" x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 800)">
        <!-- Skeleton -->
        <template x-for="i in 6" :key="i">
            <div x-show="loading" class="bg-slate-800/50 animate-pulse rounded-2xl p-6 h-[104px] border border-slate-700"></div>
        </template>

        <!-- Actual Data -->
        <div x-show="!loading" class="contents">
            <x-cyber-kpi
                title="MRR"
                :value="'$' . number_format($kpis['mrr'], 0)"
                trend=""
                icon='<path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
                color="emerald" />

            <x-cyber-kpi
                title="ARR"
                :value="'$' . number_format($kpis['arr'], 0)"
                trend=""
                icon='<path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />'
                color="cyan" />

            <x-cyber-kpi
                title="CHURN RATE"
                :value="$kpis['churn_rate'] . '%'"
                trend=""
                icon='<path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />'
                color="rose" />

            <x-cyber-kpi
                title="LTV"
                :value="'$' . number_format($kpis['ltv'], 0)"
                trend="18 Months"
                icon='<path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />'
                color="amber" />

            <x-cyber-kpi
                title="TOTAL COMPANIES"
                :value="number_format($kpis['total_companies'])"
                trend=""
                icon='<path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />'
                color="cyan" />

            <x-cyber-kpi
                title="ACTIVE SUBS"
                :value="number_format($kpis['active_subscriptions'])"
                trend=""
                icon='<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />'
                color="emerald" />
        </div>
    </div>

    <!-- Middle Row: Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Revenue Trajectory -->
        <div class="lg:col-span-8 bg-[#0f1524] p-6 rounded-2xl border border-slate-800 shadow-md">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-semibold text-slate-100 mb-1">Revenue Trend (Last 12 Months)</h3>
                </div>
            </div>
            <div class="h-80 w-full relative">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Plan Distribution Pie -->
        <div class="lg:col-span-4 bg-[#0f1524] p-6 rounded-2xl border border-slate-800 shadow-md flex flex-col">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-semibold text-slate-100">Plan Distribution</h3>
            </div>
            <div class="h-80 w-full relative flex items-center justify-center flex-1">
                <canvas id="planPieChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Additional Rows: Health, Resources, and Billing -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Resource Usage -->
        <div class="bg-[#0f1524] p-6 rounded-2xl border border-slate-800 shadow-md">
            <h3 class="text-lg font-semibold text-slate-100 mb-4 pb-2 border-b border-slate-800">Top Tenants Resource Usage</h3>
            <div class="space-y-4">
                @foreach($resourceUsage as $usageCompany)
                <div class="flex justify-between items-center text-sm">
                    <div class="font-medium text-slate-200">{{ $usageCompany->name }}</div>
                    <div class="text-slate-400 font-mono text-xs flex space-x-2">
                        <span>Vehicles: {{ $usageCompany->vehicles_count }}</span>
                        <span>Drivers: {{ $usageCompany->drivers_count }}</span>
                        <span>Users: {{ $usageCompany->users_count }}</span>
                    </div>
                </div>
                <!-- Mini Progress Bar purely decorative -->
                <div class="w-full bg-slate-800 rounded-full h-1.5 mt-1">
                    <div class="bg-cyan-500 h-1.5 rounded-full" style="width: {{ min(100, max(5, ($usageCompany->vehicles_count / 100) * 100)) }}%"></div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Live System Health -->
        <div class="bg-[#0f1524] p-6 rounded-2xl border border-slate-800 shadow-md">
            <h3 class="text-lg font-semibold text-slate-100 mb-4 pb-2 border-b border-slate-800 flex items-center justify-between">
                System Health
                <span class="animate-pulse h-2 w-2 bg-emerald-500 rounded-full"></span>
            </h3>
            <div class="space-y-6">
                <!-- CPU -->
                <div>
                    <div class="flex justify-between items-center mb-1 text-sm text-slate-300">
                        <span>CPU Load</span>
                        <span class="font-mono text-cyan-400">{{ $systemHealth['cpu_usage'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-800 rounded-full h-2">
                        <div class="bg-cyan-500 h-2 rounded-full" style="width: {{ min(100, $systemHealth['cpu_usage']) }}%"></div>
                    </div>
                </div>
                <!-- RAM -->
                <div>
                    <div class="flex justify-between items-center mb-1 text-sm text-slate-300">
                        <span>RAM Usage</span>
                        <span class="font-mono text-emerald-400">{{ $systemHealth['ram_usage'] }} / {{ $systemHealth['ram_total'] }}</span>
                    </div>
                    <div class="w-full bg-slate-800 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full" style="width: 45%"></div>
                    </div>
                </div>
                <!-- Disk -->
                <div>
                    <div class="flex justify-between items-center mb-1 text-sm text-slate-300">
                        <span>Disk Usage</span>
                        <span class="font-mono text-purple-400">{{ $systemHealth['disk_usage'] }} / {{ $systemHealth['disk_total'] }}</span>
                    </div>
                    <div class="w-full bg-slate-800 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: 30%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing Management -->
        <div class="bg-[#0f1524] p-6 rounded-2xl border border-slate-800 shadow-md">
            <h3 class="text-lg font-semibold text-slate-100 mb-4 pb-2 border-b border-slate-800">Billing Management</h3>
            <div class="flex flex-col space-y-4">
                <div class="flex items-center justify-between p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
                    <span class="text-emerald-400 text-sm font-semibold">Active Subscriptions</span>
                    <span class="text-emerald-300 font-mono font-bold">{{ $subscriptionsDetails['active'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg bg-rose-500/10 border border-rose-500/20">
                    <span class="text-rose-400 text-sm font-semibold">Expired/Canceled</span>
                    <span class="text-rose-300 font-mono font-bold">{{ $subscriptionsDetails['expired'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg bg-amber-500/10 border border-amber-500/20">
                    <span class="text-amber-400 text-sm font-semibold">Trial Users</span>
                    <span class="text-amber-300 font-mono font-bold">{{ $subscriptionsDetails['trialing'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Feeds -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Error Alerts -->
        <div class="bg-[#0f1524] p-6 rounded-2xl border border-red-900/50 shadow-md flex flex-col h-96 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-600 to-rose-900"></div>
            <div class="mb-4 pb-2 border-b border-slate-800 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-rose-500 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    System Exceptions Target
                </h3>
            </div>
            <div class="space-y-4 overflow-y-auto flex-1 pr-2 custom-scrollbar">
                @forelse($systemErrors as $errorLog)
                <div class="p-3 rounded-lg bg-slate-900/50 border border-red-900/30">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-xs font-bold text-slate-300">{{ $errorLog->tenant->name ?? 'System' }}</span>
                        <span class="text-[10px] text-slate-500 font-mono">{{ $errorLog->created_at->format('H:i') }}</span>
                    </div>
                    <p class="text-xs text-rose-400 font-mono line-clamp-2 truncate" title="{{ $errorLog->error_message }}">
                        {{ $errorLog->error_message }}
                    </p>
                    <p class="text-[10px] text-slate-500 mt-1 truncate">
                        {{ $errorLog->url }}
                    </p>
                </div>
                @empty
                <div class="flex items-center justify-center h-full text-center">
                    <p class="text-sm text-slate-600 italic font-mono uppercase tracking-widest">No Critical Errors Detected</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- System Activities -->
        <div class="bg-[#0f1524] p-6 rounded-2xl border border-slate-800 shadow-md flex flex-col h-96 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-600 to-cyan-400"></div>
            <div class="mb-4 pb-2 border-b border-slate-800 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-slate-100 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Recent Activity Feed
                </h3>
            </div>
            <div class="space-y-3 overflow-y-auto flex-1 pr-2 custom-scrollbar text-sm">
                @forelse($systemActivities as $activity)
                <div class="flex items-start">
                    <div class="mt-1 mr-3 w-2 h-2 rounded-full bg-cyan-500 shadow-[0_0_5px_#06b6d4]"></div>
                    <div class="flex-1">
                        <span class="text-slate-400 font-mono text-xs mr-2">{{ $activity->created_at->format('H:i') }}</span>
                        <span class="text-cyan-400 font-semibold">{{ $activity->tenant->name ?? 'System' }}</span>
                        <span class="text-slate-300 ml-1">{{ $activity->action }}</span>
                        @if($activity->description)
                        <div class="text-slate-500 text-xs mt-0.5 truncate">{{ $activity->description }}</div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="flex items-center justify-center h-full text-center">
                    <p class="text-sm text-slate-600 italic font-mono uppercase tracking-widest">No Activities Logged</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Fourth Row: Original Signups/Failed Payments -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Signups -->
        <div class="bg-[#0f1524] p-6 rounded-2xl border border-slate-800 shadow-md flex flex-col h-96">
            <div class="mb-4 pb-2 border-b border-slate-800 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-slate-100">Recent Signups</h3>
                <span class="px-2 py-1 bg-cyan-500/10 text-cyan-400 text-xs font-bold rounded-md border border-cyan-500/20">Latest {{ count($recentSignups) }}</span>
            </div>
            <div class="space-y-4 overflow-y-auto flex-1 pr-2 custom-scrollbar">
                @forelse($recentSignups as $company)
                <div class="flex items-center p-3 rounded-lg bg-slate-800/20 border border-slate-800/50 hover:bg-slate-800/40 transition-colors">
                    <div class="mr-4 text-cyan-500 bg-cyan-500/10 p-2 rounded-lg">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-200 truncate">{{ $company->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ $company->owner_name ?? 'No Owner' }} &middot; {{ $company->subscription->plan->name ?? 'No Plan' }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-mono text-slate-400">{{ $company->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @empty
                <div class="flex items-center justify-center h-full text-center">
                    <p class="text-sm text-slate-500 italic font-mono uppercase tracking-widest">No Recent Signups</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Failed Payments -->
        <div class="bg-[#0f1524] p-6 rounded-2xl border border-slate-800 shadow-md flex flex-col h-96">
            <div class="mb-4 pb-2 border-b border-slate-800 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-slate-100">Recent Failed Payments</h3>
                <span class="px-2 py-1 bg-rose-500/10 text-rose-400 text-xs font-bold rounded-md border border-rose-500/20">{{ count($failedPaymentsFeed) }} Alerts</span>
            </div>
            <div class="space-y-4 overflow-y-auto flex-1 pr-2 custom-scrollbar">
                @forelse($failedPaymentsFeed as $invoice)
                <div class="flex items-center p-3 rounded-lg bg-rose-900/10 border border-rose-800/30 hover:bg-rose-900/20 transition-colors">
                    <div class="mr-4 text-rose-500 bg-rose-500/10 p-2 rounded-lg">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-200 truncate">{{ $invoice->company->name ?? 'Unknown Company' }}</p>
                        <p class="text-xs text-rose-400 font-mono">${{ number_format($invoice->amount, 2) }} &middot; {{ $invoice->subscription->plan->name ?? 'Unknown Plan' }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-mono text-slate-500">{{ $invoice->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @empty
                <div class="flex items-center justify-center h-full text-center">
                    <p class="text-sm text-slate-500 italic font-mono uppercase tracking-widest">No Failed Payments</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. Revenue Trend Chart ---
        const revCtx = document.getElementById('revenueChart');
        if (revCtx) {
            const ctx = revCtx.getContext('2d');
            const revGradient = ctx.createLinearGradient(0, 0, 0, 300);
            revGradient.addColorStop(0, 'rgba(6, 182, 212, 0.4)');
            revGradient.addColorStop(1, 'rgba(6, 182, 212, 0.0)');

            const revData = @json($revenueTrend['data'] ?? []);
            const revLabels = @json($revenueTrend['labels'] ?? []);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: revLabels,
                    datasets: [{
                        label: 'Revenue',
                        data: revData,
                        borderColor: '#06b6d4',
                        borderWidth: 3,
                        backgroundColor: revGradient,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#0f1524',
                        pointBorderColor: '#06b6d4',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleFont: {
                                size: 13,
                                family: 'monospace'
                            },
                            bodyFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return '$' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255,255,255,0.05)'
                            },
                            ticks: {
                                color: '#64748b',
                                font: {
                                    family: 'monospace',
                                    size: 11
                                },
                                callback: value => '$' + value
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#64748b',
                                font: {
                                    family: 'monospace',
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        }

        // --- 2. Plan Distribution Pie Chart ---
        const pieCtx = document.getElementById('planPieChart');
        if (pieCtx) {
            const planData = @json($planDistribution['data'] ?? []);
            const planLabels = @json($planDistribution['labels'] ?? []);

            new Chart(pieCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: planLabels,
                    datasets: [{
                        data: planData,
                        backgroundColor: [
                            '#06b6d4', // Cyan
                            '#3b82f6', // Blue
                            '#10b981', // Emerald
                            '#6366f1', // Indigo
                            '#8b5cf6' // Violet
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#94a3b8',
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 12,
                                    family: 'sans-serif'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            bodyFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            padding: 10,
                            displayColors: true
                        }
                    }
                }
            });
        }
    });
</script>
<style>
    /* Custom Scrollbar for Feeds */
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
</style>
@endpush