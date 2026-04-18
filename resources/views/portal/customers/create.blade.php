@extends('layouts.company')

@section('content')
<div class="relative min-h-screen pb-20">
    <!-- Premium Page Glows -->
    <div class="absolute top-0 left-0 -translate-y-1/2 -translate-x-1/2 w-[600px] h-[600px] bg-blue-600/10 blur-[130px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-0 right-0 translate-y-1/2 translate-x-1/2 w-[500px] h-[500px] bg-indigo-600/10 blur-[110px] rounded-full pointer-events-none"></div>

    <div class="relative max-w-5xl mx-auto space-y-12">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-black uppercase tracking-widest mb-2">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                    </span>
                    Onboarding Module
                </div>
                <h1 class="text-4xl font-black text-white tracking-tight sm:text-5xl">
                    Register <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Customer</span>
                </h1>
                <p class="text-slate-400 text-lg max-w-2xl font-medium leading-relaxed">
                    Capture high-fidelity intelligence for your enterprise fleet network.
                </p>
            </div>
            <a href="{{ route('company.customers.index') }}" class="group inline-flex items-center gap-3 px-6 py-3 rounded-2xl bg-white/[0.03] border border-white/10 text-slate-400 hover:text-white hover:bg-white/[0.08] transition-all font-bold">
                <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Directory
            </a>
        </div>

        @if($errors->any())
        <div class="relative overflow-hidden bg-red-500/5 border border-red-500/20 p-6 rounded-[2rem] backdrop-blur-md animate-shake">
            <div class="absolute top-0 right-0 w-32 h-32 bg-red-500/5 blur-3xl rounded-full"></div>
            <div class="flex items-start gap-4 text-red-400 relative z-10">
                <div class="w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-black tracking-tight">Requirement Mismatch</p>
                    <ul class="mt-2 space-y-1">
                        @foreach($errors->all() as $error)
                        <li class="text-sm font-medium text-red-500/70 flex items-center gap-2">
                            <span class="w-1 h-1 rounded-full bg-red-500/40"></span>
                            {{ $error }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <form action="{{ route('company.customers.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Section 1: Customer Type & Basic Info -->
            <div class="group relative bg-white/[0.02] border border-white/5 rounded-[3rem] overflow-hidden backdrop-blur-sm transition-all hover:bg-white/[0.03] hover:border-white/10 shadow-2xl">
                <div class="px-10 py-8 border-b border-white/5 bg-white/[0.01] flex items-center justify-between">
                    <h3 class="text-xl font-black text-white flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500/20 to-indigo-500/20 border border-blue-500/30 flex items-center justify-center text-blue-400 font-black shadow-lg shadow-blue-500/10">01</div>
                        Core Identity
                    </h3>
                </div>
                <div class="p-10 space-y-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">Account Classification</label>
                            <div class="relative group/select">
                                <select name="type" id="customerType" class="w-full appearance-none bg-slate-900/50 border border-white/5 rounded-2xl py-4 px-6 text-white font-bold focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:bg-slate-900/80 transition-all cursor-pointer">
                                    <option value="individual" {{ old('type') == 'individual' ? 'selected' : '' }}>Individual Renter</option>
                                    <option value="corporate" {{ old('type') == 'corporate' ? 'selected' : '' }}>Enterprise Entity</option>
                                </select>
                                <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500 group-focus-within/select:text-blue-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">Assigned Operational Branch</label>
                            <div class="relative group/select">
                                <select name="branch_id" class="w-full appearance-none bg-slate-900/50 border border-white/5 rounded-2xl py-4 px-6 text-white font-bold focus:outline-none focus:ring-4 focus:ring-blue-600/20 focus:bg-slate-900/80 transition-all cursor-pointer">
                                    <option value="">Global / Headquarters</option>
                                    @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500 group-focus-within/select:text-blue-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Individual Fields -->
                    <div id="individualFields" class="grid grid-cols-1 md:grid-cols-2 gap-10 {{ old('type', 'individual') != 'individual' ? 'hidden' : '' }} animate-fade-in">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">Legal First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="e.g. Alexander"
                                class="w-full bg-slate-900/50 border border-white/5 rounded-2xl py-4 px-6 text-white font-bold focus:outline-none focus:ring-4 focus:ring-blue-600/20 transition-all">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">Legal Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="e.g. Hamilton"
                                class="w-full bg-slate-900/50 border border-white/5 rounded-2xl py-4 px-6 text-white font-bold focus:outline-none focus:ring-4 focus:ring-blue-600/20 transition-all">
                        </div>
                    </div>

                    <!-- Corporate Fields -->
                    <div id="corporateFields" class="space-y-3 {{ old('type') != 'corporate' ? 'hidden' : '' }} animate-fade-in">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">Registered Trade Name</label>
                        <div class="relative group/input">
                            <svg class="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500 group-focus-within/input:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="e.g. Global Logistics Corp"
                                class="w-full bg-slate-900/50 border border-white/5 rounded-2xl py-4 pl-16 pr-6 text-white font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/20 transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">Digital Correspondence</label>
                            <div class="relative group/input">
                                <svg class="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500 group-focus-within/input:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="email@enterprise.com"
                                    class="w-full bg-slate-900/50 border border-white/5 rounded-2xl py-4 pl-16 pr-6 text-white font-bold focus:outline-none focus:ring-4 focus:ring-blue-600/20 transition-all">
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">Contact Tele-line</label>
                            <div class="relative group/input">
                                <svg class="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500 group-focus-within/input:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="+971 5X XXX XXXX"
                                    class="w-full bg-slate-900/50 border border-white/5 rounded-2xl py-4 pl-16 pr-6 text-white font-bold focus:outline-none focus:ring-4 focus:ring-blue-600/20 transition-all">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Identity & Documents -->
            <div class="group relative bg-white/[0.02] border border-white/5 rounded-[3rem] overflow-hidden backdrop-blur-sm transition-all hover:bg-white/[0.03] hover:border-white/10 shadow-2xl">
                <div class="px-10 py-8 border-b border-white/5 bg-white/[0.01] flex items-center justify-between">
                    <h3 class="text-xl font-black text-white flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400 font-black shadow-lg shadow-indigo-500/10">02</div>
                        Legal Verification
                    </h3>
                </div>
                <div class="p-10 space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">Government Credential ID</label>
                            <input type="text" name="national_id" value="{{ old('national_id') }}" placeholder="Emirates ID / Passport"
                                class="w-full bg-slate-900/50 border border-white/5 rounded-2xl py-4 px-6 text-white font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/20 transition-all">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">Driver Sovereignty License</label>
                            <input type="text" name="driving_license_no" value="{{ old('driving_license_no') }}" placeholder="License Auth No."
                                class="w-full bg-slate-900/50 border border-white/5 rounded-2xl py-4 px-6 text-white font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/20 transition-all">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">Credential Expiration</label>
                            <input type="date" name="driving_license_expiry" value="{{ old('driving_license_expiry') }}"
                                class="w-full bg-slate-900/50 border border-white/5 rounded-2xl py-4 px-6 text-white font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/20 transition-all [color-scheme:dark]">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">Emergency Escalation Line</label>
                            <input type="text" name="alternate_phone" value="{{ old('alternate_phone') }}" placeholder="Alternate Number"
                                class="w-full bg-slate-900/50 border border-white/5 rounded-2xl py-4 px-6 text-white font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/20 transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Financial & Address -->
            <div class="group relative bg-white/[0.02] border border-white/5 rounded-[3rem] overflow-hidden backdrop-blur-sm transition-all hover:bg-white/[0.03] hover:border-white/10 shadow-2xl">
                <div class="px-10 py-8 border-b border-white/5 bg-white/[0.01] flex items-center justify-between">
                    <h3 class="text-xl font-black text-white flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 font-black shadow-lg shadow-emerald-500/10">03</div>
                        Fiscal Parameters
                    </h3>
                </div>
                <div class="p-10 space-y-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">Operational Credit Ceiling</label>
                        <div class="relative group/input">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-emerald-500 font-black text-sm">AED</div>
                            <input type="number" step="0.01" name="credit_limit" value="{{ old('credit_limit', 0) }}"
                                class="w-full bg-slate-900/50 border border-white/5 rounded-2xl py-4 pl-20 pr-6 text-white font-black text-xl focus:outline-none focus:ring-4 focus:ring-emerald-600/20 transition-all">
                        </div>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider px-1">Configure '0' for Standard Pre-paid Protocol.</p>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">Physical Anchorage / Address</label>
                        <textarea name="address" rows="3" placeholder="Locality, Building, Unit Number..."
                            class="w-full bg-slate-900/50 border border-white/5 rounded-[2rem] py-5 px-8 text-white font-bold focus:outline-none focus:ring-4 focus:ring-emerald-600/20 transition-all resize-none">{{ old('address') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">Metropolitan Hub</label>
                            <input type="text" name="city" value="{{ old('city') }}" placeholder="e.g. Dubai"
                                class="w-full bg-slate-900/50 border border-white/5 rounded-2xl py-4 px-6 text-white font-bold focus:outline-none focus:ring-4 focus:ring-emerald-600/20 transition-all">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">Sovereign State</label>
                            <input type="text" name="country" value="{{ old('country', 'United Arab Emirates') }}"
                                class="w-full bg-slate-900/50 border border-white/5 rounded-2xl py-4 px-6 text-white font-bold focus:outline-none focus:ring-4 focus:ring-emerald-600/20 transition-all">
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">OperationalIntelligence / Notes</label>
                        <textarea name="notes" rows="2" placeholder="Relevant background for fleet managers..."
                            class="w-full bg-slate-900/50 border border-white/5 rounded-[2rem] py-5 px-8 text-white font-medium focus:outline-none focus:ring-4 focus:ring-emerald-600/20 transition-all resize-none">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-6 pt-4">
                <button type="reset" class="px-8 py-4 rounded-2xl font-black text-slate-500 hover:text-white hover:bg-white/5 transition-all uppercase tracking-widest text-xs">
                    Clear Workspace
                </button>
                <button type="submit" class="group relative bg-blue-600 text-white px-12 py-5 rounded-2xl font-black transition-all hover:bg-blue-500 hover:shadow-[0_20px_40px_-10px_rgba(37,99,235,0.4)] hover:-translate-y-1 active:scale-95 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
                    <span class="relative flex items-center gap-3">
                        Finalize Registration
                        <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes shimmer {
        100% {
            transform: translateX(100%);
        }
    }

    .animate-shimmer {
        animation: shimmer 2s infinite;
    }

    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 0.4s ease-out forwards;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-5px);
        }

        75% {
            transform: translateX(5px);
        }
    }

    .animate-shake {
        animation: shake 0.5s ease-in-out;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('customerType');
        const individualFields = document.getElementById('individualFields');
        const corporateFields = document.getElementById('corporateFields');

        typeSelect.addEventListener('change', function() {
            if (this.value === 'corporate') {
                individualFields.classList.add('hidden');
                corporateFields.classList.remove('hidden');
                corporateFields.classList.add('animate-fade-in');
            } else {
                individualFields.classList.remove('hidden');
                individualFields.classList.add('animate-fade-in');
                corporateFields.classList.add('hidden');
            }
        });
    });
</script>
@endsection