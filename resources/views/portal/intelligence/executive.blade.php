@extends('layouts.company')

@section('title', 'Executive Intelligence')

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Executive Intelligence</h1>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-sm font-black text-slate-400 uppercase tracking-widest">Command Overview</h2>
            <p class="text-xs text-slate-500">Real-time profitability derived from ledger truth.</p>
        </div>
        <button onclick="window.print()" class="px-4 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl text-xs font-bold uppercase tracking-widest hover:opacity-90 transition-all shadow-lg flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Print Intel
        </button>
    </div>

    {{-- 1. Financial KPI Widgets --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Cash Balance -->
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm relative overflow-hidden group hover:border-indigo-500/30 transition-all duration-300">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-1">Available Liquidity</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white">AED {{ number_format($stats['financial']['cash_balance'], 2) }}</h3>
                <p class="text-[10px] text-slate-500 mt-2 flex items-center">
                    <span class="w-1 h-1 bg-indigo-500 rounded-full mr-1.5"></span>
                    Aggregated from Ledger
                </p>
            </div>
        </div>

        <!-- AR Outstanding -->
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm relative overflow-hidden group hover:border-emerald-500/30 transition-all duration-300">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-1">Accounts Receivable</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white">AED {{ number_format($stats['financial']['ar_outstanding'], 2) }}</h3>
                <p class="text-[10px] text-slate-500 mt-2 flex items-center">
                    <span class="w-1 h-1 bg-emerald-500 rounded-full mr-1.5"></span>
                    Outstanding Collections
                </p>
            </div>
        </div>

        <!-- AP Outstanding -->
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm relative overflow-hidden group hover:border-rose-500/30 transition-all duration-300">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-1">Accounts Payable</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white">AED {{ number_format($stats['financial']['ap_outstanding'], 2) }}</h3>
                <p class="text-[10px] text-slate-500 mt-2 flex items-center">
                    <span class="w-1 h-1 bg-rose-500 rounded-full mr-1.5"></span>
                    Vendor Obligations
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- 2. Revenue Trend Chart --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest">6-Month Trend</h3>
                    <p class="text-xl font-bold text-slate-900 dark:text-white">Revenue Performance</p>
                </div>
            </div>
            <div class="h-80 relative">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>

        {{-- 3. Maintenance Intelligence --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-6">Repair Analytics</h3>

            <div class="space-y-6">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Active Maintenance</span>
                        <span class="text-xs font-black text-rose-500">{{ $stats['maintenance_alerts']['due_now'] }} Units</span>
                    </div>
                    <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                        <div class="bg-rose-500 h-full" style="width: 25%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Upcoming Service</span>
                        <span class="text-xs font-black text-amber-500">{{ $stats['maintenance_alerts']['upcoming'] }} Units</span>
                    </div>
                    <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                        <div class="bg-amber-500 h-full" style="width: 45%"></div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 text-center">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Monthly Profit</p>
                    <h4 class="text-3xl font-black text-emerald-500">AED {{ number_format($stats['fleet_performance']['monthly_profit'], 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. Top Performing Vehicles --}}
    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm overflow-hidden">
        <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-6">Top Fleet Assets (MTD)</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-gray-100 dark:border-slate-800">
                        <th class="pb-3 px-2">Vehicle</th>
                        <th class="pb-3 px-2">Revenue</th>
                        <th class="pb-3 px-2">Maint.</th>
                        <th class="pb-3 px-2">Fuel</th>
                        <th class="pb-3 px-2">Net Profit</th>
                        <th class="pb-3 px-2 text-right">Margin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-800/50">
                    @foreach($stats['fleet_performance']['top_performing_vehicles'] as $vehicle)
                    <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="py-4 px-2">
                            <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $vehicle['vehicle'] }}</span>
                        </td>
                        <td class="py-4 px-2">
                            <span class="text-xs font-bold text-emerald-500">+AED {{ number_format($vehicle['revenue'], 2) }}</span>
                        </td>
                        <td class="py-4 px-2">
                            <span class="text-xs font-bold text-rose-400">-AED {{ number_format($vehicle['expenses']['maintenance'], 2) }}</span>
                        </td>
                        <td class="py-4 px-2">
                            <span class="text-xs font-bold text-rose-400">-AED {{ number_format($vehicle['expenses']['fuel'], 2) }}</span>
                        </td>
                        <td class="py-4 px-2">
                            <span class="text-sm font-black text-slate-900 dark:text-white">AED {{ number_format($vehicle['profit'], 2) }}</span>
                        </td>
                        <td class="py-4 px-2 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <span class="text-xs font-black text-slate-500">{{ $vehicle['margin_percentage'] }}%</span>
                                <div class="w-16 h-1 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                    <div class="bg-indigo-500 h-full" style="width: {{ min(100, max(0, $vehicle['margin_percentage'])) }}%"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueTrendChart').getContext('2d');

        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.2)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($stats['revenue_trend']['labels']),
                datasets: [{
                    label: 'Revenue',
                    data: @json($stats['revenue_trend']['data']),
                    borderColor: '#6366f1',
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#6366f1',
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
                            size: 12,
                            family: 'monospace'
                        },
                        bodyFont: {
                            weight: 'bold'
                        },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: (context) => 'AED ' + context.parsed.y.toLocaleString()
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            font: {
                                size: 10,
                                family: 'monospace'
                            },
                            color: '#64748b'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10,
                                family: 'monospace'
                            },
                            color: '#64748b'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush