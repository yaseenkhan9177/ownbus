<tr class="group hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors border-b border-gray-100 dark:border-slate-800/50">
    <td class="py-4 pl-6 pr-4">
        <div class="flex items-center">
            @if($level > 0)
            <div class="flex items-center mr-2">
                @for($i = 0; $i < $level; $i++)
                    <div class="w-6 h-px bg-gray-200 dark:bg-slate-700 mx-1">
            </div>
            @endfor
        </div>
        @endif

        <div class="flex flex-col">
            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $account->account_name }}</span>
            @if($account->is_system)
            <span class="text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-tighter">System Protected</span>
            @endif
        </div>
        </div>
    </td>
    <td class="py-4 px-4 text-xs font-mono font-bold text-gray-500 dark:text-gray-400">{{ $account->account_code }}</td>
    <td class="py-4 px-4 text-center">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black uppercase tracking-widest
            @if($account->account_type == 'asset') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
            @elseif($account->account_type == 'liability') bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400
            @elseif($account->account_type == 'equity') bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400
            @elseif($account->account_type == 'income') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400
            @elseif($account->account_type == 'expense') bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400
            @else bg-gray-100 text-gray-700 dark:bg-slate-800 dark:text-gray-400 @endif">
            {{ $account->account_type }}
        </span>
    </td>
    <td class="py-4 px-4 text-center">
        @if($account->is_active)
        <span class="inline-flex items-center">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2"></span>
            <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase">Active</span>
        </span>
        @else
        <span class="inline-flex items-center">
            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-2"></span>
            <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Inactive</span>
        </span>
        @endif
    </td>
    <td class="py-4 pl-4 pr-6 text-right">
        <div class="flex justify-end items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
            <button class="p-1.5 hover:bg-blue-100 dark:hover:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
            </button>
            @if(!$account->is_system)
            <button class="p-1.5 hover:bg-rose-100 dark:hover:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-lg transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m4-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
            @endif
        </div>
    </td>
</tr>

@if($account->children->count() > 0)
@foreach($account->children as $child)
@include('portal.accounting.partials.coa_row', ['account' => $child, 'level' => $level + 1])
@endforeach
@endif