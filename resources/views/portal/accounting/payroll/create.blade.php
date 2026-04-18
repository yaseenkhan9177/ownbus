@extends('layouts.company')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('company.accounting.payroll.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium flex items-center gap-1 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to History
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Process New Payroll</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 shadow-sm">Initialize a new salary batch for a specific period.</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden p-8">
        <form action="{{ route('company.accounting.payroll.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="period_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payroll Period Name</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <input type="text" name="period_name" id="period_name" required
                        class="block w-full pl-10 pr-3 py-3 bg-gray-50 border border-gray-200 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm dark:bg-slate-700 dark:text-white"
                        placeholder="e.g., February 2026"
                        value="{{ old('period_name', now()->format('F Y')) }}">
                </div>
                @error('period_name')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-gray-500 italic">This will be the title for the salary batch and appears on salary slips.</p>
            </div>

            <div>
                <label for="branch_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Branch / Storefront</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <select name="branch_id" id="branch_id"
                        class="block w-full pl-10 pr-3 py-3 bg-gray-50 border border-gray-200 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm dark:bg-slate-700 dark:text-white">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                @error('branch_id')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-gray-100 dark:border-slate-700 mt-8 flex items-center justify-between">
                <div class="flex items-start gap-3 max-w-[60%]">
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg text-indigo-600 dark:text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Processing will automatically skip employees with no defined salary or who are inactive.</p>
                </div>

                <button type="submit"
                    class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md transition-all flex items-center gap-2">
                    Initialize Batch
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection