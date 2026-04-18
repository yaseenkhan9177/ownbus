@extends('layouts.company')

@section('title', 'Maintenance: ' . $maintenance->maintenance_number)

@section('header_title')
<div class="flex items-center justify-between w-full">
    <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">
        Maintenance: {{ $maintenance->maintenance_number }}
    </h1>
    <div class="flex space-x-2">
        @if($maintenance->status === 'scheduled')
        <form action="{{ route('company.maintenance.update', $maintenance) }}" method="POST" class="inline">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="in_progress">
            <input type="hidden" name="start_date" value="{{ now() }}">
            <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-md shadow hover:bg-yellow-700 transition text-sm">
                <i class="fas fa-play mr-1"></i> Start Job
            </button>
        </form>
        <form action="{{ route('company.maintenance.cancel', $maintenance) }}" method="POST" class="inline" onsubmit="return confirm('Cancel this maintenance?');">
            @csrf
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md shadow hover:bg-red-700 transition text-sm">
                <i class="fas fa-times mr-1"></i> Cancel
            </button>
        </form>
        @endif
        <a href="{{ route('company.maintenance.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md shadow hover:bg-gray-700 transition text-sm">
            Back to List
        </a>
    </div>
</div>
@endsection

@section('content')

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        <!-- Top Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Status Timeline -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-sm font-medium text-gray-500 mb-4 uppercase tracking-wider">Status Timeline</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center {{ $maintenance->created_at ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                            <i class="fas fa-calendar-alt text-sm"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Scheduled</p>
                            <p class="text-xs text-gray-500">{{ $maintenance->scheduled_date ? $maintenance->scheduled_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="h-4 border-l-2 border-dashed {{ $maintenance->start_date ? 'border-blue-500' : 'border-gray-200' }} ml-4"></div>
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center {{ $maintenance->start_date ? 'bg-yellow-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                            <i class="fas fa-tools text-sm"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">In Progress</p>
                            <p class="text-xs text-gray-500">{{ $maintenance->start_date ? $maintenance->start_date->format('M d, Y H:i') : 'Pending' }}</p>
                        </div>
                    </div>
                    <div class="h-4 border-l-2 border-dashed {{ $maintenance->completed_date ? 'border-yellow-500' : 'border-gray-200' }} ml-4"></div>
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center {{ $maintenance->completed_date ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                            <i class="fas fa-check text-sm"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Completed</p>
                            <p class="text-xs text-gray-500">{{ $maintenance->completed_date ? $maintenance->completed_date->format('M d, Y H:i') : 'Pending' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicle Info -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-sm font-medium text-gray-500 mb-4 uppercase tracking-wider">Vehicle Details</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Name</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $maintenance->vehicle->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">License Plate</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $maintenance->vehicle->vehicle_number }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Current Odometer</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ number_format($maintenance->vehicle->current_odometer) }} km</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Service Type</dt>
                        <dd class="text-sm font-medium text-gray-900 capitalize">{{ $maintenance->type }}</dd>
                    </div>
                    <div class="flex justify-between pt-3 border-t">
                        <dt class="text-sm text-gray-500">Vendor/Workshop</dt>
                        <dd class="text-sm font-medium text-indigo-600">{{ $maintenance->vendor ? $maintenance->vendor->first_name . ' ' . $maintenance->vendor->last_name : 'Internal Workshop' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Accounting & Cost -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-sm font-medium text-gray-500 mb-4 uppercase tracking-wider">Cost Accounting</h3>
                <div class="mb-6">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total Authorized Cost</p>
                    <p class="text-4xl font-bold text-gray-900 mt-1">{{ number_format($maintenance->total_cost, 2) }} <span class="text-lg text-gray-500 font-normal">AED</span></p>
                </div>

                @if($maintenance->status === 'completed')
                <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                    <div class="flex items-center text-green-700 mb-2">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span class="font-semibold text-sm">Accounted in Ledger</span>
                    </div>
                    <p class="text-xs text-green-600">Journal Entry generated: Debit Maintenance Expense, Credit Accounts Payable / Cash.</p>
                </div>
                @elseif($maintenance->status === 'in_progress')
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <div class="flex items-center text-blue-700 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="font-semibold text-sm">Pending Completion</span>
                    </div>
                    <p class="text-xs text-blue-600">Accounting entry will be automatically generated once this job is marked as Completed.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Cost Breakdown Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Cost Breakdown (Items & Labor)</h3>
            </div>
            <div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Cost</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($maintenance->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 capitalize">{{ $item->item_type }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->description }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item->quantity, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item->unit_cost, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">{{ number_format($item->total_cost, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No cost items recorded.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-right text-sm font-bold text-gray-900 uppercase">Grand Total:</td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">{{ number_format($maintenance->total_cost, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Completion Action Box -->
        @if($maintenance->status === 'in_progress')
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Complete Maintenance Job</h3>
            <form action="{{ route('company.maintenance.complete', $maintenance) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                    <div>
                        <label for="odometer_reading" class="block text-sm font-medium text-gray-700">Completion Odometer (KM) <span class="text-red-500">*</span></label>
                        <input type="number" id="odometer_reading" name="odometer_reading" value="{{ $maintenance->vehicle->current_odometer }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p class="text-xs text-gray-500 mt-1">This sets the baseline for the next service interval calculation.</p>
                    </div>
                    <div>
                        <label for="completed_date" class="block text-sm font-medium text-gray-700">Completion Date <span class="text-red-500">*</span></label>
                        <input type="datetime-local" id="completed_date" name="completed_date" value="{{ now()->format('Y-m-d\TH:i') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>
                <div class="flex items-center space-x-2 text-sm text-gray-600 bg-gray-50 p-3 rounded mb-4">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    <span>Completing this job will unlock the vehicle to 'Available', post AED {{ number_format($maintenance->total_cost, 2) }} to Accounting, and reset service intervals.</span>
                </div>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md shadow hover:bg-green-700 font-medium">
                    <i class="fas fa-check-double mr-2"></i> Confirm Completion
                </button>
            </form>
        </div>
        @endif

    </div>
</div>
@endsection