@extends('layouts.company')

@section('title', 'User Management')

@section('header_title')
<div class="flex items-center justify-between w-full">
    <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">User Management</h1>
    <a href="{{ route('settings.index') }}" class="px-4 py-2 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition">
        &larr; Back to Settings
    </a>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Invite User -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 sticky top-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Invite New User</h3>
            <form action="{{ route('settings.roles.invite') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                    <input type="text" name="name" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-800 dark:border-slate-700" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                    <input type="email" name="email" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-800 dark:border-slate-700" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                    <select name="role" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-800 dark:border-slate-700">
                        <option value="staff">Staff</option>
                        <option value="manager">Manager</option>
                        <option value="admin">Admin</option>
                        <option value="driver">Driver</option>
                    </select>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
                    Send Invitation
                </button>
            </form>
        </div>
    </div>

    <!-- Users List -->
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/50">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Active Users</h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-slate-800">
                @foreach($users as $user)
                <div class="p-6 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-800/50 transition">
                    <div class="flex items-center space-x-4">
                        <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <form action="{{ route('settings.roles.update', $user->id) }}" method="POST" class="flex items-center space-x-2">
                            @csrf
                            @method('PUT')
                            <select name="role" onchange="this.form.submit()" class="text-sm rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-800 dark:border-slate-700 py-1 pl-2 pr-8 {{ $user->id === auth()->id() ? 'opacity-50 pointer-events-none' : '' }}">
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="manager" {{ $user->role === 'manager' ? 'selected' : '' }}>Manager</option>
                                <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="driver" {{ $user->role === 'driver' ? 'selected' : '' }}>Driver</option>
                            </select>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection