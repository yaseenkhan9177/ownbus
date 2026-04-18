@extends('layouts.company')

@section('title', 'Company Configuration - Workspace Control')

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="w-2 h-2 rounded-full bg-cyan-500"></div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Platform Preferences</h1>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-in fade-in slide-in-from-bottom-2 duration-500 max-w-5xl">
    
    <div class="bg-cyan-600 rounded-2xl p-6 text-white shadow-lg shadow-cyan-600/20 relative overflow-hidden">
        <div class="relative z-10 w-full md:w-3/4">
            <h2 class="text-xl font-black mb-2 tracking-tight">Workspace Preferences</h2>
            <p class="text-xs text-cyan-100 font-bold uppercase tracking-widest leading-relaxed">Customize essential functionality across your entire workspace. Defaults are provided by Super Admins. Overriding them here ensures your instance operates precisely to your needs.</p>
        </div>
        <div class="absolute -right-12 -bottom-12 opacity-10">
            <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.06-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.73,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.8,11.69,4.8,12s0.02,0.64,0.06,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.43-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.49-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z"></path></svg>
        </div>
    </div>

    <form method="POST" action="{{ route('company.settings.update') }}" class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm">
        @csrf

        <div x-data="{ activeTab: '{{ array_key_first($groupedSettings->toArray()) }}' }">
            <div class="border-b border-gray-50 dark:border-slate-800 px-6">
                <nav class="-mb-px flex space-x-6 overflow-x-auto">
                    @foreach($groupedSettings as $groupName => $settings)
                    <button type="button" @click="activeTab = '{{ $groupName }}'" :class="{ 'border-cyan-500 text-cyan-500': activeTab === '{{ $groupName }}', 'border-transparent text-slate-400 hover:text-slate-200': activeTab !== '{{ $groupName }}' }" class="py-4 px-1 border-b-2 font-black text-xs uppercase tracking-widest transition-all whitespace-nowrap">
                        {{ ucfirst(str_replace('_', ' ', $groupName)) }}
                    </button>
                    @endforeach
                </nav>
            </div>

            <div class="p-6 md:p-8">
                @foreach($groupedSettings as $groupName => $settings)
                <div x-show="activeTab === '{{ $groupName }}'" style="display: none;" class="space-y-8 animate-in fade-in slide-in-from-left-2 duration-300">
                    
                    @foreach($settings as $setting)
                    <div class="flex flex-col md:flex-row md:items-start gap-4">
                        <div class="w-full md:w-1/3">
                            <label class="block text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                            <p class="text-[10px] text-slate-400 mt-1 uppercase font-bold">Key: <span class="text-cyan-500 font-mono">{{ $setting->key }}</span></p>
                        </div>
                        <div class="w-full md:w-2/3">
                            @if($setting->type === 'string')
                                <input type="text" name="{{ $setting->key }}" value="{{ old($setting->key, $setting->value) }}" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                            @elseif($setting->type === 'text')
                                <textarea name="{{ $setting->key }}" rows="3" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">{{ old($setting->key, $setting->value) }}</textarea>
                            @elseif($setting->type === 'boolean')
                                <label class="relative inline-flex items-center cursor-pointer mt-1">
                                    <input type="checkbox" name="{{ $setting->key }}" value="1" class="sr-only peer" {{ old($setting->key, $setting->value) == '1' ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-cyan-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-500"></div>
                                    <span class="ml-3 text-xs font-black text-slate-600 dark:text-slate-400 uppercase tracking-widest">Enable Feature</span>
                                </label>
                            @endif
                        </div>
                    </div>
                    @if(!$loop->last)
                    <hr class="border-slate-50 dark:border-slate-800/50">
                    @endif
                    @endforeach

                </div>
                @endforeach
            </div>

            <div class="p-6 md:p-8 border-t border-slate-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20 text-right rounded-b-2xl">
                <button type="submit" class="px-6 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-cyan-500/20 transition-all">
                    Commit Changes
                </button>
            </div>
        </div>
    </form>
</div>
@endsection