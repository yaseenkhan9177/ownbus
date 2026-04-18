@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Fleet Intelligence</h1>
        <form action="{{ route('fleet.analytics') }}" method="GET" class="d-flex gap-2">
            <select name="branch_id" class="form-select">
                <option value="">All Branches</option>
                @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>
                    {{ $branch->name }}
                </option>
                @endforeach
            </select>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate->toDateString() }}">
            <input type="date" name="end_date" class="form-control" value="{{ $endDate->toDateString() }}">
            <button type="submit" class="btn btn-primary">Refresh</button>
        </form>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Utilization Rate</h6>
                    <h2 class="text-primary">{{ $utilizationRate }}%</h2>
                    <small>Time-based (Days Rented / Capacity)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Revenue / KM</h6>
                    <h2 class="text-success">${{ $revenuePerKm }}</h2>
                    <small>Avg Revenue per Kilometer</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Maintenance / KM</h6>
                    <h2 class="text-danger">${{ $maintenancePerKm }}</h2>
                    <small>Avg Cost per Kilometer</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Idle Vehicles (7d)</h6>
                    <h2 class="text-warning">{{ $idleVehicles->count() }}</h2>
                    <small>Vehicles with no bookings</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Drivers -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Top Drivers (Safety & Activity)</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Driver</th>
                                <th class="text-center">Safety Score</th>
                                <th class="text-center">Trips</th>
                                <th class="text-end">KM Driven</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topDrivers as $metric)
                            <tr>
                                <td>{{ $metric->user->name ?? 'Unknown' }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $metric->safety_score >= 95 ? 'bg-success' : ($metric->safety_score >= 80 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ $metric->safety_score }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $metric->trips_completed }}</td>
                                <td class="text-end">{{ number_format($metric->total_km_driven) }} km</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3">No driver data for this period.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Idle Vehicles List -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Idle Fleet Attention Required</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($idleVehicles as $vehicle)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $vehicle->vehicle_number }}</strong> - {{ $vehicle->make }} {{ $vehicle->model }}
                                <br>
                                <small class="text-muted">{{ $vehicle->branch->name ?? 'Unassigned' }}</small>
                            </div>
                            <span class="badge bg-secondary">{{ $vehicle->status }}</span>
                        </li>
                        @empty
                        <li class="list-group-item text-center py-3">No idle vehicles found. Good utilization!</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection