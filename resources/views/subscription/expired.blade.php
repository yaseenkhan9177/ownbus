<!DOCTYPE html>
<html lang="en" class="h-full bg-[#0A0F1E]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trial Expired - OwnBus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4">
    <div class="max-w-2xl w-full bg-[#111827] border border-slate-800 rounded-3xl p-8 md:p-12 shadow-2xl relative overflow-hidden">
        <!-- Decorative Glow -->
        <div class="absolute -top-32 -right-32 w-64 h-64 bg-cyan-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
        <div class="absolute -bottom-32 -left-32 w-64 h-64 bg-rose-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>

        <div class="relative z-10 text-center">
            <div class="mb-6 flex justify-center">
                <div class="w-16 h-16 bg-slate-800 rounded-2xl flex items-center justify-center border border-slate-700 shadow-inner">
                    <span class="text-3xl">🚌</span>
                </div>
            </div>
            
            <h1 class="text-3xl font-bold text-white mb-4">Your Trial Has Ended</h1>
            <p class="text-slate-400 mb-10 text-lg">
                Thank you for trying <span class="text-cyan-400 font-semibold">OwnBus</span>! Upgrade now to keep managing your fleet professionally.
            </p>

            <div class="grid md:grid-cols-2 gap-6 mb-12">
                <!-- Monthly Option -->
                <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 hover:border-cyan-500/50 transition-colors">
                    <h3 class="text-slate-400 font-semibold uppercase tracking-widest text-sm mb-2">Monthly</h3>
                    <div class="text-3xl font-bold text-white mb-1">Contact Us</div>
                    <p class="text-sm text-slate-500 mb-6">per month</p>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', env('OWNER_WHATSAPP', '+923409172223')) }}?text=Hi,%20I%20want%20to%20subscribe%20to%20OwnBus.%20My%20company:%20{{ urlencode(auth()->user()->company->name ?? '') }}.%20Plan:%20Monthly" target="_blank" class="block w-full py-3 px-4 bg-slate-800 hover:bg-slate-700 text-white rounded-xl font-medium transition-colors border border-slate-700">
                        Select Monthly
                    </a>
                </div>

                <!-- Yearly Option -->
                <div class="bg-gradient-to-b from-cyan-900/40 to-slate-900 border border-cyan-500/30 rounded-2xl p-6 relative">
                    <div class="absolute -top-3 inset-x-0 flex justify-center">
                        <span class="bg-cyan-500 text-white text-xs font-bold uppercase tracking-wider py-1 px-3 rounded-full shadow-[0_0_10px_rgba(6,182,212,0.4)]">Save 20%</span>
                    </div>
                    <h3 class="text-cyan-400 font-semibold uppercase tracking-widest text-sm mb-2">Yearly</h3>
                    <div class="text-3xl font-bold text-white mb-1">Contact Us</div>
                    <p class="text-sm text-slate-500 mb-6">per year</p>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', env('OWNER_WHATSAPP', '+923409172223')) }}?text=Hi,%20I%20want%20to%20subscribe%20to%20OwnBus.%20My%20company:%20{{ urlencode(auth()->user()->company->name ?? '') }}.%20Plan:%20Yearly" target="_blank" class="block w-full py-3 px-4 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl font-medium shadow-[0_0_15px_rgba(6,182,212,0.3)] transition-colors">
                        Select Yearly
                    </a>
                </div>
            </div>

            <div class="bg-slate-800/50 rounded-2xl p-6 text-left border border-slate-700/50">
                <p class="text-sm text-slate-300 font-medium mb-4">To subscribe, contact us directly:</p>
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-lg">📧</span>
                    <span class="text-white font-medium">{{ env('OWNER_EMAIL', 'ykcaptain2223@gmail.com') }}</span>
                </div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-lg">📱</span>
                    <span class="text-white font-medium">{{ env('OWNER_WHATSAPP', '+923409172223') }} (WhatsApp)</span>
                </div>
                <div class="flex items-center gap-3 mb-6">
                    <span class="text-lg">🌐</span>
                    <a href="https://ownbus.software" target="_blank" class="text-cyan-400 hover:text-cyan-300 font-medium transition-colors">ownbus.software</a>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', env('OWNER_WHATSAPP', '+923409172223')) }}" target="_blank" class="flex-1 py-3 px-4 bg-[#25D366] hover:bg-[#20bd5a] text-white rounded-xl font-bold flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        WhatsApp Us Now
                    </a>
                    <a href="mailto:{{ env('OWNER_EMAIL', 'ykcaptain2223@gmail.com') }}?subject=OwnBus%20Subscription" class="flex-1 py-3 px-4 bg-slate-700 hover:bg-slate-600 text-white rounded-xl font-bold flex items-center justify-center transition-colors">
                        <span class="mr-2">📧</span> Email Us Now
                    </a>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-slate-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-slate-500 hover:text-slate-300 font-medium transition-colors flex items-center justify-center mx-auto">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
