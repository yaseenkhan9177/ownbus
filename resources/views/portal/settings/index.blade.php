@extends('layouts.company')

@section('title', 'Company Profile - Settings')

@section('header_title')
<h1 class="text-xl font-bold text-gray-900 dark:text-white uppercase tracking-tight">Company Profile</h1>
@endsection

@section('content')
<div class="space-y-6 max-w-4xl">
    <form action="{{ route('company.settings.update') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-800">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-black uppercase text-slate-700 dark:text-slate-300 mb-2">Company Name</label>
                <input type="text" name="name" value="{{ old('name', $company->name) }}" required class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-cyan-500">
            </div>
            <div>
                <label class="block text-xs font-black uppercase text-slate-700 dark:text-slate-300 mb-2">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $company->email) }}" required class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-cyan-500">
            </div>
            <div>
                <label class="block text-xs font-black uppercase text-slate-700 dark:text-slate-300 mb-2">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-cyan-500">
            </div>
            <div>
                <label class="block text-xs font-black uppercase text-slate-700 dark:text-slate-300 mb-2">Address</label>
                <input type="text" name="address" value="{{ old('address', $company->address) }}" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-cyan-500">
            </div>
            <div>
                <label class="block text-xs font-black uppercase text-slate-700 dark:text-slate-300 mb-2">Currency (ISO)</label>
                <input type="text" name="currency" value="{{ old('currency', $company->currency ?? 'AED') }}" required maxlength="3" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-cyan-500">
            </div>
            <div>
                <label class="block text-xs font-black uppercase text-slate-700 dark:text-slate-300 mb-2">Tax Rate (%)</label>
                <input type="number" step="0.01" name="tax_rate" value="{{ old('tax_rate', $company->tax_rate ?? 5.0) }}" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-cyan-500">
            </div>
            <div>
                <label class="block text-xs font-black uppercase text-slate-700 dark:text-slate-300 mb-2">TRN Number</label>
                <input type="text" name="trn_number" value="{{ old('trn_number', $company->trn_number) }}" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-cyan-500">
            </div>
            <div>
                <label class="block text-xs font-black uppercase text-slate-700 dark:text-slate-300 mb-2">Invoice Prefix</label>
                <input type="text" name="invoice_prefix" value="{{ old('invoice_prefix', $company->invoice_prefix ?? 'INV-') }}" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-cyan-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-black uppercase text-slate-700 dark:text-slate-300 mb-2">Company Logo</label>
                <input type="file" name="logo" accept="image/*" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-cyan-500">
            </div>
        </div>

        <div class="mt-8 text-right">
            <button type="submit" class="px-6 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all">Save Profile</button>
        </div>
    </form>
</div>
@endsection
