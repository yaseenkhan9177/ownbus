@extends('portal.layout')

@section('title', 'Bulk Fine Import | OwnBus')

@section('content')
<div class="px-6 py-8">
    <div class="flex items-center space-x-4 mb-8">
        <a href="{{ route('company.fines.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-slate-600 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Bulk Fine Import</h1>
            <p class="text-slate-500 text-sm">Upload CSV exports from RTA or Police Portals</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-3xl p-10 shadow-xl border border-slate-100">
            <div class="mb-10 text-center">
                <div class="w-20 h-20 bg-indigo-50 border border-indigo-100 rounded-3xl flex items-center justify-center mx-auto mb-6 text-indigo-600">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                </div>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Upload CSV File</h3>
                <p class="text-slate-500 text-sm mt-2">Download the template to ensure correct column mapping</p>
                <div class="mt-4">
                    <a href="#" class="text-indigo-600 text-[10px] font-extrabold uppercase tracking-widest border-b border-indigo-200 hover:border-indigo-600 transition-all">Download Sample Template</a>
                </div>
            </div>

            <form action="{{ route('company.fines.storeImport') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-6">
                    <div class="relative group">
                        <input type="file" name="csv_file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required>
                        <div class="border-2 border-dashed border-slate-200 rounded-2xl p-12 text-center group-hover:border-indigo-400 group-hover:bg-indigo-50/10 transition-all">
                            <p class="text-slate-400 text-sm font-medium">Click to browse or drag and drop your file</p>
                            <p class="text-slate-300 text-xs mt-1">Accepted format: .csv, .txt (Max 2MB)</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Column mapping</h4>
                            <ul class="text-[10px] text-slate-500 space-y-1">
                                <li>&bull; <span class="font-bold text-slate-700 uppercase">vehicle_number</span> (Required)</li>
                                <li>&bull; <span class="font-bold text-slate-700 uppercase">fine_number</span> (Required)</li>
                                <li>&bull; <span class="font-bold text-slate-700 uppercase">amount</span> (Required)</li>
                                <li>&bull; <span class="font-bold text-slate-700 uppercase">fine_date</span> (YYYY-MM-DD)</li>
                            </ul>
                        </div>
                        <div class="flex items-center">
                            <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white py-4 rounded-2xl font-black text-sm uppercase tracking-widest transition-all shadow-xl">
                                Start Processing
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
