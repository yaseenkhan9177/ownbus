<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Slip - {{ $slip->user->name ?? 'Employee' }} - {{ $slip->batch->period_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
            }

            .print-shadow {
                shadow: none;
                border: 1px solid #eee;
            }
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 antialiased p-4 md:p-12">

    <div class="max-w-4xl mx-auto bg-white p-10 rounded-2xl shadow-xl border border-gray-100 print-shadow relative overflow-hidden">
        <!-- Decoration side element -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-600/5 rounded-full -mr-16 -mt-16"></div>

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 border-b-2 border-gray-50 pb-8 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 uppercase">Salary Certificate</h1>
                    <p class="text-xs font-semibold text-indigo-600 uppercase tracking-widest">{{ $slip->batch->period_name }}</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-lg font-bold text-gray-800">{{ auth()->user()->company->name }}</h2>
                <p class="text-xs text-gray-500 max-w-[200px] ml-auto">Official Payroll Document Generated for Employee Records.</p>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-12">
            <div>
                <p class="text-[10px] font-bold text-indigo-500 uppercase mb-1 tracking-wider">Employee Name</p>
                <p class="text-sm font-semibold">{{ $slip->user->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-indigo-500 uppercase mb-1 tracking-wider">Designation</p>
                <p class="text-sm font-semibold">Operational Staff / Driver</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-indigo-500 uppercase mb-1 tracking-wider">Slip Number</p>
                <p class="text-sm font-semibold">#SLP-{{ str_pad($slip->id, 8, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-indigo-500 uppercase mb-1 tracking-wider">Issue Date</p>
                <p class="text-sm font-semibold">{{ $slip->created_at->format('d M, Y') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Earnings -->
            <div>
                <h3 class="text-sm font-bold border-b border-gray-100 pb-2 mb-4 uppercase tracking-widest text-gray-400">Earnings Breakdown</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center bg-gray-50/50 p-3 rounded-lg border border-gray-50">
                        <span class="text-sm font-medium text-gray-600">Base Salary</span>
                        <span class="text-sm font-bold text-gray-900">AED {{ number_format($slip->base_salary, 2) }}</span>
                    </div>
                    @foreach($slip->items->where('type', 'addition') as $item)
                    <div class="flex justify-between items-center group">
                        <span class="text-sm text-gray-500">{{ $item->label }}</span>
                        <span class="text-sm font-semibold text-emerald-600">+ AED {{ number_format($item->amount, 2) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Deductions -->
            <div>
                <h3 class="text-sm font-bold border-b border-gray-100 pb-2 mb-4 uppercase tracking-widest text-gray-400">Deductions Breakdown</h3>
                <div class="space-y-4">
                    @forelse($slip->items->where('type', 'deduction') as $item)
                    <div class="flex justify-between items-center group">
                        <span class="text-sm text-gray-500">{{ $item->label }}</span>
                        <span class="text-sm font-semibold text-rose-500">- AED {{ number_format($item->amount, 2) }}</span>
                    </div>
                    @empty
                    <div class="flex justify-between items-center bg-gray-50/50 p-3 rounded-lg border border-dashed border-gray-100">
                        <span class="text-xs text-gray-400 italic">No deductions found for this period.</span>
                        <span class="text-sm font-bold text-gray-300">0.00</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Summary Statistics Section -->
        <div class="mt-12 pt-8 border-t-2 border-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-end">
                <div class="space-y-3">
                    <div class="flex justify-between text-xs text-gray-500 uppercase tracking-tighter">
                        <span>Gross Earnings</span>
                        <span class="font-bold text-gray-700 font-mono">AED {{ number_format($slip->base_salary + $slip->total_additions, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 uppercase tracking-tighter">
                        <span>Total Deductions</span>
                        <span class="font-bold text-rose-400 font-mono">AED {{ number_format($slip->total_deductions, 2) }}</span>
                    </div>
                    <div class="flex justify-between pt-3 border-t border-gray-50 items-center">
                        <span class="text-lg font-bold text-gray-900 uppercase tracking-widest">Net Payable</span>
                        <div class="text-right">
                            <span class="text-2xl font-black text-indigo-600 font-mono tracking-tighter">AED {{ number_format($slip->net_salary, 2) }}</span>
                            <p class="text-[9px] text-gray-400 font-medium uppercase mt-1">Amount and figures verified</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-8 md:text-right">
                    <div class="space-y-2">
                        <div class="w-32 h-1 bg-gray-900 ml-auto hidden md:block"></div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest uppercase">Authorized Signature</p>
                    </div>
                    <p class="text-[10px] text-gray-400 leading-relaxed max-w-[280px] ml-auto">
                        This is a computer-generated document and does not require a physical signature for internal verification purposes.
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-12 flex items-center justify-between text-[9px] text-gray-400 uppercase font-bold tracking-[0.2em] pt-4 border-t border-gray-50">
            <span>{{ auth()->user()->company->name }} System • Accounting Intelligence V2.2</span>
            <span>Printed on {{ now()->format('Y-m-d H:i') }}</span>
        </div>
    </div>

    <!-- Actions for UI -->
    <div class="max-w-4xl mx-auto mt-8 flex justify-center gap-4 no-print">
        <button onclick="window.print()" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Download / Print Slip
        </button>
        <button onclick="window.close()" class="px-8 py-3 bg-white hover:bg-gray-50 text-gray-600 font-bold rounded-xl border border-gray-200 transition-all">
            Close Window
        </button>
    </div>

</body>

</html>