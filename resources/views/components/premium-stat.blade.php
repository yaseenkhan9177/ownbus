@props([
    'title',
    'value',
    'icon' => null,
    'trend' => null,
    'trendUp' => true,
    'suffix' => '',
    'prefix' => '',
    'animationDuration' => 1500
])

<div class="bg-[#0A0F1E] border border-[#D4A847]/20 rounded-2xl p-6 relative overflow-hidden group hover:border-[#D4A847]/50 transition-all duration-500 shadow-xl shadow-black/40">
    <!-- Accent Gradient -->
    <div class="absolute -right-8 -top-8 w-24 h-24 bg-[#D4A847]/10 blur-3xl rounded-full group-hover:bg-[#D4A847]/20 transition-all duration-700"></div>
    <div class="absolute -left-8 -bottom-8 w-24 h-24 bg-[#D4A847]/5 blur-3xl rounded-full"></div>

    <div class="flex justify-between items-start relative z-10">
        <div>
            <p class="text-[11px] font-bold text-[#D4A847] uppercase tracking-[0.2em] mb-2">{{ $title }}</p>
            <div class="flex items-baseline" x-data="{ 
                current: 0, 
                target: {{ filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?: 0 }},
                duration: {{ $animationDuration }},
                prefix: '{{ $prefix }}',
                suffix: '{{ $suffix }}',
                is_currency: {{ str_contains($value, '$') || str_contains($prefix, '$') ? 'true' : 'false' }},
                init() {
                    let start = null;
                    const step = (timestamp) => {
                        if (!start) start = timestamp;
                        const progress = Math.min((timestamp - start) / this.duration, 1);
                        this.current = progress * this.target;
                        if (progress < 1) {
                            window.requestAnimationFrame(step);
                        }
                    };
                    window.requestAnimationFrame(step);
                }
            }">
                <span class="text-4xl font-extrabold text-white tracking-tight leading-none">
                    <span x-text="prefix"></span><span x-text="is_currency ? Math.floor(current).toLocaleString() : Math.floor(current)"></span><span x-text="suffix"></span>
                </span>
            </div>
        </div>
        
        @if($icon)
        <div class="p-3 bg-[#D4A847]/10 rounded-xl group-hover:scale-110 transition-transform duration-500 border border-[#D4A847]/20">
            <svg class="h-6 w-6 text-[#D4A847]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                {!! $icon !!}
            </svg>
        </div>
        @endif
    </div>

    @if($trend)
    <div class="mt-4 flex items-center space-x-2 relative z-10">
        <div class="flex items-center {{ $trendUp ? 'text-emerald-400' : 'text-rose-400' }} text-xs font-bold bg-white/5 px-2 py-1 rounded-lg">
            @if($trendUp)
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
            @else
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            @endif
            {{ $trend }}
        </div>
        <span class="text-slate-500 text-[10px] font-medium uppercase tracking-wider">vs last month</span>
    </div>
    @endif
</div>
