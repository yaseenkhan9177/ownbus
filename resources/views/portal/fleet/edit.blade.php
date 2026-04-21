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

            {{-- Section 1.5: UAE/Gulf Registration --}}
            <div class="space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-l-2 border-cyan-500 pl-2">State Registration (UAE/Gulf)</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Emirate / State</label>
                        <select id="registration_emirate" name="registration_emirate" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 transition-all">
                            <option value="">Select State</option>
                            <optgroup label="UAE Emirates">
                                <option value="DXB" {{ old('registration_emirate', $vehicle->registration_emirate) == 'DXB' ? 'selected' : '' }}>Dubai (DXB)</option>
                                <option value="AUH" {{ old('registration_emirate', $vehicle->registration_emirate) == 'AUH' ? 'selected' : '' }}>Abu Dhabi (AUH)</option>
                                <option value="SHJ" {{ old('registration_emirate', $vehicle->registration_emirate) == 'SHJ' ? 'selected' : '' }}>Sharjah (SHJ)</option>
                                <option value="AJM" {{ old('registration_emirate', $vehicle->registration_emirate) == 'AJM' ? 'selected' : '' }}>Ajman (AJM)</option>
                                <option value="RAK" {{ old('registration_emirate', $vehicle->registration_emirate) == 'RAK' ? 'selected' : '' }}>Ras Al Khaimah (RAK)</option>
                                <option value="FUJ" {{ old('registration_emirate', $vehicle->registration_emirate) == 'FUJ' ? 'selected' : '' }}>Fujairah (FUJ)</option>
                                <option value="UAQ" {{ old('registration_emirate', $vehicle->registration_emirate) == 'UAQ' ? 'selected' : '' }}>Umm Al Quwain (UAQ)</option>
                            </optgroup>
                            <optgroup label="Gulf Countries">
                                <option value="SAR" {{ old('registration_emirate', $vehicle->registration_emirate) == 'SAR' ? 'selected' : '' }}>Saudi Arabia - Riyadh (SAR)</option>
                                <option value="SAJ" {{ old('registration_emirate', $vehicle->registration_emirate) == 'SAJ' ? 'selected' : '' }}>Saudi Arabia - Jeddah (SAJ)</option>
                                <option value="SAD" {{ old('registration_emirate', $vehicle->registration_emirate) == 'SAD' ? 'selected' : '' }}>Saudi Arabia - Dammam (SAD)</option>
                                <option value="KWT" {{ old('registration_emirate', $vehicle->registration_emirate) == 'KWT' ? 'selected' : '' }}>Kuwait (KWT)</option>
                                <option value="BAH" {{ old('registration_emirate', $vehicle->registration_emirate) == 'BAH' ? 'selected' : '' }}>Bahrain (BAH)</option>
                                <option value="QAT" {{ old('registration_emirate', $vehicle->registration_emirate) == 'QAT' ? 'selected' : '' }}>Qatar - Doha (QAT)</option>
                                <option value="OMA" {{ old('registration_emirate', $vehicle->registration_emirate) == 'OMA' ? 'selected' : '' }}>Oman - Muscat (OMA)</option>
                            </optgroup>
                        </select>
                        @error('registration_emirate') <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Plate Category</label>
                        <select name="plate_category" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 transition-all">
                            <option value="">Select Category</option>
                            <option value="Private" {{ old('plate_category', $vehicle->plate_category) == 'Private' ? 'selected' : '' }}>Private (White plate)</option>
                            <option value="Commercial" {{ old('plate_category', $vehicle->plate_category) == 'Commercial' ? 'selected' : '' }}>Commercial/Transport (White plate - Bus)</option>
                            <option value="Government" {{ old('plate_category', $vehicle->plate_category) == 'Government' ? 'selected' : '' }}>Government (Blue plate)</option>
                            <option value="Diplomatic" {{ old('plate_category', $vehicle->plate_category) == 'Diplomatic' ? 'selected' : '' }}>Diplomatic (Green plate)</option>
                            <option value="Export" {{ old('plate_category', $vehicle->plate_category) == 'Export' ? 'selected' : '' }}>Export (Red plate)</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Plate Number / Code</label>
                        <input type="text" id="plate_number" name="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}"
                            class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-cyan-500 transition-all"
                            placeholder="e.g. A 12345">
                        <p id="plate_format_hint" class="text-[10px] font-bold text-slate-400 mt-1 uppercase"></p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Internal Vehicle Code (Unique)</label>
                        <div class="flex">
                            <input type="text" id="vehicle_code" name="vehicle_code" value="{{ old('vehicle_code', $vehicle->vehicle_code) }}"
                                class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-cyan-500 transition-all"
                                placeholder="e.g. BUS-DXB-001">
                            <button type="button" id="btn_generate_code" class="ml-2 px-3 py-2 bg-slate-100 dark:bg-slate-800 text-slate-500 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors text-xs font-bold" title="Auto Generate">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            </button>
                        </div>
                        @error('vehicle_code') <p class="text-[10px] font-bold text-rose-500 mt-1 uppercase">{{ $message }}</p> @enderror
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
                        <select id="vehicle_type" name="type" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-xs font-black uppercase tracking-widest text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const emirateSelect = document.getElementById('registration_emirate');
        const plateHint = document.getElementById('plate_format_hint');
        const plateInput = document.getElementById('plate_number');
        const generateCodeBtn = document.getElementById('btn_generate_code');
        const typeSelect = document.getElementById('vehicle_type');
        const codeInput = document.getElementById('vehicle_code');

        const hints = {
            'DXB': 'Dubai Format: A 12345 or AA 1234',
            'AUH': 'Abu Dhabi Format: 1 12345',
            'SHJ': 'Sharjah Format: 1 12345',
            'AJM': 'Ajman Format: A 12345',
            'RAK': 'RAK Format: A 12345',
            'FUJ': 'Fujairah Format: A 12345',
            'UAQ': 'UAQ Format: A 12345',
            'SAR': 'Riyadh Format: ABC 1234',
            'SAJ': 'Jeddah Format: ABC 1234',
            'SAD': 'Dammam Format: ABC 1234',
            'KWT': 'Kuwait Format: 12 12345',
            'BAH': 'Bahrain Format: 12345',
            'QAT': 'Qatar Format: 123456',
            'OMA': 'Oman Format: A 1234 or AB 1234'
        };

        emirateSelect.addEventListener('change', function() {
            plateHint.textContent = hints[this.value] || '';
        });

        generateCodeBtn.addEventListener('click', function() {
            const emirate = emirateSelect.value || 'XXX';
            const typeValue = typeSelect.value || 'BUS';
            const typeMap = {
                'bus': 'BUS',
                'minibus': 'MBUS',
                'luxury': 'LUX',
                'shuttle': 'SHT'
            };
            const prefix = typeMap[typeValue] || typeValue.toUpperCase();
            const randomId = Math.floor(Math.random() * 900) + 100; // 100 to 999
            
            codeInput.value = `${prefix}-${emirate}-${randomId}`;
        });
        
        // Trigger on load
        if(emirateSelect.value) {
            plateHint.textContent = hints[emirateSelect.value] || '';
        }
    });
</script>
@endpush
@endsection