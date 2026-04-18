@extends('layouts.company')

@section('title', 'Modify Vendor Intel - ' . $vendor->name)

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Modify Vendor Intelligence</h1>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto animate-in fade-in slide-in-from-bottom-2 duration-500">
    <form action="{{ route('company.vendors.update', $vendor) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- 1. Basic Intelligence --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center">
                <span class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-500 flex items-center justify-center mr-3">01</span>
                Vendor Configuration
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Vendor Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $vendor->name) }}" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    @error('name') <p class="text-rose-500 text-[10px] mt-1 font-bold italic">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Vendor ERP Code <span class="text-rose-500">*</span></label>
                    <input type="text" name="vendor_code" value="{{ old('vendor_code', $vendor->vendor_code) }}" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    @error('vendor_code') <p class="text-rose-500 text-[10px] mt-1 font-bold italic">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Operational Status <span class="text-rose-500">*</span></label>
                    <select name="status" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="active" {{ $vendor->status === 'active' ? 'selected' : '' }}>Active / Operational</option>
                        <option value="suspended" {{ $vendor->status === 'suspended' ? 'selected' : '' }}>Suspended / Liquidated</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Primary Contact Person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', $vendor->contact_person) }}"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- 2. Communication & Logistics --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center">
                <span class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center mr-3">02</span>
                Communication Payload
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Phone System</label>
                    <input type="text" name="phone" value="{{ old('phone', $vendor->phone) }}"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email Inbox</label>
                    <input type="email" name="email" value="{{ old('email', $vendor->email) }}"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tax Registration Number (TRN)</label>
                    <input type="text" name="tax_number" value="{{ old('tax_number', $vendor->tax_number) }}"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">HQ City</label>
                    <input type="text" name="city" value="{{ old('city', $vendor->city) }}"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Physical Address</label>
                    <textarea name="address" rows="3"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">{{ old('address', $vendor->address) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="flex items-center justify-between pt-6 border-t border-gray-100 dark:border-slate-800">
            <a href="{{ route('company.vendors.show', $vendor) }}" class="text-xs font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 dark:hover:text-white transition-colors">Abort Modifications</a>

            <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-xl shadow-blue-500/20 transition-all">
                Synchronize Vendor Record
            </button>
        </div>
    </form>
</div>
@endsection