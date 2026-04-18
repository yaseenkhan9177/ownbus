@extends('layouts.super-admin')

@section('title', 'Usage Analytics | SaaS Admin')

@section('header_title')
<div class="flex items-center space-x-4">
    <h1 class="text-xl font-bold text-slate-100 uppercase tracking-widest text-shadow-[0_0_10px_rgba(255,255,255,0.3)] flex items-center">
        <svg class="h-6 w-6 mr-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        Platform Utilization
    </h1>
</div>
@endsection

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Operational KPI Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Total Vehicles -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 p-5 shadow-lg relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 text-slate-800/30 group-hover:text-emerald-500/10 transition-colors duration-500">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M5 8h14l1.5 4.5V17a1 1 0 01-1 1h-1a1 1 0 01-1-1v-1H7v1a1 1 0 01-1 1H5a1 1 0 01-1-1v-4.5L5 8zm1.5-1.5L5 8h14l-1.5-1.5a1 1 0 00-.8-.5H7.3a1 1 0 00-.8.5zM6 14a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm12 0a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                </svg>
            </div>
            <div class="relative z-10">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Global Vehicles</p>
                <h3 class="text-3xl font-black text-emerald-400 tracking-tight">{{ number_format($stats['total_vehicles']) }}</h3>
                <p class="text-[10px] text-slate-500 mt-2 font-mono">ASSETS MANAGED</p>
            </div>
        </div>

        <!-- Total Drivers -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 p-5 shadow-lg relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 text-slate-800/30 group-hover:text-cyan-500/10 transition-colors duration-500">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="relative z-10">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Global Drivers</p>
                <h3 class="text-3xl font-black text-slate-200 tracking-tight">{{ number_format($stats['total_drivers']) }}</h3>
                <p class="text-[10px] text-slate-500 mt-2 font-mono">STAFF ACCOUNTS</p>
            </div>
        </div>

        <!-- Rentals This Month -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 p-5 shadow-lg relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 text-slate-800/30 group-hover:text-violet-500/10 transition-colors duration-500">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="relative z-10">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Rentals Processed</p>
                <h3 class="text-3xl font-black text-violet-400 tracking-tight">{{ number_format($stats['rentals_this_month']) }}</h3>
                <p class="text-[10px] text-slate-500 mt-2 font-mono">THIS CALENDAR MONTH</p>
            </div>
        </div>

        <!-- Fleet Density -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 p-5 shadow-lg relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Fleet Density</p>
                <h3 class="text-3xl font-black text-slate-200 tracking-tight">{{ $stats['avg_vehicles_per_company'] }}</h3>
                <p class="text-[10px] text-slate-500 mt-2 font-mono">AVG VEHICLES PER TENANT</p>
            </div>
        </div>

    </div>

    <!-- Rental Growth Velocity Chart -->
    <div class="bg-[#0f1524] rounded-xl border border-slate-800 p-6 shadow-lg">
        <div class="flex items-center justify-between mb-6 border-b border-slate-800 pb-4">
            <h3 class="text-sm font-semibold text-slate-300 uppercase tracking-wider">Traction: Completed Transactions (12M)</h3>
        </div>
        <div class="h-80">
            <canvas id="rentalGrowthChart"></canvas>
        </div>
    </div>

    <!-- Company Performance Leaderboard -->
    <div class="bg-[#0f1524] rounded-xl border border-slate-800 shadow-lg overflow-hidden flex flex-col">
        <div class="px-6 py-5 border-b border-slate-800 bg-slate-900/50 flex justify-between items-center shrink-0">
            <h3 class="text-sm font-semibold text-cyan-400 uppercase tracking-wider flex items-center">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Most Active Organizations (Top 10)
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900 border-b border-slate-800 text-xs uppercase tracking-wider text-slate-500">
                        <th class="px-6 py-3 font-semibold">Tenant Organization</th>
                        <th class="px-6 py-3 font-semibold text-right">Lifetime Rentals</th>
                        <th class="px-6 py-3 font-semibold text-right">Momentum (30d)</th>
                        <th class="px-6 py-3 font-semibold text-right">Registered Fleet</th>
                        <th class="px-6 py-3 font-semibold text-right">Registered Staff</th>
                        <th class="px-6 py-3 font-semibold">Active Tier</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50 text-sm">
                    @forelse($topCompanies as $index => $company)
                    <tr class="hover:bg-slate-800/30 transition-colors group">

                        <!-- Rank & Name -->
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-6 h-6 rounded flex items-center justify-center text-xs font-bold 
                                    {{ $index === 0 ? 'bg-amber-500/20 text-amber-500' : 
                                       ($index === 1 ? 'bg-slate-300/20 text-slate-300' : 
                                       ($index === 2 ? 'bg-orange-500/20 text-orange-400' : 'bg-slate-800 text-slate-500')) }}">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-200">{{ $company->name }}</div>
                                    <div class="text-[10px] text-slate-500 mt-1 font-mono">ID: {{ str_pad($company->id, 5, '0', STR_PAD_LEFT) }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Rentals -->
                        <td class="px-6 py-4 text-right">
                            <div class="font-black text-emerald-400 text-lg">{{ number_format($company->rentals_count) }}</div>
                        </td>

                        <!-- Growth -->
                        <td class="px-6 py-4 text-right">
                            @if($company->growth_percent >= 0)
                            <div class="font-bold text-emerald-500 flex items-center justify-end">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                </svg>
                                {{ $company->growth_percent }}%
                            </div>
                            @else
                            <div class="font-bold text-rose-500 flex items-center justify-end">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                                {{ abs($company->growth_percent) }}%
                            </div>
                            @endif
                        </td>

                        <!-- Vehicles -->
                        <td class="px-6 py-4 text-right">
                            <div class="font-bold text-slate-300">{{ number_format($company->vehicles_count) }}</div>
                            <div class="text-[10px] text-slate-500 mt-1 uppercase tracking-wider">UNITS</div>
                        </td>

                        <!-- Drivers/Staff -->
                        <td class="px-6 py-4 text-right">
                            <div class="font-bold text-slate-300">{{ number_format($company->drivers_count) }}</div>
                            <div class="text-[10px] text-slate-500 mt-1 uppercase tracking-wider">DRIVERS</div>
                        </td>

                        <!-- Plan -->
                        <td class="px-6 py-4">
                            @if($company->subscription && $company->subscription->plan)
                            <span class="inline-block px-2 py-1 rounded text-[10px] bg-slate-800 text-cyan-400 border border-slate-700 font-bold uppercase tracking-widest">
                                {{ $company->subscription->plan->name }}
                            </span>
                            @else
                            <span class="inline-block px-2 py-1 rounded text-[10px] bg-slate-800 text-slate-500 border border-slate-700 font-bold uppercase tracking-widest">
                                Unknown Tier
                            </span>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500 italic">No operational data recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- RENTAL TRANSACTION TRENDLINE ---
        const growthData = {
            !!json_encode($rentalGrowth) !!
        };
        const ctxGrowth = document.getElementById('rentalGrowthChart').getContext('2d');

        // Violet Gradient Fill
        let gradientGrowth = ctxGrowth.createLinearGradient(0, 0, 0, 300);
        gradientGrowth.addColorStop(0, 'rgba(139, 92, 246, 0.4)'); // Violet
        gradientGrowth.addColorStop(1, 'rgba(139, 92, 246, 0.0)');

        new Chart(ctxGrowth, {
            type: 'line',
            data: {
                labels: growthData.map(item => item.month),
                datasets: [{
                    label: 'Rentals Booked',
                    data: growthData.map(item => item.rentals),
                    borderColor: '#8b5cf6', // Tailwind Violet-500
                    borderWidth: 3,
                    backgroundColor: gradientGrowth,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#0f1524',
                    pointBorderColor: '#8b5cf6',
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
                                return parseInt(context.raw).toLocaleString() + ' Trips';
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
                            }
                        },
                        beginAtZero: true
                    }
                }
            }
        });

    });
</script>
@endpush