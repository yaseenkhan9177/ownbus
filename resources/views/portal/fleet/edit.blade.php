@extends('layouts.company')

@section('title', 'Reconfigure Asset - ' . $vehicle->vehicle_number)

@section('header_title')
<div class="flex items-center space-x-4">
    <a href="{{ route('company.fleet.show', $vehicle) }}" class="p-2 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl text-slate-400 hover:text-cyan-500 transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
    </a>
    <div class="flex flex-col">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Reconfigure Asset</h1>
        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Editing Record: {{ $vehicle->vehicle_number }}</span>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto animate-in fade-in slide-in-from-bottom-4 duration-500">
    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">

        <div class="p-4 bg-slate-50 border-b border-gray-100 dark:bg-slate-800/50 dark:border-slate-800">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 rounded-full bg-cyan-500 animate-pulse"></div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Asset Configuration Protocol</span>
                </div>
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">System Hash: {{ substr(md5($vehicle->id), 0, 8) }}</span>
            </div>
        </div>

        <form action="{{ route('company.fleet.update', $vehicle) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf
            @method('PUT')

            {{-- Section 1: Identification --}}
            <div class="space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-l-2 border-cyan-500 pl-2">Primary Identification</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Registration ID / VIN <span class="text-rose-500">*</span></label>
                        <input type="text" name="vehicle_number" value="{{ old('vehicle_number', $vehicle->vehicle_number) }}"
                            class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 transition-all">
                        @error('vehicle_number') <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Tactical Name (Alias) <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $vehicle->name) }}"
                            class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 transition-all">
                        @error('name') <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Section 2: Technical Specifications --}}
            <div class="space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-l-2 border-cyan-500 pl-2">Technical Archetype</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Manufacturer</label>
                        <input type="text" name="make" value="{{ old('make', $vehicle->make) }}" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Model variant</label>
                        <input type="text" name="model" value="{{ old('model', $vehicle->model) }}" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Color</label>
                        <input type="text" name="color" value="{{ old('color', $vehicle->color) }}" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500" placeholder="e.g. White">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Production Year</label>
                        <input type="number" name="year" value="{{ old('year', $vehicle->year) }}" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Asset Class</label>
                        <select name="type" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-xs font-black uppercase tracking-widest text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                            <option value="bus" {{ $vehicle->type == 'bus' ? 'selected' : '' }}>Heavy Transport (Bus)</option>
                            <option value="minibus" {{ $vehicle->type == 'minibus' ? 'selected' : '' }}>Medium (Minibus)</option>
                            <option value="luxury" {{ $vehicle->type == 'luxury' ? 'selected' : '' }}>Luxury Suite</option>
                            <option value="shuttle" {{ $vehicle->type == 'shuttle' ? 'selected' : '' }}>Tactical Shuttle</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Unit capacity</label>
                        <input type="number" name="seating_capacity" value="{{ old('seating_capacity', $vehicle->seating_capacity) }}" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Power Source</label>
                        <select name="fuel_type" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-xs font-black uppercase tracking-widest text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                            <option value="diesel" {{ $vehicle->fuel_type == 'diesel' ? 'selected' : '' }}>Diesel Fuel</option>
                            <option value="petrol" {{ $vehicle->fuel_type == 'petrol' ? 'selected' : '' }}>Premium Petrol</option>
                            <option value="electric" {{ $vehicle->fuel_type == 'electric' ? 'selected' : '' }}>Electric Drive</option>
                            <option value="hybrid" {{ $vehicle->fuel_type == 'hybrid' ? 'selected' : '' }}>Hybrid System</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Section 3: Status & Logistics --}}
            <div class="space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-l-2 border-cyan-500 pl-2">Logistics & Deployment Status</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Deployment Status</label>
                        <select name="status" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 font-black">
                            <option value="available" {{ $vehicle->status == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="rented" {{ $vehicle->status == 'rented' ? 'selected' : '' }}>On Trip / Rented</option>
                            <option value="maintenance" {{ $vehicle->status == 'maintenance' ? 'selected' : '' }}>In Service</option>
                            <option value="inactive" {{ $vehicle->status == 'inactive' ? 'selected' : '' }}>Offline</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Odometer (KM)</label>
                        <input type="number" name="current_odometer" value="{{ old('current_odometer', $vehicle->current_odometer) }}" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Service Delta (KM)</label>
                        <input type="number" name="next_service_odometer" value="{{ old('next_service_odometer', $vehicle->next_service_odometer) }}" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Operational Yield Rate (AED)</label>
                        <input type="number" step="0.01" name="daily_rate" value="{{ old('daily_rate', $vehicle->daily_rate) }}" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-sm font-black text-emerald-500 focus:ring-2 focus:ring-cyan-500">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Update Asset Visualization</label>
                        <input type="file" name="image" class="w-full text-xs font-bold text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-slate-900 file:text-white hover:file:bg-slate-800 transition-all cursor-pointer">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-6">
                <a href="{{ route('company.fleet.show', $vehicle) }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 dark:hover:text-white transition-colors">Discard Reconfig</a>
                <button type="submit" class="px-8 py-3 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-cyan-500/20 transition-all">
                    Apply Synthesis
                </button>
            </div>
        </form>
    </div>
</div>
@endsection