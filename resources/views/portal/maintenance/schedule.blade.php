@extends('layouts.company')

@section('title', 'Preventive Maintenance Schedule')

@section('header_title')
<div class="flex items-center space-x-2">
    <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Preventive Schedule</h1>
</div>
@endsection

@section('content')

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 text-sm text-gray-600">
            Displaying vehicles that require preventive maintenance within the next 7 days, or where the odometer is within 500km of the next scheduled service.
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Serviced</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due By Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due By KM</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Indicator</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($schedules as $schedule)
                            @php
                            $vehicle = $schedule->vehicle;
                            $overdue = false;
                            $statusText = 'Due Soon';
                            $color = 'text-yellow-600 bg-yellow-100';

                            if ($schedule->next_due_date && $schedule->next_due_date < now()) {
                                $overdue=true;
                                $statusText='Overdue (Date)' ;
                                } elseif ($schedule->next_due_odometer && $vehicle->current_odometer >= $schedule->next_due_odometer) {
                                $overdue = true;
                                $statusText = 'Overdue (KM)';
                                }

                                if ($overdue) {
                                $color = 'text-red-600 bg-red-100';
                                }
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $vehicle->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $vehicle->vehicle_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 uppercase">
                                        {{ str_replace('_', ' ', $schedule->service_type) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Odo: {{ number_format($schedule->last_service_odometer) }}<br>
                                        Date: {{ $schedule->last_service_date ? $schedule->last_service_date->format('Y-m-d') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ ($schedule->next_due_date && $schedule->next_due_date < now()) ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                        {{ $schedule->next_due_date ? $schedule->next_due_date->format('Y-m-d') : 'Not Set' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($schedule->next_due_odometer)
                                        <span class="{{ $vehicle->current_odometer >= $schedule->next_due_odometer ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                            {{ number_format($schedule->next_due_odometer) }}
                                        </span>
                                        <div class="text-xs text-gray-500">Current: {{ number_format($vehicle->current_odometer) }}</div>
                                        @else
                                        <span class="text-gray-500">Not Set</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('company.maintenance.create', ['vehicle_id' => $vehicle->id, 'type' => 'preventive']) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded">Create Job</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                        All fleet vehicles are completely healthy. No preventive maintenance required at this moment.
                                    </td>
                                </tr>
                                @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection