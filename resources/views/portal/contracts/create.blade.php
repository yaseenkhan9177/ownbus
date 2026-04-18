@extends('layouts.company')

@section('title', 'Tactical Operations - New Contract')

@section('header_title')
<div class="flex items-center space-x-3">
    <div class="p-2 bg-slate-100 dark:bg-slate-800 text-slate-400 rounded-xl">
        <i class="bi bi-file-earmark-text-fill"></i>
    </div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">New Contract Configuration</h1>
</div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto pb-20 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <form action="{{ route('company.contracts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        {{-- 1. Asset & Entity Matrix --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="w-1.5 h-4 bg-cyan-500 rounded-full"></div>
                    <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Asset & Entity Matrix</h2>
                </div>
                <span class="text-[9px] font-black text-cyan-600 bg-cyan-100 px-3 py-1 rounded-lg uppercase tracking-widest">Phase 01: Core Linkage</span>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    {{-- Customer --}}
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 group-focus-within:text-cyan-500 transition-colors">Target Client</label>
                        <select name="customer_id" required class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 transition-all outline-none">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->company_name ?: $customer->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('customer_id') <p class="text-[10px] text-rose-500 font-bold mt-2 uppercase tracking-tight">{{ $message }}</p> @enderror
                    </div>

                    {{-- Vehicle --}}
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 group-focus-within:text-cyan-500 transition-colors">Deployed Asset</label>
                        <select name="vehicle_id" required class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 transition-all outline-none">
                            <option value="">Select Vehicle</option>
                            @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->vehicle_number }} — {{ $vehicle->model }}
                            </option>
                            @endforeach
                        </select>
                        @error('vehicle_id') <p class="text-[10px] text-rose-500 font-bold mt-2 uppercase tracking-tight">{{ $message }}</p> @enderror
                    </div>

                    {{-- Driver --}}
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 group-focus-within:text-cyan-500 transition-colors">Assigned Field Agent</label>
                        <select name="driver_id" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 transition-all outline-none">
                            <option value="">No Driver / Self-Drive</option>
                            @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                                {{ $driver->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('driver_id') <p class="text-[10px] text-rose-500 font-bold mt-2 uppercase tracking-tight">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Operational Timeline --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="w-1.5 h-4 bg-amber-500 rounded-full"></div>
                    <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Operational Timeline</h2>
                </div>
                <span class="text-[9px] font-black text-amber-600 bg-amber-100 px-3 py-1 rounded-lg uppercase tracking-widest">Phase 02: Temporal Matrix</span>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 group-focus-within:text-amber-500 transition-colors">Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all outline-none">
                        @error('start_date') <p class="text-[10px] text-rose-500 font-bold mt-2 uppercase tracking-tight">{{ $message }}</p> @enderror
                    </div>
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 group-focus-within:text-amber-500 transition-colors">Start Time</label>
                        <input type="time" name="start_time" value="{{ old('start_time', '09:00') }}" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all outline-none">
                    </div>
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 group-focus-within:text-amber-500 transition-colors">End Date</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}" required class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all outline-none">
                        @error('end_date') <p class="text-[10px] text-rose-500 font-bold mt-2 uppercase tracking-tight">{{ $message }}</p> @enderror
                    </div>
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 group-focus-within:text-amber-500 transition-colors">End Time</label>
                        <input type="time" name="end_time" value="{{ old('end_time', '18:00') }}" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all outline-none">
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Financial Agreement --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="w-1.5 h-4 bg-emerald-500 rounded-full"></div>
                    <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Financial Agreement</h2>
                </div>
                <span class="text-[9px] font-black text-emerald-600 bg-emerald-100 px-3 py-1 rounded-lg uppercase tracking-widest">Phase 03: Fiscal Matrix</span>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 group-focus-within:text-emerald-500 transition-colors">Base Value (Total)</label>
                        <div class="relative">
                            <input type="number" name="contract_value" value="{{ old('contract_value') }}" step="0.01" required class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl pl-5 pr-12 py-4 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                            <span class="absolute right-5 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400 uppercase">AED</span>
                        </div>
                        @error('contract_value') <p class="text-[10px] text-rose-500 font-bold mt-2 uppercase tracking-tight">{{ $message }}</p> @enderror
                    </div>
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 group-focus-within:text-emerald-500 transition-colors">Monthly Installment</label>
                        <div class="relative">
                            <input type="number" name="monthly_rate" value="{{ old('monthly_rate') }}" step="0.01" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl pl-5 pr-12 py-4 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                            <span class="absolute right-5 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400 uppercase">AED</span>
                        </div>
                    </div>
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 group-focus-within:text-emerald-500 transition-colors">Extra Surcharges</label>
                        <div class="relative">
                            <input type="number" name="extra_charges" value="{{ old('extra_charges', 0) }}" step="0.01" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl pl-5 pr-12 py-4 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                            <span class="absolute right-5 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400 uppercase">AED</span>
                        </div>
                    </div>
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 group-focus-within:text-emerald-500 transition-colors">Tactical Discount</label>
                        <div class="relative">
                            <input type="number" name="discount" value="{{ old('discount', 0) }}" step="0.01" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl pl-5 pr-12 py-4 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                            <span class="absolute right-5 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400 uppercase">AED</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-8 p-6 bg-slate-50 dark:bg-slate-800/50 rounded-3xl border border-slate-100 dark:border-slate-800">
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Billing Cycle</label>
                        <select name="billing_cycle" required class="w-full bg-white dark:bg-slate-900 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 transition-all outline-none shadow-sm">
                            <option value="monthly">Monthly Cycle</option>
                            <option value="quarterly">Quarterly Cycle</option>
                            <option value="yearly">Yearly Cycle</option>
                            <option value="custom">Custom Arrangement</option>
                        </select>
                    </div>
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Payment Due Date</label>
                        <input type="date" name="payment_due_date" value="{{ old('payment_due_date') }}" class="w-full bg-white dark:bg-slate-900 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 transition-all outline-none shadow-sm">
                    </div>
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Asset Renewal</label>
                        <div class="flex items-center space-x-3 py-4">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="auto_renew" value="1" class="sr-only peer" {{ old('auto_renew') ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-emerald-500"></div>
                                <span class="ml-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Auto-Renew Target</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Documentation & Compliance --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Terms & Conditions --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="w-1.5 h-4 bg-purple-500 rounded-full"></div>
                        <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Legal Framework</h2>
                    </div>
                </div>
                <div class="p-8 space-y-6">
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 group-focus-within:text-purple-500 transition-colors">Contract Terms</label>
                        <textarea name="terms" rows="4" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 transition-all outline-none resize-none" placeholder="Enter formal legal terms...">{{ old('terms') }}</textarea>
                    </div>
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 group-focus-within:text-purple-500 transition-colors">Payment Terms</label>
                        <textarea name="payment_terms" rows="2" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 transition-all outline-none resize-none" placeholder="e.g. 50% Advance, Balance on completion...">{{ old('payment_terms') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Documents --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="w-1.5 h-4 bg-indigo-500 rounded-full"></div>
                        <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Document Intelligence</h2>
                    </div>
                </div>
                <div class="p-8 text-center">
                    <div class="border-2 border-dashed border-slate-100 dark:border-slate-800 rounded-3xl p-10 group hover:border-indigo-400 transition-all cursor-pointer relative">
                        <input type="file" name="documents[]" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="w-16 h-16 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-indigo-500/10 group-hover:scale-110 transition-transform">
                            <i class="bi bi-cloud-arrow-up-fill text-2xl"></i>
                        </div>
                        <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest mb-2">Upload Tactical Assets</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight leading-relaxed">Signed Agreements, ID Verifications, <br>or Technical Documents (PDF/JPG/PNG)</p>
                    </div>
                    @error('documents.*') <p class="text-[10px] text-rose-500 font-bold mt-3 uppercase tracking-tight">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Tactical Intelligence (Notes) --}}
        <div class="bg-slate-900 text-white rounded-3xl p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl"></div>
            <div class="relative z-10 flex items-start space-x-6">
                <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-cyan-400">
                    <i class="bi bi-cpu text-2xl"></i>
                </div>
                <div class="flex-1 space-y-4">
                    <label class="block text-[10px] font-black text-cyan-400/60 uppercase tracking-[0.2em]">Operational Intelligence (Internal Notes)</label>
                    <textarea name="notes" rows="3" class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white placeholder:text-white/20 focus:ring-2 focus:ring-cyan-500 transition-all outline-none resize-none" placeholder="Any internal tactical data points...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Action Matrix --}}
        <div class="flex items-center justify-between pt-6 border-t border-slate-100 dark:border-slate-800">
            <a href="{{ route('company.contracts.index') }}" class="px-8 py-4 bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">
                Abort Configuration
            </a>
            <button type="submit" class="px-12 py-4 bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:scale-105 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-2xl transition-all">
                Authorize & Deploy Contract
            </button>
        </div>

    </form>
</div>
@endsection