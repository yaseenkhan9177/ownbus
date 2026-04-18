@extends('layouts.company')

@section('content')
<div class="space-y-6">
    <!-- Station Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white mt-1 uppercase tracking-tighter">STATION_COMMAND_MONITOR</h1>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Geographic Hubs & Asset Distribution</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('company.settings.index') }}" class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                HQ_CONFIGURATION
            </a>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-blue-500/20"
                onclick="window.dispatchEvent(new CustomEvent('open-station-deployment'))">
                + DEPLOY_NEW_STATION
            </button>
        </div>
    </div>

    <!-- Station Deployment Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($branches as $branch)
        <div class="bg-white dark:bg-slate-900 rounded-3xl border {{ $branch->is_main ? 'border-blue-500/30' : 'border-slate-100 dark:border-slate-800' }} p-6 shadow-sm relative overflow-hidden group transition-all hover:shadow-xl hover:shadow-slate-900/5">
            @if($branch->is_main)
            <div class="absolute top-0 right-0">
                <div class="bg-blue-600 text-white text-[8px] font-black uppercase px-3 py-1 rounded-bl-xl tracking-widest">PRIMARY_HUB</div>
            </div>
            @endif

            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:text-blue-500 transition-colors">
                        <i class="bi bi-geo-alt-fill text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $branch->name }}</h3>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ $branch->location ?? 'LOC_UNDETERMINED' }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50">
                        <span class="text-[8px] font-black text-slate-400 uppercase block mb-1">STATION_COMMS</span>
                        <div class="text-[10px] font-bold text-slate-900 dark:text-white truncate">{{ $branch->phone ?? 'N/A' }}</div>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50">
                        <span class="text-[8px] font-black text-slate-400 uppercase block mb-1">PROTOCOL_EMAIL</span>
                        <div class="text-[10px] font-bold text-slate-900 dark:text-white truncate">{{ $branch->email ?? 'N/A' }}</div>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-900 dark:bg-blue-600 shadow-lg shadow-slate-900/10">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[9px] font-black text-white/50 uppercase tracking-widest">Fleet_Saturation</span>
                        <span class="text-[10px] font-black text-white">{{ $branch->currency }}</span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black text-white">0</span>
                        <span class="text-[9px] font-bold text-white/50 uppercase">ACTIVE_UNITS</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-50 dark:border-slate-800 flex items-center justify-between">
                <button class="text-[10px] font-black text-blue-500 uppercase tracking-widest hover:underline"
                    data-branch="{{ $branch->toJson() }}"
                    onclick="window.dispatchEvent(new CustomEvent('open-station-deployment', { detail: JSON.parse(this.dataset.branch) }))">
                    UPDATE_PROTOCOLS
                </button>
                <form action="{{ route('company.branches.destroy', $branch->id) }}" method="POST" onsubmit="return confirm('INITIATE DECOMMISSIONING SEQUENCE?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-rose-500 hover:text-rose-600 transition-colors {{ $branch->is_main ? 'opacity-20 pointer-events-none' : '' }}">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="md:col-span-2 lg:col-span-3 bg-white dark:bg-slate-900 rounded-3xl border border-dashed border-slate-200 dark:border-slate-700 p-20 text-center">
            <div class="w-16 h-16 rounded-3xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-300 mx-auto mb-6">
                <i class="bi bi-broadcast text-3xl"></i>
            </div>
            <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">ZERO_STATIONS_DEPLOYED</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase mt-2">Initialize the geographic grid to begin asset distribution</p>
            <button class="mt-8 bg-blue-600 text-white px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-blue-500/20"
                onclick="window.dispatchEvent(new CustomEvent('open-station-deployment'))">
                LAUNCH_PRIMARY_HUB
            </button>
        </div>
        @endforelse
    </div>
</div>

<!-- Station Deployment Protocol (Modal) -->
<div x-data="{ open: false, isEdit: false, branchId: null, form: { name: '', location: '', phone: '', email: '', currency: 'AED', is_main: 0 } }"
    @open-station-deployment.window="
        open = true; 
        if($event.detail) {
            isEdit = true;
            branchId = $event.detail.id;
            form = { ...$event.detail };
        } else {
            isEdit = false;
            branchId = null;
            form = { name: '', location: '', phone: '', email: '', currency: 'AED', is_main: 0, add_manager: 0, manager_name: '', manager_email: '', manager_password: '' };
        }
    "
    x-show="open"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
    x-cloak>

    <div @click.away="open = false"
        class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 w-full max-w-xl overflow-hidden shadow-2xl">

        <form :action="isEdit ? '{{ route('company.branches.update', 'ID_HOLDER') }}'.replace('ID_HOLDER', branchId) : '{{ route('company.branches.store') }}'" method="POST">
            @csrf
            <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>

            <div class="px-8 py-6 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tighter" x-text="isEdit ? 'UPDATE_STATION_PROTOCOLS' : 'INITIATE_STATION_DEPLOYMENT'"></h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Configuring Geographic Operational Hub</p>
            </div>

            <div class="p-8 space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Station_Alias</label>
                        <input type="text" name="name" x-model="form.name" required
                            class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Geographic_REF</label>
                        <input type="text" name="location" x-model="form.location"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Station_Comms_Phone</label>
                        <input type="text" name="phone" x-model="form.phone"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Direct_Signal_Email</label>
                        <input type="email" name="email" x-model="form.email"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Economic_Currency</label>
                        <select name="currency" x-model="form.currency" required
                            class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all uppercase">
                            <option value="AED">AED (DIRHAM)</option>
                            <option value="USD">USD (DOLLAR)</option>
                            <option value="SAR">SAR (RIYAL)</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-3 pt-6">
                        <input type="checkbox" name="is_main" value="1" x-model="form.is_main" id="is_main_check"
                            class="w-5 h-5 rounded-lg bg-slate-50 dark:bg-slate-800 border-none text-blue-600 focus:ring-2 focus:ring-blue-500">
                        <label for="is_main_check" class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-widest cursor-pointer">DESIGNATE_PRIMARY_HUB</label>
                    </div>
                </div>

                <!-- Manager Deployment Section -->
                <template x-if="!isEdit">
                    <div class="space-y-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight">MANAGE_STATION_COMMAND</h4>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Assign a commanding officer to this station</p>
                            </div>
                            <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl">
                                <button type="button" @click="form.add_manager = 0"
                                    :class="form.add_manager == 0 ? 'bg-white dark:bg-slate-700 shadow-sm text-blue-600' : 'text-slate-400'"
                                    class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">
                                    NO
                                </button>
                                <button type="button" @click="form.add_manager = 1"
                                    :class="form.add_manager == 1 ? 'bg-white dark:bg-slate-700 shadow-sm text-blue-600' : 'text-slate-400'"
                                    class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">
                                    YES
                                </button>
                                <input type="hidden" name="add_manager" :value="form.add_manager">
                            </div>
                        </div>

                        <div x-show="form.add_manager == 1" x-transition.opacity class="space-y-4">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Manager_Name</label>
                                <input type="text" name="manager_name" x-model="form.manager_name" :required="form.add_manager == 1"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Manager_Email</label>
                                    <input type="email" name="manager_email" x-model="form.manager_email" :required="form.add_manager == 1"
                                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Access_Password</label>
                                    <input type="password" name="manager_password" x-model="form.manager_password" :required="form.add_manager == 1"
                                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="px-8 py-6 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-4">
                <button type="button" @click="open = false" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">
                    CANCEL_DEPLOYMENT
                </button>
                <button type="submit" class="bg-blue-600 text-white px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                    <span x-text="isEdit ? 'COMMIT_PROTOCOLS' : 'DEPLOY_STATION'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection