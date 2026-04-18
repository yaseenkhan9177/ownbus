@extends('layouts.super-admin')

@section('title', 'Billing & Revenue | SaaS Admin')

@section('header_title')
<div class="flex items-center space-x-4">
    <h1 class="text-xl font-bold text-slate-100 uppercase tracking-widest text-shadow-[0_0_10px_rgba(255,255,255,0.3)] flex items-center">
        <svg class="h-6 w-6 mr-3 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Financial Command
    </h1>
</div>
@endsection

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Top KPI Grid (6 Blocks) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6">

        <!-- MRR -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 p-5 shadow-lg relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 text-slate-800/30 group-hover:text-cyan-500/10 transition-colors duration-500">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                </svg>
            </div>
            <div class="relative z-10">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Monthly Recurring</p>
                <h3 class="text-3xl font-black text-cyan-400 tracking-tight">${{ number_format($kpis['mrr']) }}</h3>
                <p class="text-[10px] text-slate-500 mt-2 font-mono">LIVE MRR RUNRATE</p>
            </div>
        </div>

        <!-- ARR -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 p-5 shadow-lg relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 text-slate-800/30 group-hover:text-cyan-500/10 transition-colors duration-500">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="relative z-10">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Annual Revenue</p>
                <h3 class="text-3xl font-black text-slate-200 tracking-tight">${{ number_format($kpis['arr']) }}</h3>
                <p class="text-[10px] text-slate-500 mt-2 font-mono">PROJECTED ARR</p>
            </div>
        </div>

        <!-- ARPC -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 p-5 shadow-lg relative overflow-hidden group lg:col-span-1">
            <div class="relative z-10">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Avg Rev Per Co.</p>
                <h3 class="text-3xl font-black text-emerald-400 tracking-tight">${{ number_format($kpis['arpc'], 0) }}</h3>
                <p class="text-[10px] text-slate-500 mt-2 font-mono">ARPC</p>
            </div>
        </div>

        <!-- Active Subs -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 p-5 shadow-lg relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Active Subs</p>
                <h3 class="text-3xl font-black text-slate-200 tracking-tight">{{ number_format($kpis['active_subscriptions']) }}</h3>
                <p class="text-[10px] text-slate-500 mt-2 font-mono">TENANT ORGS</p>
            </div>
        </div>

        <!-- Churn Rate -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 p-5 shadow-lg relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Platform Churn</p>
                <h3 class="text-3xl font-black {{ $kpis['churn_rate'] > 5 ? 'text-rose-500' : 'text-emerald-500' }} tracking-tight">{{ number_format($kpis['churn_rate'], 1) }}%</h3>
                <p class="text-[10px] text-slate-500 mt-2 font-mono">HISTORICAL CHURN</p>
            </div>
        </div>

        <!-- Failed Payments -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 p-5 shadow-lg relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Failed Payments</p>
                <h3 class="text-3xl font-black {{ $kpis['failed_payments_count'] > 0 ? 'text-amber-500' : 'text-slate-200' }} tracking-tight">{{ number_format($kpis['failed_payments_count']) }}</h3>
                <p class="text-[10px] text-slate-500 mt-2 font-mono">LAST 30 DAYS</p>
            </div>
        </div>

    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Revenue Trend -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 p-6 shadow-lg lg:col-span-2">
            <div class="flex items-center justify-between mb-6 border-b border-slate-800 pb-4">
                <h3 class="text-sm font-semibold text-slate-300 uppercase tracking-wider">MRR Velocity (12 Months)</h3>
            </div>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Sub Breakdown -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 p-6 shadow-lg lg:col-span-1">
            <div class="flex items-center justify-between mb-6 border-b border-slate-800 pb-4">
                <h3 class="text-sm font-semibold text-slate-300 uppercase tracking-wider">Health Distribution</h3>
            </div>
            <div class="h-64 relative">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Data Tables Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Recent Successful Invoices -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 shadow-lg overflow-hidden flex flex-col h-[500px]">
            <div class="px-6 py-5 border-b border-slate-800 bg-slate-900/50 flex justify-between items-center shrink-0">
                <h3 class="text-sm font-semibold text-emerald-400 uppercase tracking-wider flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Recent Cleared Payouts
                </h3>
            </div>
            <div class="overflow-y-auto flex-1 p-0 custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 z-10">
                        <tr class="bg-slate-900 border-b border-slate-800 text-xs uppercase tracking-wider text-slate-500">
                            <th class="px-6 py-3 font-semibold">Tenant Organization</th>
                            <th class="px-6 py-3 font-semibold">Volume</th>
                            <th class="px-6 py-3 font-semibold text-right">Cleared</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50 text-sm">
                        @forelse($recentInvoices as $invoice)
                        <tr class="hover:bg-slate-800/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-200">{{ $invoice->subscription->company->name ?? 'Unknown' }}</div>
                                <div class="text-xs text-slate-500 mt-1 font-mono">Invoice #{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-emerald-400">${{ number_format($invoice->amount, 2) }}</div>
                                <div class="text-[10px] text-slate-500 mt-1 uppercase tracking-wider">{{ $invoice->subscription->plan->name ?? 'Custom' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="font-mono text-xs text-slate-400">{{ $invoice->paid_at ? \Carbon\Carbon::parse($invoice->paid_at)->format('M d, H:i') : '-' }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-slate-500 italic">No recent paid invoices available.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Failed & Delinquent Collections -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 shadow-lg overflow-hidden flex flex-col h-[500px]">
            <div class="px-6 py-5 border-b border-slate-800 bg-slate-900/50 flex justify-between items-center shrink-0">
                <h3 class="text-sm font-semibold text-rose-500 uppercase tracking-wider flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Collections Grid
                </h3>
            </div>
            <div class="overflow-y-auto flex-1 p-0 custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 z-10">
                        <tr class="bg-slate-900 border-b border-slate-800 text-xs uppercase tracking-wider text-slate-500">
                            <th class="px-6 py-3 font-semibold">Delinquent Organization</th>
                            <th class="px-6 py-3 font-semibold">Deficit</th>
                            <th class="px-6 py-3 font-semibold text-right">State</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50 text-sm">
                        @forelse($failedPayments as $failed)
                        <tr class="hover:bg-slate-800/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-200">{{ $failed->subscription->company->name ?? 'Unknown' }}</div>
                                <div class="text-[10px] text-slate-500 mt-1 font-mono">Attempted: {{ $failed->created_at->format('M d, H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-rose-400">${{ number_format($failed->amount, 2) }}</div>
                                <div class="text-[10px] text-slate-500 mt-1 uppercase tracking-wider">Retries: {{ $failed->retry_count ?? 0 }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-block px-2 py-1 rounded text-[10px] bg-rose-500/10 text-rose-500 border border-rose-500/20 font-bold uppercase tracking-widest">
                                    {{ $failed->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-emerald-500/70 italic text-sm font-medium">Platform health is excellent. Zero collection issues.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.5);
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(51, 65, 85, 0.8);
        border-radius: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(71, 85, 105, 1);
    }
</style>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- REVENUE TRENDLINE CHART ---
        const trendData = {
            !!json_encode($revenueTrend['data'] ?? []) !!
        };
        const ctxRevenue = document.getElementById('revenueChart').getContext('2d');

        // Gradient Fill
        let gradientRev = ctxRevenue.createLinearGradient(0, 0, 0, 400);
        gradientRev.addColorStop(0, 'rgba(34, 211, 238, 0.4)'); // Cyan
        gradientRev.addColorStop(1, 'rgba(34, 211, 238, 0.0)');

        new Chart(ctxRevenue, {
            type: 'line',
            data: {
                labels: trendData.map(item => item.month),
                datasets: [{
                    label: 'Gross Paid Revenue',
                    data: trendData.map(item => item.revenue),
                    borderColor: '#22d3ee', // Tailwind Cyan-400
                    borderWidth: 3,
                    backgroundColor: gradientRev,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#0f1524',
                    pointBorderColor: '#22d3ee',
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
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        borderColor: '#334155',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                return '$' + parseInt(context.raw).toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                family: "'JetBrains Mono', monospace",
                                size: 10
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: '#1e293b',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                family: "'JetBrains Mono', monospace",
                                size: 10
                            },
                            callback: function(value) {
                                return '$' + value;
                            }
                        },
                        beginAtZero: true
                    }
                }
            }
        });

        // --- SUBSCRIPTION STATUS PIE CHART ---
        const breakdownData = {
            !!json_encode($subscriptionBreakdown) !!
        };
        const ctxStatus = document.getElementById('statusChart').getContext('2d');

        // Mapping standard colors
        const colorMap = {
            'Active': '#10b981', // Emerald 500
            'Trialing': '#f59e0b', // Amber 500
            'Past due': '#f43f5e', // Rose 500
            'Cancelled': '#64748b' // Slate 500
        };

        const bgColors = breakdownData.map(item => colorMap[item.status] || '#8b5cf6');

        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: breakdownData.map(item => item.status),
                datasets: [{
                    data: breakdownData.map(item => item.count),
                    backgroundColor: bgColors,
                    borderColor: '#0f1524',
                    borderWidth: 4,
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
                                size: 11,
                                family: "'Inter', sans-serif",
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });

    });
</script>
@endpush