@extends('portal.layout')

@section('title', 'Record New Fine | OwnBus')

@section('content')
<div class="px-6 py-8">
    <div class="flex items-center space-x-4 mb-8">
        <a href="{{ route('company.fines.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-slate-600 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Record Fine</h1>
            <p class="text-slate-500 text-sm">Log a new authority violation for the fleet</p>
        </div>
    </div>

    <form action="{{ route('company.fines.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-8 space-y-8">
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6 border-b border-slate-100 pb-2">Violation Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 block">Authority</label>
                            <select name="authority" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500" required>
                                <option value="RTA Dubai">RTA Dubai</option>
                                <option value="Dubai Police">Dubai Police</option>
                                <option value="Abu Dhabi Police">Abu Dhabi Police</option>
                                <option value="Sharjah Police">Sharjah Police</option>
                                <option value="ITC Abu Dhabi">ITC Abu Dhabi</option>
                                <option value="Ajman Police">Ajman Police</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 block">Violation Type</label>
                            <input type="text" name="fine_type" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. Speeding (20-30 km/h)" required>
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 block">Fine Number</label>
                            <input type="text" name="fine_number" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. 1234567890" required>
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 block">Fine Date</label>
                            <input type="date" name="fine_date" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 block">Vehicle</label>
                            <select name="vehicle_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500" required>
                                @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_number }} ({{ $vehicle->name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 block">Driver (At time of fine)</label>
                            <select name="driver_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500">
                                <option value="">Select Driver</option>
                                @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 block">Amount (AED)</label>
                            <input type="number" step="0.01" name="amount" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500" required>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 block">Fine Description / Location</label>
                    <textarea name="description" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. Sheikh Zayed Road, near Al Safa Bridge"></textarea>
                </div>
            </div>

            <div class="lg:col-span-4 space-y-8">
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest mb-6">Responsibility</h3>
                    
                    <div class="space-y-4">
                        <select name="responsible_type" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500" required>
                            <option value="driver">Driver (Recover from salary)</option>
                            <option value="customer">Customer (Recover from deposit)</option>
                            <option value="company">Company Liability</option>
                            <option value="both">Split (50/50)</option>
                        </select>

                        <div class="flex items-center p-3 rounded-xl bg-amber-50 border border-amber-100 italic text-[10px] text-amber-700 leading-relaxed">
                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Selecting a responsible party will automatically generate a journal entry for recovery in the accounting module.
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-xl font-black text-sm uppercase tracking-widest transition-all shadow-lg">
                        Save Fine
                    </button>
                    <p class="text-[10px] text-center text-slate-400 mt-4 uppercase font-bold tracking-widest">Linked to Fleet Compliance</p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection