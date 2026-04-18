@extends('layouts.company')

@section('title', 'Accounting Intelligence Dashboard')

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="p-2 bg-blue-600 rounded-lg shadow-sm">
        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
        </svg>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Financial Intelligence</h1>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Revenue -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-800">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-emerald-500/10 rounded-lg">
                    <svg class="h-6 w-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-[10px] font-black text-emerald-500 bg-emerald-500/10 px-2 py-1 rounded-full uppercase">Monthly Revenue</span>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white">AED {{ number_format($kpis['revenue_this_month'], 2) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-bold">Total earnings in {{ now()->format('F') }}</p>
        </div>

        <!-- Expenses -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-800">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-rose-500/10 rounded-lg">
                    <svg class="h-6 w-6 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-[10px] font-black text-rose-500 bg-rose-500/10 px-2 py-1 rounded-full uppercase">Monthly Expenses</span>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white">AED {{ number_format($kpis['expenses_this_month'], 2) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-bold">Total burn in {{ now()->format('F') }}</p>
        </div>

        <!-- Net Profit -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-800">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-indigo-500/10 rounded-lg">
                    <svg class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <span class="text-[10px] font-black text-indigo-500 bg-indigo-500/10 px-2 py-1 rounded-full uppercase">Net Profit</span>
            </div>
            <p class="text-2xl font-black {{ $kpis['net_profit'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                AED {{ number_format($kpis['net_profit'], 2) }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-bold">Bottom line efficiency</p>
        </div>

        <!-- Receivables -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-800">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-amber-500/10 rounded-lg">
                    <svg class="h-6 w-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-[10px] font-black text-amber-500 bg-amber-500/10 px-2 py-1 rounded-full uppercase">Receivables</span>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white">AED {{ number_format($kpis['receivables'], 2) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-bold">Outstanding customer payments</p>
        </div>
    </div>

    <!-- Secondary Counters -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Payables -->
        <div class="bg-slate-800 rounded-2xl p-6 shadow-sm text-white flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Outstanding Payables</p>
                <p class="text-xl font-black mt-1">AED {{ number_format($kpis['payables'], 2) }}</p>
            </div>
            <div class="p-3 bg-slate-700/50 rounded-xl">
                <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>

        <!-- Bank -->
        <div class="bg-blue-600 rounded-2xl p-6 shadow-sm text-white flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-blue-100 uppercase tracking-widest">Cash in Bank</p>
                <p class="text-xl font-black mt-1">AED {{ number_format($kpis['bank_balance'], 2) }}</p>
            </div>
            <div class="p-3 bg-blue-500/50 rounded-xl">
                <svg class="h-6 w-6 text-blue-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
            </div>
        </div>

        <!-- Hand -->
        <div class="bg-indigo-600 rounded-2xl p-6 shadow-sm text-white flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-indigo-100 uppercase tracking-widest">Cash in Hand</p>
                <p class="text-xl font-black mt-1">AED {{ number_format($kpis['cash_balance'], 2) }}</p>
            </div>
            <div class="p-3 bg-indigo-500/50 rounded-xl">
                <svg class="h-6 w-6 text-indigo-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Trend -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-800">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-widest mb-6 border-l-4 border-blue-600 pl-3">Revenue Intelligence (Last 6 Months)</h3>
            <div id="revenue-trend-chart" class="min-h-[300px]"></div>
        </div>

        <!-- Expense Breakdown -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-800">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-widest mb-6 border-l-4 border-rose-600 pl-3">Expense Distribution (This Month)</h3>
            <div id="expense-breakdown-chart" class="min-h-[300px]"></div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Revenue Trend Chart
        var revenueOptions = {
            series: [{
                name: 'Revenue',
                data: @json($revenueTrend['data'])
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: false
                },
                fontFamily: 'inherit'
            },
            colors: ['#2563eb'],
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [20, 100, 100]
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: @json($revenueTrend['labels']),
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return "AED " + val.toLocaleString();
                    }
                }
            }
        };

        var revenueChart = new ApexCharts(document.querySelector("#revenue-trend-chart"), revenueOptions);
        revenueChart.render();

        // Expense Breakdown Chart
        var expenseOptions = {
            series: @json($expenseBreakdown['data']),
            chart: {
                type: 'donut',
                height: 350,
                fontFamily: 'inherit'
            },
            labels: @json($expenseBreakdown['labels']),
            colors: ['#f43f5e', '#3b82f6', '#8b5cf6', '#f59e0b', '#10b981', '#6366f1'],
            legend: {
                position: 'bottom'
            },
            dataLabels: {
                enabled: false
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Expenses',
                                formatter: function(w) {
                                    return 'AED ' + w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString();
                                }
                            }
                        }
                    }
                }
            }
        };

        var expenseChart = new ApexCharts(document.querySelector("#expense-breakdown-chart"), expenseOptions);
        expenseChart.render();
    });
</script>
@endpush
@endsection