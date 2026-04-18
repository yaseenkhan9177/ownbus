@props([
'title',
'value',
'icon' => null,
'trend' => null,
'trendUp' => true,
'isActive' => false,
'color' => 'blue'
])

<div class="group relative bg-white dark:bg-slate-900 rounded-3xl p-6 border-2 transition-all duration-300 
    {{ $isActive 
        ? 'border-blue-600 dark:border-blue-500 shadow-xl shadow-blue-500/10' 
        : 'border-transparent hover:border-gray-100 dark:hover:border-slate-800 shadow-sm hover:shadow-md' }}">

    <div class="flex flex-col space-y-4">
        <!-- Icon Bag -->
        <div class="flex items-center justify-center w-12 h-12 rounded-xl border border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/50 text-{{ $color }}-600 dark:text-{{ $color }}-400 transition-colors group-hover:bg-{{ $color }}-50 dark:group-hover:bg-{{ $color }}-900/20">
            @if($icon)
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icon !!}
            </svg>
            @endif
        </div>

        <!-- Content -->
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 tracking-tight">{{ $title }}</p>
            <div class="flex items-baseline mt-1 space-x-2">
                <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ $value }}</h3>
                @if($trend)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $trendUp ? 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400' }}">
                    {{ $trendUp ? '↑' : '↓' }} {{ $trend }}
                </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Active Indicator Dot (Optional) -->
    @if($isActive)
    <div class="absolute top-4 right-4 w-2 h-2 bg-blue-600 dark:bg-blue-400 rounded-full animate-pulse"></div>
    @endif
</div>