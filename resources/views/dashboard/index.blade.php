@extends('layouts.company')

@section('title', 'Fleet Control Center')

@section('header_title')
<h1 class="text-xl font-bold text-gray-800">Fleet Control Center</h1>
@endsection

@section('content')
<div class="space-y-6 animate-in fade-in duration-500">

    {{-- 1. KPI Cards Row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-kpi-card
            title="Active Rentals"
            value="{{ $data['kpis']['active_rentals'] }}"
            isActive="true"
            icon='<path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />'
            color="blue" />

        <x-kpi-card
            title="Fleet Utilization"
            value="{{ $data['kpis']['utilization_rate'] }}%"
            icon='<path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />'
            color="{{ $data['kpis']['utilization_rate'] > 80 ? 'green' : ($data['kpis']['utilization_rate'] > 50 ? 'indigo' : 'orange') }}" />

        <x-kpi-card
            title="Revenue (Today)"
            value="{{ number_format($data['kpis']['revenue_today'], 2) }}"
            icon='<path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
            color="emerald" />

        <x-kpi-card
            title="Pending Invoices"
            value="{{ $data['kpis']['pending_invoices_count'] }}"
            trend="Needs Action"
            :trendUp="false"
            icon='<path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />'
            color="rose" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- 2. Rental Operations Snapshot --}}
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Rental Operations</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-slate-800 rounded-xl">
                    <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Upcoming (48h)</span>
                    <span class="text-lg font-bold text-blue-700 dark:text-blue-300">{{ $data['operations']['upcoming'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-slate-800 rounded-xl">
                    <span class="text-sm font-medium text-green-700 dark:text-green-300">Active Rentals</span>
                    <span class="text-lg font-bold text-green-700 dark:text-green-300">{{ $data['operations']['in_progress'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-orange-50 dark:bg-slate-800 rounded-xl">
                    <span class="text-sm font-medium text-orange-700 dark:text-orange-300">Pending Assignment</span>
                    <span class="text-lg font-bold text-orange-700 dark:text-orange-300">{{ $data['operations']['pending_assignment'] }}</span>
                </div>
            </div>

            {{-- Conflict Warning --}}
            @if(($data['operations']['conflicts'] ?? 0) > 0)
            <div class="mt-4 p-3 bg-red-50 border border-red-100 rounded-lg flex items-start space-x-3">
                <svg class="w-5 h-5 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div class="flex-1">
                    <p class="text-xs text-red-600 font-bold">
                        Conflict Alert
                    </p>
                    <p class="text-xs text-red-600">
                        {{ $data['operations']['conflicts'] }} scheduling conflicts detected.
                    </p>
                </div>
            </div>
            @endif
        </div>

        {{-- 3. Fleet Intelligence & Utilization Trend --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Fleet Utilization Trend</h3>
                <div class="flex space-x-4 text-sm">
                    <div class="flex flex-col">
                        <span class="text-xs text-gray-500">Idle Vehicles</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $data['utilization']['idle_count'] }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs text-gray-500">Utilization Rate</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $data['utilization']['rate'] }}%</span>
                    </div>
                </div>
            </div>
            <div class="h-[250px] relative">
                <div id="chartLoader" class="absolute inset-0 flex items-center justify-center bg-white/50 z-10">
                    <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <canvas id="utilizationChart"></canvas>
            </div>
        </div>

        {{-- 4. Financial Snapshot --}}
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Financial Snapshot</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 text-sm">Revenue (Total)</span>
                    <span class="font-bold text-gray-900 dark:text-white">${{ number_format($data['financials']['total_revenue'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 text-sm">Expenses (Fuel/Maint)</span>
                    <span class="font-bold text-red-600">-${{ number_format($data['financials']['total_expenses'], 2) }}</span>
                </div>
                <div class="h-px bg-gray-100 dark:bg-slate-800"></div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-900 dark:text-white font-medium">Net Profit</span>
                    <span class="font-bold text-green-600">${{ number_format($data['financials']['total_profit'], 2) }}</span>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    Margin: <span class="font-semibold {{ $data['financials']['margin_percent'] > 20 ? 'text-green-600' : 'text-orange-500' }}">{{ $data['financials']['margin_percent'] }}%</span>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-slate-800">
                    <p class="text-xs text-gray-400 uppercase tracking-widest mb-2">Receivables</p>
                    <div class="flex justify-between items-end">
                        <div>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($data['financials']['receivables']['pending_amount'], 2) }}</span>
                        </div>
                        <span class="text-xs text-gray-500 mb-1">{{ $data['financials']['receivables']['count'] }} Invoices</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. Alerts Panel --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 h-fit">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Action Center</h3>
            @if(empty($data['alerts']))
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <svg class="w-12 h-12 text-green-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-500">All systems operational. No active alerts.</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($data['alerts'] as $alert)
                <div class="flex items-start p-4 rounded-xl {{ ($alert['type'] ?? 'info') == 'danger' ? 'bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/30' : 'bg-orange-50 dark:bg-orange-900/10 border border-orange-100 dark:border-orange-900/30' }}">
                    <div class="shrink-0 mt-0.5">
                        @if(($alert['type'] ?? 'info') == 'danger')
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        @else
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        @endif
                    </div>
                    <div class="ml-3 flex-1">
                        <div class="flex justify-between">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $alert['message'] }}</p>
                            @if(isset($alert['category']))
                            <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-white/50 text-gray-500">{{ $alert['category'] }}</span>
                            @endif
                        </div>

                        @if(isset($alert['action_url']))
                        <a href="{{ $alert['action_url'] }}" class="mt-2 text-xs font-semibold hover:underline {{ ($alert['type'] ?? 'info') == 'danger' ? 'text-red-600 dark:text-red-400' : 'text-orange-600 dark:text-orange-400' }}">
                            Take Action &rarr;
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const utilCtx = document.getElementById('utilizationChart').getContext('2d');
        const loader = document.getElementById('chartLoader');

        // Fetch data via AJAX
        fetch('{{ route("company.dashboard.utilization-trend") }}')
            .then(response => response.json())
            .then(data => {
                loader.classList.add('hidden');

                new Chart(utilCtx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Active Vehicles',
                            data: data.values,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 2
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
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    display: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    maxTicksLimit: 7
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error loading chart:', error);
                loader.innerHTML = '<span class="text-red-500 text-sm">Failed to load chart data</span>';
            });
    });
</script>
@endpush