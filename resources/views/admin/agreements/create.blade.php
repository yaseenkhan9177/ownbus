@extends('layouts.super-admin')
@section('title', 'Upload New Agreement')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.agreements.index') }}" class="text-slate-400 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Upload New Agreement Version</h1>
            <p class="text-slate-400 text-sm mt-0.5">This will become the active agreement shown during new company registrations.</p>
        </div>
    </div>

    @if($errors->any())
    <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm space-y-1">
        @foreach($errors->all() as $error)
        <p>⚠️ {{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form action="{{ route('admin.agreements.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-[#0f172a] border border-slate-800 rounded-2xl p-6 space-y-6">
            {{-- Version Number --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Version Number</label>
                <input type="text" name="version" value="{{ old('version', '1.' . ((\App\Models\AgreementVersion::count()) + 1)) }}"
                    required placeholder="e.g. 1.0, 2.3, 2026-Q1"
                    class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                <p class="text-slate-500 text-xs mt-1">Use semantic versioning (e.g. 1.0, 1.1) or date-based (e.g. 2026-Q1)</p>
            </div>

            {{-- Content --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Agreement Content</label>
                <p class="text-slate-500 text-xs mb-3">You can paste plain text or HTML. HTML is rendered in the registration form.</p>
                <textarea name="content" rows="20" required
                    class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all font-mono text-sm resize-y"
                    placeholder="Paste your Terms & Conditions here...">{{ old('content') }}</textarea>
            </div>

            <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-4 text-amber-400 text-sm">
                <strong>⚡ Note:</strong> Uploading a new version will automatically deactivate all previous versions. The new version will immediately be shown to users registering.
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('admin.agreements.index') }}"
                class="px-6 py-3 text-slate-400 hover:text-white font-bold transition-colors">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-8 py-3 rounded-xl shadow-lg shadow-emerald-900/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Publish Agreement
            </button>
        </div>
    </form>
</div>
@endsection