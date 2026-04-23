<div x-data="{ open: false }" class="fixed bottom-8 right-8 z-[100]" x-cloak>
    <!-- Action Menu (Tooltips/Labels) -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        @click.away="open = false"
        class="absolute bottom-20 right-0 flex flex-col items-end space-y-4 min-w-[200px]">
        @if(auth()->user()->role === 'company_admin' || auth()->user()->can('create-rental'))
        <a href="{{ route('company.rentals.create') }}" class="group flex items-center justify-end space-x-3">
            <span class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap shadow-xl">Create Rental</span>
            <div class="w-12 h-12 bg-cyan-500 text-white rounded-2xl shadow-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
            </div>
        </a>
        @endif

        @if(auth()->user()->role === 'company_admin' || auth()->user()->can('create-contract'))
        <a href="{{ route('company.rentals.contract') }}" class="group flex items-center justify-end space-x-3">
            <span class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap shadow-xl">Create Contract</span>
            <div class="w-12 h-12 bg-indigo-500 text-white rounded-2xl shadow-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
        </a>
        @endif

        @if(auth()->user()->role === 'company_admin' || auth()->user()->can('create-expense'))
        <a href="{{ route('company.expenses.create') }}" class="group flex items-center justify-end space-x-3">
            <span class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap shadow-xl">Add Expense</span>
            <div class="w-12 h-12 bg-rose-500 text-white rounded-2xl shadow-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </a>
        @endif

        @if(auth()->user()->role === 'company_admin' || auth()->user()->can('create-fine'))
        <a href="{{ route('company.fines.checker') }}" class="group flex items-center justify-end space-x-3">
            <span class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap shadow-xl">Fine Checker</span>
            <div class="w-12 h-12 bg-orange-500 text-white rounded-2xl shadow-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </a>

        <a href="{{ route('company.fines.create') }}" class="group flex items-center justify-end space-x-3">
            <span class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap shadow-xl">Record Fine</span>
            <div class="w-12 h-12 bg-amber-500 text-white rounded-2xl shadow-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </a>
        @endif
    </div>

    <!-- Main Trigger Button -->
    <button
        @click="open = !open"
        class="relative w-16 h-16 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-[2rem] shadow-2xl flex items-center justify-center transition-all duration-300 hover:scale-110 active:scale-95 group overflow-hidden">
        <!-- Animated Background Glow -->
        <div class="absolute inset-0 bg-gradient-to-tr from-cyan-500/20 to-violet-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

        <svg
            class="w-8 h-8 transition-transform duration-500 font-black"
            :class="open ? 'rotate-45' : 'rotate-0'"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
        </svg>
    </button>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>