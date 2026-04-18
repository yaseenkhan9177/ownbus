@extends('layouts.company')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white mt-1">REGISTER_PERSONNEL</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Deploy New Tactical Asset to Fleet</p>
        </div>
        <a href="{{ route('company.drivers.index') }}" class="text-[10px] font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest transition-all">
            <i class="bi bi-arrow-left mr-1"></i> ABORT_REGISTRATION
        </a>
    </div>

    <form action="{{ route('company.drivers.store') }}" method="POST" class="space-y-6" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf

        <!-- Basic Info Section -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-500">
                    <i class="bi bi-person-badge"></i>
                </div>
                <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">BASIC_PERSONNEL_DATA</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">FIRST_NAME</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    @error('first_name') <p class="text-rose-500 text-[10px] font-bold uppercase">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">LAST_NAME</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    @error('last_name') <p class="text-rose-500 text-[10px] font-bold uppercase">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">CONTACT_PHONE</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    @error('phone') <p class="text-rose-500 text-[10px] font-bold uppercase">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">EMAIL_ADDRESS</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    @error('email') <p class="text-rose-500 text-[10px] font-bold uppercase">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">RESIDENTIAL_ADDRESS</label>
                    <textarea name="address" rows="2" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">{{ old('address') }}</textarea>
                    @error('address') <p class="text-rose-500 text-[10px] font-bold uppercase">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">CITY</label>
                    <input type="text" name="city" value="{{ old('city') }}" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    @error('city') <p class="text-rose-500 text-[10px] font-bold uppercase">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">NATIONAL_ID (CNIC/PASSPORT)</label>
                    <input type="text" name="national_id" value="{{ old('national_id') }}" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    @error('national_id') <p class="text-rose-500 text-[10px] font-bold uppercase">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- License Info Section -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-500">
                    <i class="bi bi-card-checklist"></i>
                </div>
                <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">COMPLIANCE_&_LICENSE</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">LICENSE_NUMBER</label>
                    <input type="text" name="license_number" value="{{ old('license_number') }}" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    @error('license_number') <p class="text-rose-500 text-[10px] font-bold uppercase">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">LICENSE_TYPE</label>
                    <select name="license_type" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all appearance-none">
                        <option value="light" {{ old('license_type') == 'light' ? 'selected' : '' }}>LIGHT_VEHICLE</option>
                        <option value="heavy" {{ old('license_type') == 'heavy' ? 'selected' : '' }}>HEAVY_VEHICLE</option>
                        <option value="bus" {{ old('license_type') == 'bus' ? 'selected' : '' }}>BUS_AUTHORIZATION</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">LICENSE_EXPIRY_DATE</label>
                    <input type="date" name="license_expiry_date" value="{{ old('license_expiry_date') }}" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    @error('license_expiry_date') <p class="text-rose-500 text-[10px] font-bold uppercase">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Employment Info Section -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <i class="bi bi-briefcase"></i>
                </div>
                <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">EMPLOYMENT_DETAILS</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">HIRE_DATE</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    @error('hire_date') <p class="text-rose-500 text-[10px] font-bold uppercase">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">BASE_SALARY</label>
                    <input type="number" step="0.01" name="salary" value="{{ old('salary') }}"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    @error('salary') <p class="text-rose-500 text-[10px] font-bold uppercase">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">COMMISSION_RATE (%)</label>
                    <input type="number" step="0.01" name="commission_rate" value="{{ old('commission_rate') }}"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    @error('commission_rate') <p class="text-rose-500 text-[10px] font-bold uppercase">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Emergency Contact Section -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-rose-500/10 flex items-center justify-center text-rose-500">
                    <i class="bi bi-telephone-outbound"></i>
                </div>
                <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">EMERGENCY_DATA</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">CONTACT_PERSON_NAME</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    @error('emergency_contact_name') <p class="text-rose-500 text-[10px] font-bold uppercase">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">CONTACT_PERSON_PHONE</label>
                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    @error('emergency_contact_phone') <p class="text-rose-500 text-[10px] font-bold uppercase">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">OPERATIONAL_NOTES</label>
                    <textarea name="notes" rows="3"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="flex items-center justify-end gap-4 pb-12">
            <button type="submit" x-bind:disabled="submitting" x-text="submitting ? 'DEPLOYING...' : 'INITIALIZE_DEPLOYMENT'" class="bg-blue-600 text-white px-8 py-4 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 hover:scale-105 transition-all shadow-lg shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                INITIALIZE_DEPLOYMENT
            </button>
        </div>
    </form>
</div>
@endsection