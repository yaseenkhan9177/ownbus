@extends('layouts.company')

@section('title', 'Multi-State Fine Checker | Governance')

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Multi-State Fine Checker</h1>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-in fade-in slide-in-from-bottom-2 duration-500 max-w-4xl mx-auto">
    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-8 shadow-sm">
        <h2 class="text-sm font-black text-rose-500 uppercase tracking-widest mb-4 border-l-2 border-rose-500 pl-2">Centralized Infraction Check</h2>
        <form action="{{ route('company.fines.record') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="space-y-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Select Asset <span class="text-rose-500">*</span></label>
                    <select name="vehicle_id" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 transition-all font-mono">
                        <option value="">-- Choose Asset --</option>
                        @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                            {{ $vehicle->vehicle_number }} - {{ $vehicle->registration_emirate ?? 'N/A' }} {{ $vehicle->plate_number ? '('.$vehicle->plate_number.')' : '' }}
                        </option>
                        @endforeach
                    </select>
                    @error('vehicle_id') <p class="text-[10px] font-bold text-rose-500 uppercase mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Traffic Authority <span class="text-rose-500">*</span></label>
                    <select name="authority" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 transition-all">
                        <option value="">Select Authority</option>
                        <optgroup label="UAE Emirates">
                            <option value="Dubai Police" {{ old('authority') == 'Dubai Police' ? 'selected' : '' }}>Dubai Police</option>
                            <option value="Abu Dhabi Police" {{ old('authority') == 'Abu Dhabi Police' ? 'selected' : '' }}>Abu Dhabi Police</option>
                            <option value="Sharjah Police" {{ old('authority') == 'Sharjah Police' ? 'selected' : '' }}>Sharjah Police</option>
                            <option value="RTA Dubai" {{ old('authority') == 'RTA Dubai' ? 'selected' : '' }}>RTA Dubai (Salik/Parking)</option>
                            <option value="Ajman Police" {{ old('authority') == 'Ajman Police' ? 'selected' : '' }}>Ajman Police</option>
                            <option value="RAK Police" {{ old('authority') == 'RAK Police' ? 'selected' : '' }}>RAK Police</option>
                            <option value="Fujairah Police" {{ old('authority') == 'Fujairah Police' ? 'selected' : '' }}>Fujairah Police</option>
                            <option value="UAQ Police" {{ old('authority') == 'UAQ Police' ? 'selected' : '' }}>UAQ Police</option>
                        </optgroup>
                        <optgroup label="Gulf Region">
                            <option value="Saudi Traffic Police" {{ old('authority') == 'Saudi Traffic Police' ? 'selected' : '' }}>Saudi Traffic Police (Muroor)</option>
                            <option value="Oman Royal Police" {{ old('authority') == 'Oman Royal Police' ? 'selected' : '' }}>Oman Royal Police</option>
                            <option value="Qatar Traffic" {{ old('authority') == 'Qatar Traffic' ? 'selected' : '' }}>Qatar Traffic Police</option>
                            <option value="Bahrain Traffic" {{ old('authority') == 'Bahrain Traffic' ? 'selected' : '' }}>Bahrain Traffic Directorate</option>
                            <option value="Kuwait Traffic" {{ old('authority') == 'Kuwait Traffic' ? 'selected' : '' }}>Kuwait Traffic Department</option>
                        </optgroup>
                    </select>
                    @error('authority') <p class="text-[10px] font-bold text-rose-500 uppercase mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Infraction / Ticket ID <span class="text-rose-500">*</span></label>
                    <input type="text" name="fine_number" value="{{ old('fine_number') }}" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 font-mono placeholder-slate-400" placeholder="e.g. TKT-9382103">
                    @error('fine_number') <p class="text-[10px] font-bold text-rose-500 uppercase mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Date of Infraction <span class="text-rose-500">*</span></label>
                    <input type="datetime-local" name="fine_date" value="{{ old('fine_date') }}" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500">
                    @error('fine_date') <p class="text-[10px] font-bold text-rose-500 uppercase mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Penalty Amount (AED) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-3 text-sm font-black text-rose-500 focus:ring-2 focus:ring-rose-500 placeholder-slate-400" placeholder="0.00">
                    @error('amount') <p class="text-[10px] font-bold text-rose-500 uppercase mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full bg-rose-600 hover:bg-rose-500 text-white rounded-xl px-6 py-4 text-xs font-black uppercase tracking-widest shadow-lg shadow-rose-500/20 transition-all flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span>Record & Log Infraction Into System</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
