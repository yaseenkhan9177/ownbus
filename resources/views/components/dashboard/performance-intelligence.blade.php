@props(['overview'])

<div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-50 dark:border-slate-800">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">Asset Performance Intelligence</h3>
                <p class="text-base font-bold text-gray-900 dark:text-white mt-0.5">Fleet Efficiency — {{ $overview['period_label'] }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Avg Margin</p>
                    <p class="text-sm font-black text-emerald-500">AED {{ number_format($overview['avg_margin_per_km'], 2) }}/km</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Fleet Utilization</p>
                <p class="text-lg font-black text-slate-900 dark:text-white">{{ number_format($overview['fleet_utilization_pct'], 1) }}%</p>
            </div>
            <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Avg Rev / KM</p>
                <p class="text-lg font-black text-cyan-600">AED {{ number_format($overview['avg_revenue_per_km'], 2) }}</p>
            </div>
            <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Underperforming</p>
                <p class="text-lg font-black text-amber-500">{{ count($overview['underperforming']) }} Units</p>
            </div>
            <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Negative Margin</p>
                <p class="text-lg font-black text-rose-500">{{ count($overview['negative_margin']) }} Units</p>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-gray-50 dark:border-slate-800">
                    <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Vehicle</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Rev/KM</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Cost/KM</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Margin/KM</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Util %</th>
                    <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">ROI Score</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
                @foreach(array_merge($overview['top_vehicles'], $overview['bottom_vehicles']) as $v)
                <tr class="hover:bg-slate-50/30 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-6 py-4">
                        <p class="text-xs font-black text-slate-900 dark:text-white">{{ $v['vehicle_number'] }}</p>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-tighter">{{ $v['name'] }}</p>
                    </td>
                    <td class="px-4 py-4 text-xs font-bold text-slate-600 dark:text-slate-400">AED {{ number_format($v['revenue_per_km'], 2) }}</td>
                    <td class="px-4 py-4 text-xs font-bold text-slate-600 dark:text-slate-400">AED {{ number_format($v['total_cost_per_km'], 2) }}</td>
                    <td class="px-4 py-4">
                        <span class="text-xs font-black {{ $v['gross_margin_per_km'] > 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                            AED {{ number_format($v['gross_margin_per_km'], 2) }}
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center space-x-2">
                            <div class="flex-1 h-1 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden min-w-[40px]">
                                <div class="h-full {{ $v['utilization_rate'] > 70 ? 'bg-emerald-500' : ($v['utilization_rate'] > 40 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ $v['utilization_rate'] }}%"></div>
                            </div>
                            <span class="text-[10px] font-bold text-slate-500">{{ number_format($v['utilization_rate'], 0) }}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-widest 
                            {{ $v['roi_score'] > 80 ? 'bg-emerald-500/10 text-emerald-600' : ($v['roi_score'] > 50 ? 'bg-amber-500/10 text-amber-600' : 'bg-rose-500/10 text-rose-600') }}">
                            {{ $v['roi_score'] }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>