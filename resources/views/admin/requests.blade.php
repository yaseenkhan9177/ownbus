@extends('layouts.super-admin')

@section('content')
<div class="min-h-screen bg-[#020617] py-12 px-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-white">Pending Requests</h1>
                <p class="text-slate-400 mt-1">Review and approve new company registrations.</p>
            </div>
            <div class="bg-blue-600/10 text-blue-400 px-4 py-2 rounded-lg font-bold border border-blue-500/20">
                Total Pending: {{ $pendingCompanies->count() }}
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-400 p-4 rounded-xl mb-6 text-sm font-semibold">
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-slate-900/50 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
            @if($pendingCompanies->isEmpty())
            <div class="p-12 text-center text-slate-500">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-lg font-medium">No pending requests at the moment.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-white/5 bg-white/5 text-xs uppercase tracking-wider text-slate-400">
                            <th class="p-6">Company</th>
                            <th class="p-6">Owner</th>
                            <th class="p-6">Contact</th>
                            <th class="p-6">Date</th>
                            <th class="p-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($pendingCompanies as $company)
                        <tr class="hover:bg-white/5 transition">
                            <td class="p-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center overflow-hidden">
                                        @if($company->logo_path)
                                        <img src="{{ Storage::url($company->logo_path) }}" class="w-full h-full object-cover">
                                        @else
                                        <span class="text-xs font-bold text-slate-500">N/A</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-white font-bold">{{ $company->name }}</p>
                                        <p class="text-xs text-slate-400">TRN: {{ $company->trn_number ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-6">
                                <p class="text-slate-300 font-medium">{{ $company->owner_name }}</p>
                            </td>
                            <td class="p-6">
                                <p class="text-sm text-slate-300">{{ $company->email }}</p>
                                <p class="text-xs text-slate-500">{{ $company->phone }}</p>
                            </td>
                            <td class="p-6">
                                <span class="text-sm text-slate-400">{{ $company->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="p-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.requests.approve', $company) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-green-500/10 text-green-400 hover:bg-green-500/20 border border-green-500/20 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                            Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.requests.reject', $company) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-red-500/10 text-red-400 hover:bg-red-500/20 border border-red-500/20 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection