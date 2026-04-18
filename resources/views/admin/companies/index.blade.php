@extends('layouts.super-admin')

@section('title', 'Company Management | SaaS Admin')

@section('header_title')
<div class="flex flex-col md:flex-row md:items-center md:justify-between w-full">
    <h1 class="text-xl font-bold text-slate-100 uppercase tracking-widest text-shadow-[0_0_10px_rgba(255,255,255,0.3)]">
        Tenant Companies
    </h1>

    <!-- Action buttons -->
    <div class="mt-4 md:mt-0 flex space-x-3">
        <a href="{{ route('admin.companies.create') }}" class="inline-flex items-center px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white text-sm font-medium rounded-lg shadow-[0_0_15px_rgba(6,182,212,0.4)] transition-all">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New Tenant
        </a>
    </div>
</div>
@endsection

@section('content')
<div x-data="{
    licenseModalOpen: false,
    selectedCompanyId: null,
    selectedCompanyName: '',
    openLicenseModal(id, name) {
        this.selectedCompanyId = id;
        this.selectedCompanyName = name;
        this.licenseModalOpen = true;
    }
}" class="space-y-6 relative">
    <!-- Filters Row -->
    <div class="bg-[#0f1524] p-4 rounded-xl border border-slate-800 shadow-lg">
        <form method="GET" action="{{ route('admin.companies.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">

            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Search</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Company name, owner, email..." class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg pl-10 pr-4 py-2 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all text-sm placeholder-slate-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Plan Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Plan</label>
                <select name="plan_id" class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-4 py-2 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all text-sm">
                    <option value="">All Plans</option>
                    @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" {{ request('plan_id') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Account Status</label>
                <select name="status" class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-4 py-2 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all text-sm">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 text-cyan-400 border border-slate-700 hover:border-cyan-500/50 transition-all font-medium py-2 rounded-lg text-sm">
                    Apply Filters
                </button>
                @if(request()->anyFilled(['search', 'plan_id', 'status', 'date_from', 'date_to']))
                <a href="{{ route('admin.companies.index') }}" class="px-3 py-2 bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 border border-rose-500/20 rounded-lg transition-all" title="Clear Filters">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
                @endif
            </div>

        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-[#0f1524] rounded-xl border border-slate-800 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/50 border-b border-slate-800 text-xs uppercase tracking-wider text-slate-400">
                        <th class="px-6 py-4 font-semibold">Tenant Organization</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold">Current Plan</th>
                        <th class="px-6 py-4 font-semibold text-center">Vehicles</th>
                        <th class="px-6 py-4 font-semibold">Registered On</th>
                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50 text-sm">
                    @forelse($companies as $company)
                    <tr class="hover:bg-slate-800/30 transition-colors group">
                        <!-- Company & Owner -->
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center text-cyan-500 font-bold uppercase shadow-[0_0_10px_rgba(6,182,212,0.1)]">
                                    {{ substr($company->name, 0, 2) }}
                                </div>
                                <div class="ml-4">
                                    <div class="font-bold text-slate-200">{{ $company->name }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">
                                        {{ $company->owner_name ?? ($company->owner->name ?? 'Awaiting Owner Setup') }}
                                        &middot; {{ $company->email }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Master Company Status -->
                        <td class="px-6 py-4">
                            @if($company->status === 'active')
                            <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 uppercase tracking-widest shadow-[0_0_5px_rgba(16,185,129,0.2)]">Active</span>
                            @elseif($company->status === 'pending')
                            <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 uppercase tracking-widest shadow-[0_0_5px_rgba(245,158,11,0.2)]">Pending</span>
                            @else
                            <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20 uppercase tracking-widest shadow-[0_0_5px_rgba(225,29,72,0.2)]">Suspended</span>
                            @endif
                        </td>

                        <!-- Subscription & Plan -->
                        <td class="px-6 py-4">
                            @if($company->subscription && $company->subscription->plan)
                            <div class="font-medium text-cyan-400">{{ $company->subscription->plan->name }}</div>
                            <div class="text-[11px] font-mono mt-1 
                                    {{ $company->subscription->status === 'active' ? 'text-emerald-500' : 'text-amber-500' }}">
                                SUB: {{ strtoupper($company->subscription->status) }}
                            </div>
                            @else
                            <span class="text-slate-500 italic text-xs">No Active Subscription</span>
                            @endif
                        </td>

                        <!-- Resources/Vehicles -->
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-md bg-slate-800 text-slate-300 font-mono text-xs border border-slate-700">
                                {{ $company->vehicles_count }}
                            </span>
                        </td>

                        <!-- Dates -->
                        <td class="px-6 py-4">
                            <div class="text-slate-300">{{ $company->created_at->format('M d, Y') }}</div>
                            <div class="text-xs text-slate-500 font-mono mt-0.5">{{ $company->created_at->diffForHumans() }}</div>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-3">
                                <a href="{{ route('admin.companies.show', $company->id) }}" class="text-slate-400 hover:text-cyan-400 transition-colors" title="View Details">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>

                                <!-- Impersonate -->
                                @if($company->status === 'active')
                                <form method="POST" action="{{ route('admin.companies.impersonate', $company->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-slate-400 hover:text-amber-400 transition-colors" title="Login as Company (Impersonate)">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                        </svg>
                                    </button>
                                </form>
                                @endif

                                <!-- Manage License Modal Button -->
                                @if(in_array($company->status, ['active', 'suspended']))
                                <button type="button" @click="openLicenseModal('{{ $company->id }}', '{{ addslashes($company->name) }}')" class="text-slate-400 hover:text-indigo-400 transition-colors" title="Manage License Key / Subscription">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </button>
                                @endif

                                <!-- Suspend/Unsuspend & Approve -->
                                @if($company->status === 'pending')
                                <form method="POST" action="{{ route('admin.companies.approve', $company->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-slate-400 hover:text-emerald-400 transition-colors" title="Approve Tenant">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                </form>
                                @elseif($company->status === 'active')
                                <form method="POST" action="{{ route('admin.companies.toggle-status', $company->id) }}" class="inline" onsubmit="return confirm('Suspend this tenant? They will instantly lose access.');">
                                    @csrf
                                    <button type="submit" class="text-slate-400 hover:text-rose-400 transition-colors" title="Suspend Tenant">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                    </button>
                                </form>
                                @elseif($company->status === 'suspended')
                                <form method="POST" action="{{ route('admin.companies.toggle-status', $company->id) }}" class="inline" onsubmit="return confirm('Re-activate this suspended tenant?');">
                                    @csrf
                                    <button type="submit" class="text-slate-400 hover:text-emerald-400 transition-colors" title="Unsuspend Tenant">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-500">
                                <svg class="h-12 w-12 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <p class="text-sm font-medium">No companies found matching criteria.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($companies->hasPages())
        <div class="bg-slate-900/50 border-t border-slate-800 px-6 py-4">
            {{ $companies->withQueryString()->links() }}
        </div>
        @endif
    </div>

    <!-- Manage License Modal -->
    <div x-show="licenseModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Background overlay -->
        <div x-show="licenseModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="licenseModalOpen" @click.away="licenseModalOpen = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-2xl bg-[#0B1120] border border-slate-700 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                <!-- Modal Header -->
                <div class="bg-slate-800/50 px-6 py-4 border-b border-slate-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center" id="modal-title">
                        <svg class="h-5 w-5 mr-2 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        Issue License Key
                    </h3>
                    <button @click="licenseModalOpen = false" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form :action="`{{ url('admin/companies') }}/${selectedCompanyId}/grant-license`" method="POST">
                    @csrf
                    <div class="px-6 py-6 space-y-5">
                        <p class="text-sm text-slate-400">
                            You are manually issuing a subscription license for <span class="font-bold text-cyan-400" x-text="selectedCompanyName"></span>. This will bypass Stripe and immediately activate their account for the chosen duration.
                        </p>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Select Subscription Plan</label>
                            <select name="plan_id" required class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all font-medium">
                                <option value="" disabled selected>-- Choose a Plan --</option>
                                @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} (Up to {{ $plan->max_vehicles }} vehicles)</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">License Duration</label>
                            <select name="duration" required class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all font-medium">
                                <option value="week">1 Week (7 Days)</option>
                                <option value="month" selected>1 Month (30 Days)</option>
                                <option value="year">1 Year (365 Days)</option>
                            </select>
                        </div>

                        <div class="bg-indigo-500/10 border border-indigo-500/20 rounded-lg p-4 flex items-start mt-2">
                            <svg class="h-5 w-5 text-indigo-400 mt-0.5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-xs text-indigo-200">
                                This will override any existing active subscription for this tenant and reset their billing cycle according to the new duration you issue here.
                            </p>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-slate-800/50 px-6 py-4 border-t border-slate-700 flex justify-end space-x-3">
                        <button type="button" @click="licenseModalOpen = false" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg font-medium transition-colors text-sm">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg font-medium transition-colors shadow-[0_0_15px_rgba(79,70,229,0.4)] text-sm flex items-center">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                            Generate & Apply License
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection