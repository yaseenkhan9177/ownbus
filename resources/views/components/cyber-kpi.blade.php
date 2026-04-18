@props([
'title',
'value',
'icon' => null,
'trend' => null,
'trendUp' => true,
'color' => 'cyan'
])

<div class="relative group bg-[#0f1524] border border-{{ $color }}-500/50 rounded-xl p-5 shadow-[0_0_15px_rgba(6,182,212,0.1)] transition-all duration-300 hover:shadow-[0_0_25px_rgba(6,182,212,0.3)]">
    <!-- Inner Glow -->
    <div class="absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent via-cyan-500/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>

    <!-- Top Row: Title and Icon -->
    <div class="flex justify-between items-start mb-2">
        <p class="text-xs font-bold text-slate-300 uppercase tracking-widest">{{ $title }}</p>
        @if($icon)
        <div class="text-cyan-400 drop-shadow-[0_0_8px_rgba(6,182,212,0.8)]">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                {!! $icon !!}
            </svg>
        </div>
        @endif
    </div>

    <!-- Value -->
    <h3 class="text-3xl font-bold text-white tracking-tight drop-shadow-md mb-3 font-sans">{{ $value }}</h3>

    <!-- Trend -->
    @if($trend)
    <p class="text-xs font-semibold {{ $trendUp ? 'text-cyan-400 drop-shadow-[0_0_5px_rgba(6,182,212,0.5)]' : 'text-slate-400' }}">
        {{ $trend }}
    </p>
    @endif
</div>