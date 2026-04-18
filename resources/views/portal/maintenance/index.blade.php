@extends('layouts.company')

@section('title', 'Maintenance Engine')

@section('header_title')
<div class="flex items-center justify-between w-full">
    <div class="flex items-center space-x-2">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Maintenance Engine</h1>
    </div>
    <div class="flex space-x-2">
        <a href="{{ route('company.maintenance.schedule') }}" class="px-4 py-2 bg-yellow-600 text-white rounded-md shadow hover:bg-yellow-700 transition text-sm">
            <i class="fas fa-calendar-alt mr-1"></i> Preventive Schedule
        </a>
        <a href="{{ route('company.maintenance.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 transition text-sm">
            <i class="fas fa-plus mr-1"></i> New Maintenance
        </a>
    </div>
</div>
@endsection

@section('content')

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- KPIs -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Scheduled Jobs</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $kpis['scheduled'] }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-xl">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">In Progress</p>
                    <h3 class="text-2xl font-bold text-yellow-600">{{ $kpis['in_progress'] }}</h3>
                </div>
                <div class="w-12 h-12 bg-yellow-50 text-yellow-600 rounded-lg flex items-center justify-center text-xl">
                    <i class="fas fa-tools"></i>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Overdue Services</p>
                    <h3 class="text-2xl font-bold text-red-600">{{ $kpis['overdue'] }}</h3>
                </div>
                <div class="w-12 h-12 bg-red-50 text-red-600 rounded-lg flex items-center justify-center text-xl">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Cost (This Month)</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ number_format($kpis['total_cost_month'], 2) }}</h3>
                </div>
                <div class="w-12 h-12 bg-green-50 text-green-600 rounded-lg flex items-center justify-center text-xl">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Maintenance Records</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Number</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Cost</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($records as $record)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                    <a href="{{ route('company.maintenance.show', $record->id) }}">
                                        {{ $record->maintenance_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $record->vehicle->name }} ({{ $record->vehicle->vehicle_number }})
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">
                                    {{ $record->type }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($record->status === 'scheduled')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Scheduled</span>
                                    @elseif($record->status === 'in_progress')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">In Progress</span>
                                    @elseif($record->status === 'completed')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                    @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Cancelled</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    {{ number_format($record->total_cost, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('company.maintenance.show', $record->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No maintenance records found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $records->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection