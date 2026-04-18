@extends('layouts.company')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Edit Profile: {{ $customer->name }}</h1>
            <p class="text-slate-400 mt-2 font-medium">Update customer details and credit status.</p>
        </div>
        <a href="{{ route('company.customers.show', $customer) }}" class="text-slate-400 hover:text-white transition font-bold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Dashboard
        </a>
    </div>

    @if($errors->any())
    <div class="bg-red-500/10 border border-red-500/50 p-4 rounded-xl">
        <div class="flex items-center gap-3 text-red-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="font-bold">Please correct the errors below</p>
        </div>
        <ul class="mt-2 list-disc list-inside text-xs text-red-500/80">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('company.customers.update', $customer) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Section 1: Identity & Status -->
        <div class="bg-[#0f172a] border border-slate-800 rounded-3xl overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-800 bg-slate-900/30 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-blue-600/20 text-blue-500 flex items-center justify-center text-sm">01</span>
                    Core Profile & Status
                </h3>
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">UID: {{ $customer->id }}</span>
            </div>
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Customer Status</label>
                        <select name="status" class="w-full bg-slate-900 border border-slate-700 rounded-xl py-3 px-4 text-white focus:outline-none focus:ring-2 focus:ring-blue-600/50 transition">
                            <option value="active" {{ old('status', $customer->status) == 'active' ? 'selected' : '' }}>🟢 Active / Reliable</option>
                            <option value="blacklisted" {{ old('status', $customer->status) == 'blacklisted' ? 'selected' : '' }}>🔴 Blacklisted / Blocked</option>
                            <option value="inactive" {{ old('status', $customer->status) == 'inactive' ? 'selected' : '' }}>⚪ Inactive</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Customer Type</label>
                        <select name="type" id="customerType" class="w-full bg-slate-900 border border-slate-700 rounded-xl py-3 px-4 text-white focus:outline-none focus:ring-2 focus:ring-blue-600/50 transition opacity-80 pointer-events-none">
                            <option value="individual" {{ old('type', $customer->type) == 'individual' ? 'selected' : '' }}>Individual Renter</option>
                            <option value="corporate" {{ old('type', $customer->type) == 'corporate' ? 'selected' : '' }}>Corporate / Enterprise</option>
                        </select>
                        <p class="text-[10px] text-slate-500 px-1">Type cannot be changed after registration.</p>
                    </div>
                </div>

                <!-- Individual Fields -->
                <div id="individualFields" class="grid grid-cols-1 md:grid-cols-2 gap-6 {{ old('type', $customer->type) != 'individual' ? 'hidden' : '' }}">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $customer->first_name) }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl py-3 px-4 text-white focus:outline-none focus:ring-2 focus:ring-blue-600/50 transition">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $customer->last_name) }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl py-3 px-4 text-white focus:outline-none focus:ring-2 focus:ring-blue-600/50 transition">
                    </div>
                </div>

                <!-- Corporate Fields -->
                <div id="corporateFields" class="space-y-2 {{ old('type', $customer->type) != 'corporate' ? 'hidden' : '' }}">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Company / Organization Name</label>
                    <input type="text" name="company_name" value="{{ old('company_name', $customer->company_name) }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl py-3 px-4 text-white focus:outline-none focus:ring-2 focus:ring-blue-600/50 transition">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl py-3 px-4 text-white focus:outline-none focus:ring-2 focus:ring-blue-600/50 transition">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Primary Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" required class="w-full bg-slate-900 border border-slate-700 rounded-xl py-3 px-4 text-white focus:outline-none focus:ring-2 focus:ring-blue-600/50 transition">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Financial & Credit (Critical) -->
        <div class="bg-[#0f172a] border border-slate-800 rounded-3xl overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-800 bg-slate-900/30">
                <h3 class="text-lg font-bold text-white flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-emerald-600/20 text-emerald-500 flex items-center justify-center text-sm">02</span>
                    Financial Safety Settings
                </h3>
            </div>
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Individual Credit Limit (AED)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-bold">AED</span>
                            <input type="number" step="0.01" name="credit_limit" value="{{ old('credit_limit', $customer->credit_limit) }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl py-3 pl-14 pr-4 text-white focus:outline-none focus:ring-2 focus:ring-blue-600/50 transition">
                        </div>
                    </div>
                    <div class="p-4 bg-slate-900/50 border border-slate-800 rounded-2xl flex flex-col justify-center">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Current Balance Liability</p>
                        <p class="text-xl font-black {{ $customer->current_balance > 0 ? 'text-amber-500' : 'text-slate-400' }}">AED {{ number_format($customer->current_balance, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Identity & Address -->
        <div class="bg-[#0f172a] border border-slate-800 rounded-3xl overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-800 bg-slate-900/30">
                <h3 class="text-lg font-bold text-white flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-indigo-600/20 text-indigo-500 flex items-center justify-center text-sm">03</span>
                    Additional Documents & Info
                </h3>
            </div>
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">ID Number</label>
                        <input type="text" name="national_id" value="{{ old('national_id', $customer->national_id) }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl py-3 px-4 text-white focus:outline-none focus:ring-2 focus:ring-blue-600/50 transition">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">License No</label>
                        <input type="text" name="driving_license_no" value="{{ old('driving_license_no', $customer->driving_license_no) }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl py-3 px-4 text-white focus:outline-none focus:ring-2 focus:ring-blue-600/50 transition">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Complete Address</label>
                    <textarea name="address" rows="3" class="w-full bg-slate-900 border border-slate-700 rounded-xl py-3 px-4 text-white focus:outline-none focus:ring-2 focus:ring-blue-600/50 transition">{{ old('address', $customer->address) }}</textarea>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Manager Notes</label>
                    <textarea name="notes" rows="2" class="w-full bg-slate-900 border border-slate-700 rounded-xl py-3 px-4 text-white focus:outline-none focus:ring-2 focus:ring-blue-600/50 transition">{{ old('notes', $customer->notes) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4 pb-12">
            <a href="{{ route('company.customers.show', $customer) }}" class="px-8 py-3 rounded-xl font-bold text-slate-400 hover:text-white transition">Cancel Changes</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-10 py-3 rounded-xl font-bold transition-all shadow-lg shadow-blue-900/20 active:scale-95">
                Save Updates
            </button>
        </div>
    </form>
</div>
@endsection