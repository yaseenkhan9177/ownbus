<div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest flex items-center">
                <span class="w-2 h-2 rounded-full bg-cyan-500 mr-2 animate-pulse"></span>
                Operational Timeline
            </h3>
            <p class="text-[10px] text-slate-500 mt-1 uppercase tracking-tight">Real-time audit trail of critical system events</p>
        </div>
        <a href="#" class="text-[10px] font-black text-cyan-500 uppercase hover:underline flex items-center">
            Detailed Log <i class="bi bi-chevron-right ml-1"></i>
        </a>
    </div>

    <div class="relative">
        {{-- Vertical Line --}}
        <div class="absolute left-4 top-0 bottom-0 w-px bg-slate-100 dark:bg-slate-800 ml-px"></div>

        <div class="space-y-6 relative">
            @forelse($data['timeline'] as $event)
            @php
            $severityColorClass = match($event->severity) {
            'critical' => 'bg-rose-500',
            'warning' => 'bg-amber-500',
            default => 'bg-cyan-500',
            };

            $severityLightClass = match($event->severity) {
            'critical' => 'bg-rose-500/10 text-rose-500 border-rose-500/20',
            'warning' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
            default => 'bg-cyan-500/10 text-cyan-500 border-cyan-500/20',
            };

            $icon = match($event->event_type) {
            'rental_created' => 'bi-plus-circle',
            'rental_completed' => 'bi-check-circle',
            'expense_recorded' => 'bi-receipt',
            'fine_added' => 'bi-exclamation-triangle',
            'fine_paid' => 'bi-cash-coin',
            'fine_recovered' => 'bi-arrow-left-right',
            'contract_activated'=> 'bi-file-earmark-check',
            'credit_blocked' => 'bi-slash-circle',
            default => 'bi-info-circle',
            };
            @endphp

            <div class="flex group">
                {{-- Icon Dot --}}
                <div class="relative z-10 flex items-center justify-center w-8 h-8 rounded-full {{ $severityColorClass }} text-white shadow-sm ring-4 ring-white dark:ring-slate-900 group-hover:scale-110 transition-transform duration-200">
                    <i class="bi {{ $icon }} text-xs"></i>
                </div>

                <div class="ml-4 flex-1">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100">{{ $event->title }}</h4>
                        <span class="text-[10px] font-medium text-slate-400">{{ $event->occurred_at->diffForHumans(['short' => true]) }}</span>
                    </div>

                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($event->meta as $key => $value)
                        @if(is_scalar($value))
                        <div class="px-2 py-0.5 rounded border text-[9px] font-bold uppercase tracking-tighter {{ $severityLightClass }}">
                            <span class="opacity-60 mr-1">{{ str_replace('_', ' ', $key) }}:</span>
                            {{ is_numeric($value) ? (str_contains($key, 'amount') ? 'AED ' . number_format($value, 0) : $value) : Str::limit($value, 15) }}
                        </div>
                        @endif
                        @endforeach

                        @if($event->performedBy)
                        <div class="flex items-center text-[10px] text-slate-500">
                            <div class="w-4 h-4 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-[8px] font-black mr-1 uppercase">
                                {{ substr($event->performedBy->name, 0, 1) }}
                            </div>
                            {{ $event->performedBy->name }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <p class="text-xs text-slate-500 italic">No operational events recorded for this cycle.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>