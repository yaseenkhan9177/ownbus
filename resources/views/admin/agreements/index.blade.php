@extends('layouts.super-admin')
@section('title', 'Agreement Management')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Agreement Management</h1>
            <p class="text-slate-400 text-sm mt-1">Manage the Terms & Conditions shown during company registration.</p>
        </div>
        <a href="{{ route('admin.agreements.create') }}"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-lg shadow-blue-900/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Upload New Version
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 font-semibold text-sm">
        ✅ {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 font-semibold text-sm">
        ⚠️ {{ session('error') }}
    </div>
    @endif

    {{-- Agreement Versions Table --}}
    <div class="bg-[#0f172a] border border-slate-800 rounded-2xl overflow-hidden">
        @if($agreements->isEmpty())
        <div class="text-center py-16 text-slate-500">
            <svg class="w-12 h-12 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="font-semibold">No agreements uploaded yet.</p>
            <a href="{{ route('admin.agreements.create') }}" class="text-blue-400 hover:underline text-sm mt-2 inline-block">Upload your first agreement →</a>
        </div>
        @else
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-900/60 text-slate-400 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4">Version</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Created</th>
                    <th class="px-6 py-4">Preview</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @foreach($agreements as $agreement)
                <tr class="hover:bg-slate-800/30 transition-colors">
                    <td class="px-6 py-4 font-bold text-white">v{{ $agreement->version }}</td>
                    <td class="px-6 py-4">
                        @if($agreement->active)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                            ACTIVE
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-700 text-slate-400">
                            Inactive
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-slate-400">{{ $agreement->created_at->format('d M Y, H:i') }}</td>
                    <td class="px-6 py-4 text-slate-400 max-w-xs truncate">{{ Str::limit(strip_tags($agreement->content), 60) }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.agreements.edit', $agreement) }}"
                                class="text-xs font-bold text-blue-400 hover:text-blue-300 px-3 py-1.5 rounded-lg hover:bg-blue-500/10 transition-all">
                                Edit
                            </a>
                            @if(!$agreement->active)
                            <form action="{{ route('admin.agreements.activate', $agreement) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs font-bold text-emerald-400 hover:text-emerald-300 px-3 py-1.5 rounded-lg hover:bg-emerald-500/10 transition-all">
                                    Set Active
                                </button>
                            </form>
                            <form action="{{ route('admin.agreements.destroy', $agreement) }}" method="POST"
                                onsubmit="return confirm('Delete this agreement version?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-rose-400 hover:text-rose-300 px-3 py-1.5 rounded-lg hover:bg-rose-500/10 transition-all">
                                    Delete
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection