@extends('layouts.company')

@section('title', 'Rental Intelligence - #' . $rental->rental_number)

@section('header_title')
<div class="flex items-center space-x-3">
    <a href="{{ route('company.rentals.index') }}" class="p-2 bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <div class="flex items-center space-x-2">
        <div class="w-2 h-2 rounded-full bg-cyan-500"></div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Rental #{{ $rental->rental_number }}</h1>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-in fade-in slide-in-from-bottom-2 duration-500">

    @php
    $lockService = app(\App\Services\DataLockService::class);
    $isLocked = $lockService->isLocked($rental);
    @endphp

    @if($isLocked)
    <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-900/30 rounded-2xl flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-amber-500 text-white rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/20">
                <i class="bi bi-lock-fill text-lg"></i>
            </div>
            <div>
                <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Enterprise Data Lock Active</h3>
                <p class="text-[10px] text-amber-600 dark:text-amber-400 font-bold uppercase">{{ $lockService->lockReason($rental) }}</p>
            </div>
        </div>
        @can('override_data_lock')
        <span class="text-[9px] font-black text-amber-600 bg-amber-100 dark:bg-amber-800/50 px-3 py-1 rounded-lg uppercase tracking-widest">
            <i class="bi bi-shield-check mr-1"></i> Privilege: Can Override
        </span>
        @endcan
    </div>
    @endif

    {{-- 1. Tactical Summary Bar --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="p-4 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Status</p>
            @php
            $statusColors = [
            'draft' => 'text-slate-500',
            'confirmed' => 'text-blue-500',
            'active' => 'text-emerald-500',
            'completed' => 'text-purple-500',
            'cancelled' => 'text-rose-500',
            ];
            $color = $statusColors[$rental->status] ?? 'text-gray-500';
            @endphp
            <p class="text-lg font-black uppercase tracking-widest {{ $color }}">{{ $rental->status }}</p>
        </div>
        <div class="p-4 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Payment</p>
            <p class="text-lg font-black uppercase tracking-widest {{ $rental->payment_status == 'paid' ? 'text-emerald-500' : 'text-amber-500' }}">
                {{ $rental->payment_status }}
            </p>
        </div>
        <div class="p-4 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Matrix Value</p>
            <p class="text-lg font-black text-slate-900 dark:text-white uppercase">{{ number_format($rental->final_amount, 2) }} <span class="text-[8px] text-slate-400 tracking-tighter ml-1">AED</span></p>
        </div>
        <div class="p-4 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Target Client</p>
            <p class="text-lg font-bold text-slate-900 dark:text-white truncate">{{ $rental->customer->name }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Mission Details & Assets --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Mission Timeline --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center space-x-2">
                        <div class="w-1.5 h-4 bg-cyan-500 rounded-full"></div>
                        <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Mission Timeline</h2>
                    </div>
                </div>

                <div class="relative flex items-center justify-between">
                    <div class="absolute inset-x-0 top-1/2 h-0.5 bg-slate-100 dark:bg-slate-800 -translate-y-1/2"></div>

                    <div class="relative z-10 text-center bg-white dark:bg-slate-900 px-4">
                        <div class="w-10 h-10 rounded-full bg-cyan-500 flex items-center justify-center mx-auto mb-3 shadow-lg shadow-cyan-500/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-widest">Pickup Schedule</p>
                        <p class="text-xs font-bold text-slate-400 mt-1">{{ $rental->start_date->format('d M Y, H:i') }}</p>
                        <p class="text-[9px] text-cyan-500 font-bold mt-1 uppercase">{{ $rental->pickup_location }}</p>
                    </div>

                    <div class="relative z-10 text-center bg-white dark:bg-slate-900 px-4">
                        <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-widest">Duration</p>
                        <p class="text-xs font-bold text-slate-400 mt-1">{{ $rental->start_date->diffForHumans($rental->end_date, true) }}</p>
                    </div>

                    <div class="relative z-10 text-center bg-white dark:bg-slate-900 px-4">
                        <div class="w-10 h-10 rounded-full bg-slate-900 dark:bg-white flex items-center justify-center mx-auto mb-3 shadow-lg shadow-slate-900/10">
                            <svg class="w-5 h-5 text-white dark:text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-11V7" />
                            </svg>
                        </div>
                        <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-widest">Return Target</p>
                        <p class="text-xs font-bold text-slate-400 mt-1">{{ $rental->end_date->format('d M Y, H:i') }}</p>
                        <p class="text-[9px] text-slate-500 font-bold mt-1 uppercase">{{ $rental->dropoff_location ?: 'Same as Pickup' }}</p>
                    </div>
                </div>
            </div>

            {{-- Assets & Driver Intelligence --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Asset --}}
                <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center space-x-2 mb-6">
                        <div class="w-1.5 h-4 bg-purple-500 rounded-full"></div>
                        <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Deployed Asset</h2>
                    </div>

                    @if($rental->vehicle)
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center overflow-hidden">
                            @if($rental->vehicle->image_path)
                            <img src="{{ asset('storage/' . $rental->vehicle->image_path) }}" class="w-full h-full object-cover">
                            @else
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m4 0h1m-5 10h5m-5 4h5m2 0h2" />
                            </svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-black text-slate-900 dark:text-white uppercase">{{ $rental->vehicle->vehicle_number }}</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">{{ $rental->vehicle->make }} {{ $rental->vehicle->model }} ({{ $rental->vehicle->year }})</p>
                            <a href="{{ route('company.fleet.show', $rental->vehicle) }}" class="text-[8px] font-black text-cyan-500 uppercase tracking-widest mt-1 inline-block">View Asset Log →</a>
                        </div>
                    </div>
                    @else
                    <div class="p-4 bg-rose-50 dark:bg-rose-500/10 rounded-xl border border-rose-100 dark:border-rose-500/20">
                        <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-1">Alert: Assignment Missing</p>
                        <p class="text-[10px] text-rose-400 font-medium">Please assign a vehicle to confirm this mission.</p>
                    </div>
                    @endif
                </div>

                {{-- Driver --}}
                <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center space-x-2 mb-6">
                        <div class="w-1.5 h-4 bg-emerald-500 rounded-full"></div>
                        <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Field Agent (Driver)</h2>
                    </div>

                    @if($rental->driver)
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center overflow-hidden uppercase font-black text-slate-300 text-xl">
                            {{ substr($rental->driver->name, 0, 2) }}
                        </div>
                        <div>
                            <p class="text-xs font-black text-slate-900 dark:text-white uppercase">{{ $rental->driver->name }}</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">{{ $rental->driver->email }}</p>
                            <span class="text-[8px] bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded uppercase font-black tracking-tighter mt-1 inline-block">Active Duty</span>
                        </div>
                    </div>
                    @else
                    <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl text-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">No Agent Assigned / Self-Drive</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Intelligent Notes --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center space-x-2 mb-4">
                    <div class="w-1.5 h-4 bg-slate-400 rounded-full"></div>
                    <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Mission Intelligence (Notes)</h2>
                </div>
                <p class="text-xs font-bold text-slate-500 leading-relaxed">{{ $rental->notes ?: 'No special operational intelligence provided for this mission.' }}</p>
            </div>
        </div>

        {{-- Right: Status Control & Financial Matrix --}}
        <div class="space-y-6">

            {{-- Status Control Center --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center space-x-2 mb-6">
                    <div class="w-1.5 h-4 bg-blue-500 rounded-full"></div>
                    <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Lifecycle Matrix</h2>
                </div>

                <div class="space-y-2">
                    @if($rental->status == 'draft')
                    <form action="{{ route('company.rentals.transition', $rental) }}" method="POST">
                        @csrf
                        <input type="hidden" name="to_status" value="confirmed">
                        <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-blue-500/20 transition-all">
                            Confirm Mission
                        </button>
                    </form>
                    @endif

                    @if($rental->status == 'confirmed')
                    <form action="{{ route('company.rentals.transition', $rental) }}" method="POST">
                        @csrf
                        <input type="hidden" name="to_status" value="active">
                        <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-emerald-500/20 transition-all">
                            Deploy (Activate)
                        </button>
                    </form>
                    @endif

                    @if($rental->status == 'active')
                    <form action="{{ route('company.rentals.transition', $rental) }}" method="POST">
                        @csrf
                        <input type="hidden" name="to_status" value="completed">
                        <button type="submit" class="w-full py-3 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-purple-500/20 transition-all">
                            Finalize (Complete)
                        </button>
                    </form>
                    @endif

                    @if(in_array($rental->status, ['draft', 'confirmed', 'active']))
                    <form action="{{ route('company.rentals.transition', $rental) }}" method="POST" onsubmit="return confirm('Abort Mission?')">
                        @csrf
                        <input type="hidden" name="to_status" value="cancelled">
                        <button type="submit" class="w-full py-3 bg-white dark:bg-slate-800 text-rose-500 border border-rose-100 dark:border-rose-900/30 hover:bg-rose-50 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all mt-2">
                            Terminate (Cancel)
                        </button>
                    </form>
                    @endif

                    @if($rental->status == 'completed' || $rental->status == 'cancelled')
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl text-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Mission Concluded</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Financial Summary --}}
            <div class="bg-slate-900 text-white rounded-3xl p-6 shadow-xl relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-cyan-500/10 rounded-full blur-3xl"></div>

                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40 mb-6">Financial Summary</h3>

                <div class="space-y-3">
                    <div class="flex justify-between text-xs font-bold">
                        <span class="opacity-50 uppercase">Base Rate ({{ $rental->rate_type }})</span>
                        <span>{{ number_format($rental->rate_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-xs font-bold">
                        <span class="opacity-50 uppercase">Incentives (Discount)</span>
                        <span class="text-emerald-400">- {{ number_format($rental->discount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-xs font-bold">
                        <span class="opacity-50 uppercase">VAT (5%)</span>
                        <span>{{ number_format($rental->tax, 2) }}</span>
                    </div>
                    <div class="border-t border-white/10 my-4"></div>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-xs font-black uppercase tracking-widest opacity-60">Total Value</span>
                        <span class="text-2xl font-black text-cyan-400 tracking-tighter uppercase">
                            {{ number_format($rental->final_amount, 2) }} <span class="text-[10px] ml-0.5 opacity-50">AED</span>
                        </span>
                    </div>
                </div>

                <div class="mt-8 space-y-3">
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-2xl border border-white/5">
                        <span class="text-[10px] font-black uppercase tracking-widest opacity-50">Security Bond</span>
                        <span class="text-xs font-black uppercase">{{ number_format($rental->security_deposit, 2) }} AED</span>
                    </div>
                </div>

                @if($rental->payment_status !== 'paid')
                <a href="#" class="w-full mt-6 py-4 bg-white text-slate-900 rounded-2xl text-[10px] font-black uppercase tracking-widest flex items-center justify-center hover:scale-[1.02] transition-transform">
                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                    </svg>
                    Collect Funds
                </a>
                @endif
            </div>

            {{-- Status Log (Timeline) --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest mb-6">Status Matrix History</h3>
                <div class="space-y-6 relative before:absolute before:inset-y-0 before:left-3 before:w-0.5 before:bg-slate-100 dark:before:bg-slate-800">
                    @foreach($rental->statusLogs as $log)
                    <div class="relative pl-8">
                        <div class="absolute left-1.5 top-1 w-3 h-3 rounded-full bg-white dark:bg-slate-900 border-2 border-slate-300 dark:border-slate-700 -translate-x-1/2"></div>
                        <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-widest leading-none">{{ $log->to_status }}</p>
                        <p class="text-[9px] text-slate-400 font-bold mt-1 uppercase">{{ $log->created_at->format('d M, H:i') }} • {{ $log->user?->name ?: 'System' }}</p>
                        @if($log->reason)
                        <p class="text-[9px] text-slate-500 mt-1 italic">"{{ $log->reason }}"</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection