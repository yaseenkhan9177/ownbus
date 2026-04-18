@extends('layouts.company')

@section('content')
<div class="space-y-6">
    <!-- Security Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white mt-1 uppercase tracking-tighter">AEGIS_SECURITY_CONSOLE</h1>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Personnel Management & Role Synchronization</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('company.settings.index') }}" class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                BACK_TO_HQ
            </a>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-blue-500/20"
                onclick="window.dispatchEvent(new CustomEvent('open-invitation-protocol'))">
                + INITIATE_INVITATION
            </button>
        </div>
    </div>

    <!-- Personnel Roster Grid -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="px-8 py-6 border-b border-slate-50 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/30">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active_Personnel_Roster</h3>
            <span class="text-[9px] font-black text-blue-500 uppercase tracking-widest">{{ $users->count() }}_OPERATIVES_DEPLOYED</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/30 dark:bg-slate-800/20 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-800">
                        <th class="px-8 py-4">Operative_Identity</th>
                        <th class="px-8 py-4">Security_Clearance</th>
                        <th class="px-8 py-4">Deployment_Hub</th>
                        <th class="px-8 py-4">Protocol_State</th>
                        <th class="px-8 py-4 text-right">Execution</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                        <td class="px-8 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 font-black text-lg">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $user->name }}</div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            <div class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-tighter">
                                {{ $user->role === 'admin' ? 'LEVEL_ALPHA_ADMIN' : '' }}
                                {{ $user->role === 'manager' ? 'LEVEL_BETA_MANAGER' : '' }}
                                {{ $user->role === 'branch_manager' ? 'STATION_COMMANDER' : '' }}
                                {{ $user->role === 'staff' ? 'LEVEL_GAMMA_STAFF' : '' }}
                                {{ $user->role === 'driver' ? 'OPERATIONAL_DRIVER' : '' }}
                            </div>
                        </td>
                        <td class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-tighter">
                            {{ $user->branches->pluck('name')->implode(', ') ?: 'GLOBAL_ACCESS' }}
                        </td>
                        <td class="px-8 py-4">
                            <span class="inline-flex px-2 py-0.5 rounded-lg text-[9px] font-black bg-emerald-500/10 text-emerald-500 ring-1 ring-emerald-500/20 uppercase tracking-widest">
                                ACTIVE_DEPLOIMENT
                            </span>
                        </td>
                        <td class="px-8 py-4 text-right flex items-center justify-end gap-2">
                            <button class="text-blue-500 hover:text-blue-600 transition-colors"
                                data-user="{{ json_encode(['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role]) }}"
                                onclick="window.dispatchEvent(new CustomEvent('open-edit-protocol', { detail: JSON.parse(this.dataset.user) }))">
                                <i class="bi bi-pencil-square text-sm"></i>
                            </button>
                            @if($user->id !== auth()->id())
                            <button class="text-rose-500 hover:text-rose-600 transition-colors">
                                <i class="bi bi-shield-slash-fill text-sm"></i>
                            </button>
                            @else
                            <span class="text-[9px] font-black text-slate-300 uppercase italic tracking-widest">SELF</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Invitation Protocol (Modal) -->
<div x-data="{ open: false }"
    @open-invitation-protocol.window="open = true"
    x-show="open"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
    x-cloak>

    <div @click.away="open = false"
        class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 w-full max-w-md overflow-hidden shadow-2xl">

        <form action="{{ route('company.settings.roles.invite') }}" method="POST">
            @csrf

            <div class="px-8 py-6 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tighter">INITIATE_INVITATION_PROTOCOL</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Deploying New Operative into the Ecosystem</p>
            </div>

            <div class="p-8 space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Full_Operative_Name</label>
                    <input type="text" name="name" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Direct_Comms_Email</label>
                    <input type="email" name="email" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Security_Clearance_Level</label>
                    <select name="role" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all uppercase">
                        <option value="staff">LEVEL_GAMMA_STAFF</option>
                        <option value="manager">LEVEL_BETA_MANAGER</option>
                        <option value="admin">LEVEL_ALPHA_ADMIN</option>
                        <option value="driver">OPERATIONAL_DRIVER</option>
                    </select>
                </div>
            </div>

            <div class="px-8 py-6 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-4">
                <button type="button" @click="open = false" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">
                    ABORT_INVITATION
                </button>
                <button type="submit" class="bg-blue-600 text-white px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                    SEND_PROTOCOLS
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Protocol (Modal) -->
<div x-data="{ open: false, userId: null, form: { name: '', email: '', role: '', password: '' } }"
    @open-edit-protocol.window="
        open = true;
        userId = $event.detail.id;
        form = { ...$event.detail, password: '' };
    "
    x-show="open"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
    x-cloak>

    <div @click.away="open = false"
        class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 w-full max-w-md overflow-hidden shadow-2xl">

        <form :action="'{{ route('company.settings.roles.update', 'ID_HOLDER') }}'.replace('ID_HOLDER', userId)" method="POST">
            @csrf
            @method('PUT')

            <div class="px-8 py-6 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tighter">RECONFIGURE_OPERATIVE_PROTOCOLS</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Adjusting Identity & Security Parameters</p>
            </div>

            <div class="p-8 space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Full_Operative_Name</label>
                    <input type="text" name="name" x-model="form.name" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Direct_Comms_Email</label>
                    <input type="email" name="email" x-model="form.email" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Security_Clearance</label>
                    <select name="role" x-model="form.role" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all uppercase">
                        <option value="admin">LEVEL_ALPHA_ADMIN</option>
                        <option value="manager">LEVEL_BETA_MANAGER</option>
                        <option value="branch_manager">STATION_COMMANDER</option>
                        <option value="staff">LEVEL_GAMMA_STAFF</option>
                        <option value="driver">OPERATIONAL_DRIVER</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Override_Password (Optional)</label>
                    <input type="password" name="password" x-model="form.password" placeholder="LEAVE_BLANK_TO_RETAIN_CURRENT"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-[9px] font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
            </div>

            <div class="px-8 py-6 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-4">
                <button type="button" @click="open = false" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">
                    ABORT_CHANGES
                </button>
                <button type="submit" class="bg-blue-600 text-white px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                    COMMIT_PROTOCOLS
                </button>
            </div>
        </form>
    </div>
</div>
@endsection