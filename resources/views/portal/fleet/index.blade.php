@extends('layouts.company')

@section('title', 'Fleet Inventory - Logistics Control')

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="w-2 h-2 rounded-full bg-cyan-500"></div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Fleet Inventory</h1>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-in fade-in slide-in-from-bottom-2 duration-500">

    {{-- 1. Toolbar & Stats --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="p-3 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl shadow-sm">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Total Assets</p>
                <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $vehicles->total() }}</p>
            </div>
            <div class="p-3 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl shadow-sm">
                <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest leading-none mb-1">Operational</p>
                <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $vehicles->where('status', 'active')->count() }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <!-- Export UI -->
            <form action="{{ route('company.exports.store') }}" method="POST" class="flex items-center bg-slate-100 dark:bg-slate-800 rounded-xl p-1 shadow-inner">
                @csrf
                <input type="hidden" name="type" value="vehicles">
                <input type="hidden" name="filters[status]" value="{{ request('status') }}">
                <input type="hidden" name="filters[type]" value="{{ request('type') }}">
                <input type="hidden" name="filters[branch_id]" value="{{ request('branch_id') }}">
                <select name="format" class="text-[10px] bg-transparent border-none text-slate-700 dark:text-slate-300 font-bold uppercase py-1.5 focus:ring-0 w-20">
                    <option value="xlsx">EXCEL</option>
                    <option value="pdf">PDF</option>
                </select>
                <button type="submit" class="px-3 py-1.5 bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow rounded-lg text-xs font-black uppercase transition-all flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export
                </button>
            </form>

            @can('create', App\Models\Vehicle::class)
            <a href="{{ route('company.fleet.create') }}" class="px-4 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-cyan-500/20 transition-all flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                Register Asset
            </a>
            @endcan
        </div>
    </div>

    {{-- 2. Advanced Filters --}}
    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
        <form action="{{ route('company.fleet.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div class="lg:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-10 py-2.5 text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-cyan-500"
                    placeholder="Search Tag, VIN, or Model...">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <select name="status" class="bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500" onchange="this.form.submit()">
                <option value="">Operational Status</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>On Trip / Rented</option>
                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>In Service</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Offline</option>
            </select>

            <select name="branch_id" class="bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500" onchange="this.form.submit()">
                <option value="">All Branches</option>
                @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                @endforeach
            </select>

            <select name="type" class="bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500" onchange="this.form.submit()">
                <option value="">Asset Type</option>
                <option value="bus" {{ request('type') == 'bus' ? 'selected' : '' }}>Bus</option>
                <option value="luxury" {{ request('type') == 'luxury' ? 'selected' : '' }}>Luxury Suite</option>
                <option value="shuttle" {{ request('type') == 'shuttle' ? 'selected' : '' }}>Shuttle</option>
            </select>

            <div class="flex items-center space-x-2">
                <button type="submit" class="p-2.5 bg-slate-900 dark:bg-slate-800 text-white rounded-xl hover:bg-slate-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                </button>
                <a href="{{ route('company.fleet.index') }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-cyan-500">Reset</a>
            </div>
        </form>
    </div>

    {{-- 3. Assets Table --}}
    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                        <th class="py-4 px-6">Asset Identification</th>
                        <th class="py-4 px-4">Class</th>
                        <th class="py-4 px-4">Plate Info</th>
                        <th class="py-4 px-4">Deployment Status</th>
                        <th class="py-4 px-4">Assigned To</th>
                        <th class="py-4 px-4">Logistics (ODO)</th>
                        <th class="py-4 px-4">Yield (Gross)</th>
                        <th class="py-4 px-6 text-right">Command</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-800/50">
                    @forelse($vehicles as $vehicle)
                    <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 shrink-0 overflow-hidden border border-slate-200 dark:border-slate-700">
                                    @if($vehicle->image_path)
                                    <img src="{{ Storage::url($vehicle->image_path) }}" alt="{{ $vehicle->name }}" class="w-full h-full object-cover">
                                    @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('company.fleet.show', $vehicle) }}" class="text-sm font-black text-slate-900 dark:text-white group-hover:text-cyan-500 transition-colors uppercase tracking-tight">
                                        {{ $vehicle->vehicle_number }}
                                    </a>
                                    @if($vehicle->plate_number)
                                        <div class="text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700 mt-1 inline-block font-mono font-bold tracking-widest uppercase">
                                            {{ $vehicle->registration_emirate }} - {{ $vehicle->plate_number }}
                                        </div>
                                    @endif
                                    <div class="flex items-center text-[10px] text-slate-500 mt-1 space-x-2">
                                        <span class="font-bold">{{ $vehicle->name }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $vehicle->model }} ({{ $vehicle->year }})</span>
                                        @if($vehicle->color)
                                        <span>&bull;</span>
                                        <span class="flex items-center gap-1">
                                            <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div>{{ $vehicle->color }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $vehicle->type }}</span>
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $vehicle->seating_capacity }} Units</span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            @php
                                $flags = [
                                    'Dubai'=>'🇦🇪','Abu Dhabi'=>'🇦🇪','Sharjah'=>'🇦🇪','Ajman'=>'🇦🇪','RAK'=>'🇦🇪','Fujairah'=>'🇦🇪','UAQ'=>'🇦🇪',
                                    'Saudi Arabia'=>'🇸🇦','Kuwait'=>'🇰🇼','Bahrain'=>'🇧🇭','Qatar'=>'🇶🇦','Oman'=>'🇴🇲'
                                ];
                                $flag = $flags[$vehicle->plate_source] ?? '🏳️';
                            @endphp
                            @if($vehicle->plate_number_dp)
                            <div class="flex items-center space-x-2">
                                <span class="text-lg" title="{{ $vehicle->plate_source }}">{{ $flag }}</span>
                                <div class="flex flex-col">
                                    <span class="text-xs font-mono font-black text-slate-900 dark:text-white">{{ $vehicle->plate_code_dp ? $vehicle->plate_code_dp . ' ' : '' }}{{ $vehicle->plate_number_dp }}</span>
                                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">{{ $vehicle->plate_source }}</span>
                                </div>
                            </div>
                            @else
                            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">No Plate</span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            @php
                            $statusConfig = [
                            'available' => ['bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-500', 'dot' => 'bg-emerald-500', 'label' => 'Available'],
                            'rented' => ['bg' => 'bg-cyan-500/10', 'text' => 'text-cyan-500', 'dot' => 'bg-cyan-500', 'label' => 'Rented'],
                            'maintenance' => ['bg' => 'bg-amber-500/10', 'text' => 'text-amber-500', 'dot' => 'bg-amber-500', 'label' => 'In Service'],
                            'inactive' => ['bg' => 'bg-slate-500/10', 'text' => 'text-slate-500', 'dot' => 'bg-slate-500', 'label' => 'Offline'],
                            ];
                            $cfg = $statusConfig[$vehicle->status] ?? $statusConfig['inactive'];
                            @endphp
                            <div class="inline-flex items-center px-2 py-1 rounded-lg {{ $cfg['bg'] }} {{ $cfg['text'] }} space-x-1.5 border border-current/10">
                                <div class="w-1.5 h-1.5 rounded-full {{ $cfg['dot'] }} {{ $vehicle->status === 'available' ? 'animate-pulse' : '' }}"></div>
                                <span class="text-[10px] font-black uppercase tracking-widest">{{ $cfg['label'] }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            @if($vehicle->currentRental && $vehicle->currentRental->customer)
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-slate-900 dark:text-white uppercase truncate max-w-[120px]">{{ $vehicle->currentRental->customer->name }}</span>
                                <a href="{{ route('company.rentals.show', $vehicle->currentRental) }}" class="text-[9px] text-cyan-500 font-bold uppercase tracking-widest hover:underline">{{ $vehicle->currentRental->rental_number }}</a>
                            </div>
                            @else
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">--</span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-mono font-bold text-slate-900 dark:text-slate-100">{{ number_format($vehicle->current_odometer) }} <span class="text-[9px] text-slate-500">KM</span></span>
                                <div class="w-full h-1 bg-slate-100 dark:bg-slate-800 rounded-full mt-1.5 overflow-hidden">
                                    @php
                                    $servicePct = max(0, min(100, ($vehicle->current_odometer / max(1, $vehicle->next_service_odometer)) * 100));
                                    @endphp
                                    <div class="{{ $servicePct > 90 ? 'bg-rose-500' : 'bg-cyan-500' }} h-full transition-all duration-1000" style="width: <?= $servicePct ?>%"></div>
                                </div>
                                <span class="text-[9px] text-slate-500 mt-1 uppercase font-bold tracking-tighter">Svc Due: {{ number_format($vehicle->next_service_odometer) }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="text-xs font-black text-emerald-600 dark:text-emerald-400">AED {{ number_format($vehicle->total_revenue ?? 0, 0) }}</span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('company.fleet.show', $vehicle) }}" class="p-2 text-slate-400 hover:text-cyan-500 transition-colors" title="Intelligence">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </a>
                                @can('update', $vehicle)
                                <a href="{{ route('company.fleet.edit', $vehicle) }}" class="p-2 text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors" title="Reconfigure">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-400">
                                <svg class="w-12 h-12 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <p class="text-sm font-bold uppercase tracking-widest">No assets discovered in this sector</p>
                                <a href="{{ route('company.fleet.create') }}" class="text-cyan-500 hover:underline mt-2 text-xs font-black uppercase tracking-tighter">Add Intelligence Record &rarr;</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vehicles->hasPages())
        <div class="px-6 py-4 border-t border-gray-50 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/30">
            {{ $vehicles->links() }}
        </div>
        @endif
    </div>
</div>
@endsection