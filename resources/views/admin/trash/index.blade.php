@extends('layouts.super-admin')

@section('title', 'Recycle Bin')

@section('header')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 leading-tight">System Trash</h1>
            <p class="text-sm text-gray-500 mt-1">Review, restore, or securely wipe deleted records.</p>
        </div>
    </div>
@endsection

@section('content')
<div x-data="{ activeTab: 'vehicles' }" class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700">

    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="border-b border-gray-50 dark:border-slate-800 px-6">
            <nav class="-mb-px flex space-x-6">
                @foreach (['vehicles', 'rentals', 'customers', 'contracts', 'invoices'] as $tab)
                <button @click="activeTab = '{{ $tab }}'" :class="{ 'border-cyan-500 text-cyan-500': activeTab === '{{ $tab }}', 'border-transparent text-slate-400 hover:text-slate-200': activeTab !== '{{ $tab }}' }" class="py-4 px-1 border-b-2 font-black text-xs uppercase tracking-widest transition-all">
                    {{ ucfirst($tab) }} ({{ $trashed[$tab]->total() }})
                </button>
                @endforeach
            </nav>
        </div>

        <div class="p-0">
            @foreach (['vehicles', 'rentals', 'customers', 'contracts', 'invoices'] as $tab)
            <div x-show="activeTab === '{{ $tab }}'" style="display: none;" class="animate-in fade-in slide-in-from-left-2 duration-300">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[9px] bg-slate-50 font-black text-slate-400 uppercase tracking-widest border-b border-gray-100">
                            <th class="py-4 px-6">Record Details</th>
                            <th class="py-4 px-6 text-right">Deleted At</th>
                            <th class="py-4 px-6 text-right w-32">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($trashed[$tab] as $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3 px-6">
                                <span class="text-sm font-bold text-slate-900">
                                    {{ $item->name ?? $item->vehicle_number ?? $item->rental_number ?? $item->contract_number ?? $item->invoice_number ?? $item->id }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-right">
                                <span class="text-xs text-slate-500">{{ $item->deleted_at->format('d M Y H:i') }}</span>
                            </td>
                            <td class="py-3 px-6 text-right space-x-2">
                                <form action="{{ route('admin.trash.restore', ['module' => $tab, 'id' => $item->id]) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-emerald-500 hover:text-emerald-700 text-sm font-bold bg-emerald-50 px-3 py-1 rounded">Restore</button>
                                </form>
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('Super Admin'))
                                <form action="{{ route('admin.trash.force-delete', ['module' => $tab, 'id' => $item->id]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? This cannot be undone!');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-500 hover:text-rose-700 text-sm font-bold bg-rose-50 px-3 py-1 rounded">Wipe</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-xs text-slate-400 font-bold uppercase tracking-widest">Trash is empty for {{ $tab }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($trashed[$tab]->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $trashed[$tab]->links() }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
