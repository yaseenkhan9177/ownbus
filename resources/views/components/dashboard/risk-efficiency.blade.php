{{-- Risk Score + Fleet Efficiency Widget --}}
{{-- Usage: <x-dashboard.risk-score :score="$riskScore" :efficiency="$efficiency" /> --}}

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Executive Risk Scoreboard --}}
    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">Risk Intelligence</h3>
                <p class="text-base font-bold text-gray-900 dark:text-white mt-0.5">Company Health Score</p>
            </div>
            <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-tight
                {{ $score['color'] === 'emerald' ? 'bg-emerald-500/10 text-emerald-500' : ($score['color'] === 'amber' ? 'bg-amber-500/10 text-amber-500' : 'bg-rose-500/10 text-rose-500') }}">
                {{ $score['label'] }}
            </span>
        </div>

        {{-- Score Gauge --}}
        <div class="flex items-center space-x-5 mb-5">
            <div class="relative w-20 h-20 shrink-0">
                <svg class="w-full h-full transform -rotate-90">
                    <circle cx="40" cy="40" r="32" stroke="currentColor" stroke-width="7" fill="transparent" class="text-slate-100 dark:text-slate-800" />
                    <circle cx="40" cy="40" r="32" stroke="currentColor" stroke-width="7" fill="transparent"
                        stroke-dasharray="201"
                        stroke-dashoffset="{{ 201 - (201 * $score['score'] / 100) }}"
                        class="{{ $score['color'] === 'emerald' ? 'text-emerald-500' : ($score['color'] === 'amber' ? 'text-amber-500' : 'text-rose-500') }} transition-all duration-1000" />
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-lg font-black text-slate-900 dark:text-white leading-none">{{ $score['score'] }}</span>
                    <span class="text-[8px] font-bold text-slate-400 uppercase">Risk</span>
                </div>
            </div>

            <div class="flex-1 space-y-2">
                @foreach($score['factors'] as $factor)
                <div class="flex items-center justify-between">
                    <span class="text-[10px] text-slate-500 truncate max-w-[120px]">{{ $factor['label'] }}</span>
                    <div class="flex items-center space-x-2">
                        <div class="w-14 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-700
                                {{ $factor['penalty'] >= 60 ? 'bg-rose-500' : ($factor['penalty'] >= 30 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                style="width: {{ $factor['penalty'] }}%"></div>
                        </div>
                        <span class="text-[10px] font-bold {{ $factor['penalty'] >= 60 ? 'text-rose-500' : ($factor['penalty'] >= 30 ? 'text-amber-500' : 'text-emerald-500') }}">
                            {{ $factor['penalty'] }}%
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <p class="text-[9px] text-slate-400">Score 0–100: 🟢 0–30 Healthy · 🟡 31–65 Moderate · 🔴 66–100 High Risk</p>
    </div>

    {{-- Fleet Efficiency Metrics --}}
    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">Fleet Intelligence</h3>
                <p class="text-base font-bold text-gray-900 dark:text-white mt-0.5">Efficiency Metrics</p>
            </div>
            <span class="px-2 py-1 rounded text-[10px] font-black bg-indigo-500/10 text-indigo-500 uppercase tracking-tighter">
                {{ $efficiency['idle_pct'] }}% Idle
            </span>
        </div>

        <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Revenue / KM</p>
                <p class="text-base font-black text-slate-900 dark:text-white">
                    @if($efficiency['revenue_per_km'])
                    AED {{ number_format($efficiency['revenue_per_km'], 2) }}
                    @else
                    <span class="text-xs text-slate-400">N/A</span>
                    @endif
                </p>
            </div>
            <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Maint. / KM</p>
                <p class="text-base font-black text-slate-900 dark:text-white">
                    @if($efficiency['maintenance_cost_per_km'])
                    AED {{ number_format($efficiency['maintenance_cost_per_km'], 2) }}
                    @else
                    <span class="text-xs text-slate-400">N/A</span>
                    @endif
                </p>
            </div>
            <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total KMs</p>
                <p class="text-base font-black text-slate-900 dark:text-white">
                    {{ number_format($efficiency['total_rental_kms']) }}
                </p>
            </div>
            <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Idle Vehicles</p>
                <p class="text-base font-black {{ $efficiency['idle_pct'] > 30 ? 'text-amber-500' : 'text-emerald-500' }}">
                    {{ $efficiency['idle_count'] }}
                    <span class="text-xs font-normal text-slate-400">/ {{ $efficiency['total_vehicles'] }}</span>
                </p>
            </div>
        </div>

        {{-- Top vehicles by revenue --}}
        @if(count($efficiency['per_vehicle']) > 0)
        <div>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Top Performers</p>
            @foreach(array_slice($efficiency['per_vehicle']->toArray(), 0, 3) as $v)
            <div class="flex items-center justify-between py-1.5 border-b border-gray-50 dark:border-slate-800/50 last:border-0">
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 rounded bg-indigo-500/10 flex items-center justify-center text-[8px] font-black text-indigo-500">
                        {{ strtoupper(substr($v['vehicle_number'] ?? 'V', -2)) }}
                    </div>
                    <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300">{{ $v['vehicle_number'] ?? '—' }}</span>
                </div>
                <span class="text-[11px] font-black text-slate-700 dark:text-slate-100">
                    AED {{ number_format($v['total_revenue'] ?? 0, 0) }}
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>