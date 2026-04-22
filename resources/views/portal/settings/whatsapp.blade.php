@extends('layouts.company')

@section('title', 'WhatsApp Notification Settings')

@section('header_title')
<div class="flex items-center space-x-4">
    <div class="flex flex-col">
        <h1 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight leading-none">WhatsApp Notifications</h1>
        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Configure automated alerts</span>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6 max-w-4xl animate-in fade-in slide-in-from-bottom-4 duration-700">
    @if(session('success'))
    <div class="p-4 bg-emerald-500/10 border border-emerald-500/50 rounded-xl flex items-center space-x-3">
        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="p-4 bg-rose-500/10 border border-rose-500/50 rounded-xl flex items-center space-x-3">
        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="text-sm font-bold text-rose-600 dark:text-rose-400">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('company.settings.whatsapp.update') }}" class="p-8">
            @csrf
            
            <div class="flex items-center space-x-3 mb-8 pb-4 border-b border-gray-100 dark:border-slate-800">
                <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">WhatsApp Configuration</h2>
                    <p class="text-[10px] text-slate-500 font-bold uppercase mt-1">Set your primary number and toggle system notifications.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                {{-- Left Col: Primary Info --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">WhatsApp Number</label>
                        <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', $settings->whatsapp_number) }}" class="w-full bg-slate-50 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm font-bold focus:ring-2 focus:ring-cyan-500" placeholder="+971 50 123 4567">
                    </div>
                    
                    <label class="flex items-center space-x-3 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="whatsapp_enabled" value="1" class="sr-only peer" {{ old('whatsapp_enabled', $settings->whatsapp_enabled) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </div>
                        <span class="text-xs font-black uppercase tracking-widest text-slate-600 dark:text-slate-300 group-hover:text-emerald-500 transition-colors">Enable WhatsApp Alerts</span>
                    </label>

                    <div class="pt-4 border-t border-gray-100 dark:border-slate-800">
                        <button type="button" onclick="document.getElementById('test-form').submit()" class="w-full py-3 bg-slate-800 hover:bg-slate-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all border border-slate-700 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            Send Test Message
                        </button>
                    </div>
                </div>

                {{-- Right Col: Event Toggles --}}
                <div class="lg:col-span-2 space-y-4">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Notification Events</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $events = [
                                'notify_new_rental' => 'New Rental Created',
                                'notify_rental_expiring' => 'Rental Expiring',
                                'notify_payment' => 'Payment Received',
                                'notify_new_fine' => 'New Fine Detected',
                                'notify_document_expiring' => 'Document Expiring',
                                'notify_maintenance' => 'Maintenance Due',
                                'notify_driver_license' => 'Driver License Expiring',
                                'notify_subscription' => 'Subscription Expiring',
                            ];
                        @endphp

                        @foreach($events as $field => $label)
                        <label class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-gray-100 dark:border-slate-800 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800 transition-all group">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $label }}</span>
                            <div class="relative">
                                <input type="checkbox" name="{{ $field }}" value="1" class="sr-only peer" {{ old($field, $settings->$field ?? true) ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-slate-200 dark:bg-slate-700 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-cyan-500"></div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-800 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-cyan-600/20">
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    {{-- Hidden form for testing --}}
    <form id="test-form" method="POST" action="{{ route('company.settings.whatsapp.test') }}" class="hidden">
        @csrf
        <input type="hidden" name="whatsapp_number" id="test_number">
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('whatsapp_number').addEventListener('input', function(e) {
        document.getElementById('test_number').value = e.target.value;
    });
    // Set initial value
    document.getElementById('test_number').value = document.getElementById('whatsapp_number').value;
</script>
@endpush
@endsection
