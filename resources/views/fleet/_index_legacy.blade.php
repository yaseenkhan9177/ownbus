@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Fleet Dashboard</h1>
        <div>
            <a href="{{ route('fleet.index', ['date' => $startDate->copy()->subDays(7)->toDateString()]) }}" class="btn btn-outline-secondary">&lt; Prev</a>
            <span class="mx-2 fw-bold">{{ $startDate->format('d M') }} - {{ $endDate->format('d M Y') }}</span>
            <a href="{{ route('fleet.index', ['date' => $startDate->copy()->addDays(7)->toDateString()]) }}" class="btn btn-outline-secondary">Next &gt;</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 200px;">Vehicle</th>
                            @for($i = 0; $i < 7; $i++)
                                <th class="text-center">{{ $startDate->copy()->addDays($i)->format('D d') }}</th>
                                @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicles as $vehicle)
                        <tr>
                            <td class="fw-bold">
                                {{ $vehicle->vehicle_number }}
                                <div class="small text-muted">{{ $vehicle->type }}</div>
                            </td>
                            @for($i = 0; $i < 7; $i++)
                                @php
                                $day=$startDate->copy()->addDays($i);
                                // Simple overlap check for display
                                $booking = $vehicle->bookings->first(function($b) use ($day) {
                                return $day->between($b->start_datetime, $b->end_datetime) ||
                                $day->isSameDay($b->start_datetime) ||
                                $day->isSameDay($b->end_datetime);
                                });
                                @endphp
                                <td class="p-1 {{ $booking ? 'bg-primary-subtle' : '' }}">
                                    @if($booking)
                                    <a href="{{ route('rentals.show', $booking) }}" class="d-block text-truncate small text-primary" style="max-width: 100px;">
                                        #{{ $booking->contract_number }}
                                    </a>
                                    @endif
                                </td>
                                @endfor
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection