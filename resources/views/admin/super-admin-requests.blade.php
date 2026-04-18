@extends('layouts.super-admin')

@section('title', 'Admin Requests')

@section('header_title')
<h1 class="text-xl font-bold text-slate-100 uppercase tracking-widest">Access Requests</h1>
@endsection

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-black text-white mb-2">Super Admin Registration Requests</h2>
    <p class="text-slate-400">Manage pending access requests for the Super Admin panel.</p>
</div>

<div class="bg-[#0f1524] rounded-2xl border border-slate-800 shadow-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-900/50 border-b border-slate-800">
                    <th class="py-4 px-6 text-xs font-bold text-slate-400 uppercase tracking-wider">Applicant Name</th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-400 uppercase tracking-wider">Email Details</th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-400 uppercase tracking-wider">Requested From IP</th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-400 uppercase tracking-wider">Requested Date</th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Status</th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($requests as $req)
                <tr class="hover:bg-slate-800/20 transition-colors group">
                    <td class="py-4 px-6">
                        <div class="text-sm font-bold text-slate-200">{{ $req->name }}</div>
                    </td>
                    <td class="py-4 px-6">
                        <div class="text-sm text-slate-400 font-mono">{{ $req->email }}</div>
                    </td>
                    <td class="py-4 px-6">
                        <div class="text-xs text-slate-500 font-mono">{{ $req->ip_address ?? 'Unknown' }}</div>
                    </td>
                    <td class="py-4 px-6">
                        <div class="text-xs text-slate-400">{{ $req->created_at->format('M d, Y H:i') }}</div>
                        <div class="text-[10px] text-slate-500">{{ $req->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="py-4 px-6 text-center">
                        @if($req->status === 'pending')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-500/10 text-amber-500 border border-amber-500/20">
                            Pending
                        </span>
                        @elseif($req->status === 'approved')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            Approved
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                            Rejected
                        </span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-right space-x-2">
                        @if($req->status === 'pending')
                        <div class="flex justify-end space-x-2">
                            <form action="{{ route('admin.requests.approve', $req->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to GRANT Super Admin access to this user? They will have full system control.');">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-lg text-xs font-bold uppercase transition-colors">
                                    Approve
                                </button>
                            </form>
                            <form action="{{ route('admin.requests.reject', $req->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to REJECT this request?');">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 rounded-lg text-xs font-bold uppercase transition-colors">
                                    Reject
                                </button>
                            </form>
                        </div>
                        @else
                        <span class="text-xs text-slate-600 font-mono">Processed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-slate-500">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-slate-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm uppercase tracking-widest font-bold">No Registration Requests Found</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection