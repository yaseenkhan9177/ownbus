@extends('layouts.super-admin')
@section('title', 'Edit Agreement v' . $agreement->version)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.agreements.index') }}" class="text-slate-400 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Edit Agreement <span class="text-blue-400">v{{ $agreement->version }}</span></h1>
            <p class="text-slate-400 text-sm mt-0.5">
                @if($agreement->active)
                <span class="text-emerald-400 font-semibold">● Currently Active</span> — changes take effect immediately for new registrations.
                @else
                This version is not currently active.
                @endif
            </p>
        </div>
    </div>

    @if($errors->any())
    <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm space-y-1">
        @foreach($errors->all() as $error)
        <p>⚠️ {{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form action="{{ route('admin.agreements.update', $agreement) }}" method="POST" class="space-y-6">
        @csrf @method('PUT')

        <div class="bg-[#0f172a] border border-slate-800 rounded-2xl p-6 space-y-6">
            {{-- Version (read-only) --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Version Number</label>
                <input type="text" value="v{{ $agreement->version }}" disabled
                    class="w-full px-4 py-3 bg-slate-900/30 border border-slate-800 rounded-xl text-slate-500 cursor-not-allowed">
                <p class="text-slate-600 text-xs mt-1">Version cannot be changed. Upload a new version instead.</p>
            </div>

            {{-- Active toggle --}}
            <div class="flex items-center justify-between bg-slate-900/50 p-4 rounded-xl border border-slate-700">
                <div>
                    <p class="text-white font-semibold text-sm">Set as Active Agreement</p>
                    <p class="text-slate-500 text-xs mt-0.5">Makes this the agreement shown during registration. Deactivates all others.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="active" value="1" class="sr-only peer" {{ $agreement->active ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                </label>
            </div>

            {{-- Content --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Agreement Content</label>
                <textarea name="content" rows="20" required
                    class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all font-mono text-sm resize-y">{{ old('content', $agreement->content) }}</textarea>
            </div>
        </div>

        <div class="flex justify-between gap-4">
            <a href="{{ route('admin.agreements.index') }}"
                class="px-6 py-3 text-slate-400 hover:text-white font-bold transition-colors">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-bold px-8 py-3 rounded-xl shadow-lg shadow-blue-900/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection