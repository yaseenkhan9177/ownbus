@extends('layouts.super-admin')

@section('title', 'System Broadcasts | SaaS Admin')

@section('header_title')
<div class="flex items-center space-x-4">
    <h1 class="text-xl font-bold text-slate-100 uppercase tracking-widest text-shadow-[0_0_10px_rgba(255,255,255,0.3)] flex items-center">
        <svg class="h-6 w-6 mr-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        System Broadcasts
    </h1>
</div>
@endsection

@section('content')
<div class="space-y-6">

    @if(session('error'))
    <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-4 rounded-lg flex items-center">
        <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    @if(session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-lg flex items-center">
        <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Compose Broadcast Panel -->
        <div class="lg:col-span-1">
            <div class="bg-[#0f1524] rounded-xl border border-slate-800 p-6 shadow-lg sticky top-6">
                <h3 class="text-lg font-semibold text-slate-100 border-b border-slate-800 pb-3 mb-6 uppercase tracking-wider flex items-center">
                    Deploy Announcement
                </h3>

                <form method="POST" action="{{ route('admin.broadcasts.store') }}" class="space-y-5">
                    @csrf

                    <!-- Target Role -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Target Audience Role</label>
                        <select name="target_role" required class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-4 py-2 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all font-medium text-sm">
                            <option value="all">Global (All Users)</option>
                            <option value="company_admin">Company Managers Only</option>
                            <option value="driver">Fleet Drivers Only</option>
                            <option value="customer">Subscribed Customers</option>
                        </select>
                    </div>

                    <!-- Target Company (Optional) -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Specific Tenant Scope</label>
                        <select name="company_id" class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-4 py-2 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all font-medium text-sm">
                            <option value="">-- Apply to All Tenants --</option>
                            @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-500 mt-1 italic">Leave blank to broadcast system-wide.</p>
                    </div>

                    <!-- Expiration -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Auto-Expire Announcement At</label>
                        <input type="datetime-local" name="expires_at" min="{{ now()->format('Y-m-d\TH:i') }}" value="{{ now()->addDays(7)->format('Y-m-d\TH:i') }}" class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-4 py-2 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all font-mono text-sm [color-scheme:dark]">
                    </div>

                    <!-- Message Payload -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Message Payload</label>
                        <textarea name="message" required rows="4" placeholder="Enter the critical alert or system maintenance notice..." class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-4 py-3 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all text-sm"></textarea>
                    </div>

                    <!-- Active Toggle -->
                    <div class="flex items-center pt-2">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-300 after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500 shadow-[0_0_10px_rgba(245,158,11,0.2)]"></div>
                            <span class="ml-3 text-xs font-bold text-slate-300 uppercase tracking-wider">Deploy Immediately</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-3 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-lg shadow-[0_0_15px_rgba(245,158,11,0.4)] transition-all uppercase tracking-widest text-sm mt-4">
                        Send Broadcast
                    </button>
                </form>
            </div>
        </div>

        <!-- Broadcast History Log -->
        <div class="lg:col-span-2">
            <div class="bg-[#0f1524] rounded-xl border border-slate-800 shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-800 bg-slate-900/50 flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-slate-300 uppercase tracking-wider">Transmission Log</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-900 border-b border-slate-800 text-xs uppercase tracking-wider text-slate-500">
                                <th class="px-6 py-3 font-semibold">Message & Scope</th>
                                <th class="px-6 py-3 font-semibold">Audience</th>
                                <th class="px-6 py-3 font-semibold">State</th>
                                <th class="px-6 py-3 font-semibold text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50 text-sm">
                            @forelse($broadcasts as $broadcast)
                            @php
                            $isExpired = $broadcast->expires_at && $broadcast->expires_at->isPast();
                            @endphp
                            <tr class="hover:bg-slate-800/30 transition-colors {{ $isExpired || !$broadcast->is_active ? 'opacity-60' : '' }}">
                                <!-- Message Data -->
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-200 line-clamp-2 max-w-sm">{{ $broadcast->message }}</div>
                                    <div class="text-xs text-slate-500 mt-1 flex items-center space-x-2">
                                        <span class="font-mono">ID: {{ str_pad($broadcast->id, 4, '0', STR_PAD_LEFT) }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $broadcast->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                </td>

                                <!-- Scope -->
                                <td class="px-6 py-4">
                                    <div class="flex flex-col space-y-1.5">
                                        @if($broadcast->target_role === 'all')
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] bg-slate-800 text-slate-300 border border-slate-700 font-bold uppercase tracking-widest w-max">Global</span>
                                        @elseif($broadcast->target_role === 'company_admin')
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] bg-purple-500/10 text-purple-400 border border-purple-500/20 font-bold uppercase tracking-widest w-max">Managers</span>
                                        @elseif($broadcast->target_role === 'driver')
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 font-bold uppercase tracking-widest w-max">Drivers</span>
                                        @else
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold uppercase tracking-widest w-max">Customers</span>
                                        @endif

                                        @if($broadcast->company)
                                        <span class="text-[11px] text-amber-500 font-medium truncate max-w-[120px]" title="{{ $broadcast->company->name }}">📍 {{ $broadcast->company->name }}</span>
                                        @else
                                        <span class="text-[11px] text-slate-500 font-medium">📍 All Tenants</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Status / Expiration -->
                                <td class="px-6 py-4">
                                    @if(!$broadcast->is_active)
                                    <span class="text-slate-500 font-bold text-xs uppercase tracking-wider">Halted</span>
                                    @elseif($isExpired)
                                    <span class="text-rose-500 font-bold text-xs uppercase tracking-wider">Expired</span>
                                    @else
                                    <span class="text-amber-400 font-bold text-xs uppercase tracking-wider shadow-[0_0_10px_rgba(245,158,11,0.2)]">Live Broadcast</span>
                                    @endif

                                    @if($broadcast->expires_at)
                                    <div class="text-[10px] font-mono text-slate-500 mt-1">Until: {{ $broadcast->expires_at->format('m/d H:i') }}</div>
                                    @endif
                                </td>

                                <!-- Action (Toggle Status) -->
                                <td class="px-6 py-4 text-right">
                                    <form method="POST" action="{{ route('admin.broadcasts.destroy', $broadcast->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        @if($broadcast->is_active)
                                        <button type="submit" class="px-3 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-500 border border-rose-500/30 text-xs font-bold uppercase rounded transition-colors" title="Halt Broadcast">Stop</button>
                                        @else
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-500 border border-emerald-500/30 text-xs font-bold uppercase rounded transition-colors" title="Reactivate Broadcast">Resume</button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500 italic">
                                    No transmission history found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($broadcasts->hasPages())
                <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/50">
                    {{ $broadcasts->links() }}
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection