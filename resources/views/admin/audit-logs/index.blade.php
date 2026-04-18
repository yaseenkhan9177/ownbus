@extends('layouts.super-admin')

@section('title', 'System Audit Logs')

@section('header')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 leading-tight">Audit Logs</h1>
            <p class="text-sm text-gray-500 mt-1">Track comprehensive system activities across all modules.</p>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Filter Bar -->
    <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex flex-wrap gap-4 items-center justify-between">
        <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="flex items-center gap-3 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search logic or user..." class="text-sm rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 w-64">
            
            <select name="action" class="text-sm rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Actions</option>
                <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create</option>
                <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update</option>
                <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
            </select>
            
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">Filter</button>
            @if(request()->anyFilled(['search', 'action', 'module']))
                <a href="{{ route('admin.audit-logs.index') }}" class="text-gray-500 text-sm hover:text-gray-700 font-medium">Clear</a>
            @endif
        </form>
    </div>

    <!-- Data Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="p-4 font-semibold">User</th>
                    <th class="p-4 font-semibold">Action</th>
                    <th class="p-4 font-semibold">Module</th>
                    <th class="p-4 font-semibold min-w-[200px]">Changes</th>
                    <th class="p-4 font-semibold">IP & Agent</th>
                    <th class="p-4 font-semibold text-right">Timestamp</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition-colors group">
                        <td class="p-4">
                            <div class="font-medium text-gray-900">{{ $log->user->name ?? 'System/Guest' }}</div>
                            <div class="text-xs text-gray-500">{{ $log->company->company_name ?? 'Super Admin' }}</div>
                        </td>
                        <td class="p-4">
                            @if($log->action === 'create')
                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-green-50 text-green-700 text-xs font-medium ring-1 ring-inset ring-green-600/20">Create</span>
                            @elseif($log->action === 'update')
                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-medium ring-1 ring-inset ring-blue-600/20">Update</span>
                            @elseif($log->action === 'delete')
                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-red-50 text-red-700 text-xs font-medium ring-1 ring-inset ring-red-600/20">Delete</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-50 text-gray-700 text-xs font-medium ring-1 ring-inset ring-gray-600/20">{{ ucfirst($log->action) }}</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <span class="font-medium text-gray-700">{{ $log->module }}</span>
                            <span class="text-xs text-gray-400">#{{ $log->reference_id }}</span>
                        </td>
                        <td class="p-4">
                            @if($log->action === 'update')
                                <div class="text-xs space-y-1">
                                    @if(is_array($log->new_data))
                                        @foreach($log->new_data as $key => $val)
                                            <div class="flex items-start gap-1">
                                                <span class="text-gray-500 font-medium">{{ $key }}:</span>
                                                <span class="line-through text-red-400 mr-1">{{ is_array($log->old_data[$key] ?? '') ? json_encode($log->old_data[$key] ?? '') : ($log->old_data[$key] ?? 'null') }}</span>
                                                <span class="text-green-600">&rarr; {{ is_array($val) ? json_encode($val) : $val }}</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @elseif($log->action === 'create')
                                <span class="text-xs text-gray-500">New record created.</span>
                            @elseif($log->action === 'delete')
                                <span class="text-xs text-gray-500">Record completely deleted.</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="text-xs text-gray-700">{{ $log->ip_address }}</div>
                            <div class="text-[10px] text-gray-400 truncate w-32" title="{{ $log->user_agent }}">{{ $log->user_agent }}</div>
                        </td>
                        <td class="p-4 text-right text-gray-500 whitespace-nowrap">
                            {{ $log->created_at->format('M d, Y H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="font-medium text-gray-900">No logs found</span>
                                <p class="text-sm mt-1">There are no audit logs matching your criteria.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($logs->hasPages())
        <div class="p-4 border-t border-gray-100 bg-white">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection