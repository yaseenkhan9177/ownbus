    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden mb-6">
        {{-- Header Section --}}
        <div class="p-6 border-b border-gray-50 dark:border-slate-800/50 bg-linear-to-r from-slate-50/50 to-white dark:from-slate-900 dark:to-slate-900/50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2.5 rounded-2xl bg-rose-500/10 text-rose-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">Executive Attention Engine</h3>
                        <p class="text-xs text-slate-500 font-medium">Unified Risk Center • Real-time Operational Awareness</p>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    @php
                    $criticalCount = count($risks['critical']);
                    $warningCount = count($risks['warning']);
                    $infoCount = count($risks['info']);
                    $totalCount = $criticalCount + $warningCount + $infoCount;
                    @endphp
                    <div class="px-4 py-2 rounded-2xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-black flex items-center space-x-2 shadow-lg shadow-slate-200 dark:shadow-none">
                        <span>⚠ Attention Required</span>
                        <span class="w-5 h-5 flex items-center justify-center bg-rose-500 text-white rounded-full text-[10px]">{{ $totalCount }}</span>
                    </div>
                </div>
            </div>

            {{-- Severity Summary Bar --}}
            <div class="mt-6 grid grid-cols-3 gap-4">
                <div class="p-3 rounded-2xl bg-rose-50 dark:bg-rose-500/10 border border-rose-100 dark:border-rose-500/20 text-center">
                    <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest">Critical</p>
                    <p class="text-xl font-black text-rose-600 dark:text-rose-400">{{ $criticalCount }}</p>
                </div>
                <div class="p-3 rounded-2xl bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20 text-center">
                    <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Warning</p>
                    <p class="text-xl font-black text-amber-600 dark:text-amber-400">{{ $warningCount }}</p>
                </div>
                <div class="p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 text-center">
                    <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Info</p>
                    <p class="text-xl font-black text-emerald-600 dark:text-emerald-400">{{ $infoCount }}</p>
                </div>
            </div>
        </div>

        {{-- Risk Items List --}}
        <div class="divide-y divide-gray-50 dark:divide-slate-800/50">
            @if($totalCount === 0)
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-900 dark:text-white">All Clear</h4>
                <p class="text-xs text-slate-500 mt-1">No critical risks requiring immediate attention.</p>
            </div>
            @else
            {{-- Critical Risks --}}
            @foreach($risks['critical'] as $risk)
            <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-all group border-l-4 border-rose-500">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-500 flex items-center justify-center font-black animate-pulse">
                            !
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-rose-500 uppercase tracking-widest">Critical Alert</p>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ $risk['title'] }}</h4>
                            <p class="text-[11px] text-slate-500 font-medium">Entity: <span class="text-slate-700 dark:text-slate-300 font-bold uppercase">{{ $risk['entity_name'] }}</span></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <a href="{{ $risk['action_url'] }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-[10px] font-black uppercase tracking-widest hover:scale-105 active:scale-95 transition-transform">
                            Action &rarr;
                        </a>
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Warning Risks --}}
            @foreach($risks['warning'] as $risk)
            <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-all group border-l-4 border-amber-500 bg-amber-50/20 dark:bg-transparent">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center font-black">
                            ?
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-amber-500 uppercase tracking-widest">Warning</p>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ $risk['title'] }}</h4>
                            <p class="text-[11px] text-slate-500 font-medium">Affected: <span class="text-slate-700 dark:text-slate-300 font-bold uppercase">{{ $risk['entity_name'] }}</span></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <a href="{{ $risk['action_url'] }}" class="text-[11px] font-black text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white transition-colors flex items-center">
                            Manage <span class="ml-1 opacity-0 group-hover:opacity-100 transition-opacity">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Info Risks --}}
            @foreach($risks['info'] as $risk)
            <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-all border-l-4 border-emerald-500">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center font-black">
                            i
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-emerald-500 uppercase tracking-widest">Insight</p>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ $risk['title'] }}</h4>
                            <p class="text-[11px] text-slate-500 font-medium">{{ $risk['entity_name'] }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <a href="{{ $risk['action_url'] }}" class="text-[11px] font-black text-slate-400 hover:text-emerald-500 transition-colors">
                            View
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
            @endif
        </div>

        {{-- Footer/War Room Link --}}
        @if($totalCount > 0)
        <div class="p-4 bg-slate-50 dark:bg-slate-800/30 text-center">
            <a href="{{ route('company.command-center') }}" class="text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-slate-900 dark:hover:text-white transition-colors">
                Enter Tactical War Room &rarr;
            </a>
        </div>
        @endif
    </div>