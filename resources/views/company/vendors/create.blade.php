@extends('layouts.company')

@section('title', 'Register New Vendor')

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Register Vendor</h1>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto animate-in fade-in slide-in-from-bottom-2 duration-500">
    <form action="{{ route('company.vendors.store') }}" method="POST" class="space-y-6" x-data="{ openingBalance: 0 }">
        @csrf

        {{-- 1. Basic Intelligence --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center">
                <span class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-500 flex items-center justify-center mr-3">01</span>
                Vendor Basic Profile
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Vendor Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    @error('name') <p class="text-rose-500 text-[10px] mt-1 font-bold italic">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Vendor ERP Code <span class="text-rose-500">*</span></label>
                    <input type="text" name="vendor_code" value="{{ old('vendor_code') }}" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. VEN-001">
                    @error('vendor_code') <p class="text-rose-500 text-[10px] mt-1 font-bold italic">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Operational Status <span class="text-rose-500">*</span></label>
                    <select name="status" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="active">Active / Operational</option>
                        <option value="suspended">Suspended / Liquidated</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Primary Contact Person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- 2. Communication & Finance --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center">
                <span class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center mr-3">02</span>
                Logistics & Tax Payload
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Phone System</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email Inbox</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tax Registration Number (TRN)</label>
                    <input type="text" name="tax_number" value="{{ old('tax_number') }}"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">HQ City</label>
                    <input type="text" name="city" value="{{ old('city') }}"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Physical Address</label>
                    <textarea name="address" rows="3"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">{{ old('address') }}</textarea>
                </div>
            </div>
        </div>

        {{-- 3. Accounting Seed --}}
        <div class="bg-slate-900 dark:bg-slate-950 border border-slate-800 rounded-2xl p-6 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/10 rounded-full blur-3xl -mr-16 -mt-16"></div>

            <h2 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-6 flex items-center">
                <span class="w-8 h-8 rounded-lg bg-blue-500 text-white flex items-center justify-center mr-3 shadow-lg shadow-blue-500/20">03</span>
                Enterprise Accounting Initialization
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Opening Ledger Balance</label>
                    <input type="number" step="0.01" name="opening_balance" x-model="openingBalance"
                        class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-white focus:ring-2 focus:ring-blue-500">
                    <p class="text-[9px] text-slate-500 mt-2 italic">Seed value for starting balance. Cannot be modified after registration.</p>
                </div>

                <div x-show="openingBalance > 0" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <label class="block text-[10px] font-black uppercase tracking-widest mb-2 text-blue-400">Balance Vector Direction <span class="text-rose-500">*</span></label>
                    <select name="balance_direction" :required="openingBalance > 0"
                        class="w-full bg-slate-800/50 border border-blue-500/30 rounded-xl px-4 py-3 text-sm font-bold text-white focus:ring-2 focus:ring-blue-500 shadow-lg shadow-blue-500/5">
                        <option value="payable">Payable (We owe them)</option>
                        <option value="receivable">Receivable (They owe us)</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- 4. Footer Actions --}}
        <div class="flex items-center justify-between pt-6 border-t border-gray-100 dark:border-slate-800">
            <a href="{{ route('company.vendors.index') }}" class="text-xs font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 dark:hover:text-white transition-colors">Abort Mission</a>

            <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-xl shadow-blue-500/20 transition-all">
                Initialize Vendor Record
            </button>
        </div>
    </form>
</div>
@endsection