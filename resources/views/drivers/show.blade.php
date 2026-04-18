@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Driver Profile: {{ $driver->name }}</h1>
        <a href="{{ route('company.drivers.index') }}" class="btn btn-outline-secondary">Back to List</a>
    </div>

    <div class="row">
        <!-- Sidebar Info -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="avatar-circle mx-auto mb-3 bg-primary text-white d-flex justify-content-center align-items-center rounded-circle" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr($driver->name, 0, 1)) }}
                    </div>
                    <h4 class="card-title">{{ $driver->name }}</h4>
                    <p class="text-muted">{{ $driver->email }}</p>
                    <hr>
                    <div class="text-start">
                        <p><strong>Status:</strong> <span class="badge bg-{{ $driver->driverProfile->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($driver->driverProfile->status ?? 'Unknown') }}</span></p>
                        <p><strong>Phone:</strong> {{ $driver->phone_number ?? 'N/A' }}</p>
                        <p><strong>Joined:</strong> {{ $driver->created_at->format('d M Y') }}</p>
                        <p><strong>Branch:</strong> {{ $driver->branches->first()->name ?? 'Unassigned' }}</p>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Documents</h5>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse($driver->driverProfile->documents as $doc)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ ucfirst($doc->document_type) }}</strong>
                            <br>
                            <small class="text-muted">{{ $doc->document_number }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $doc->expiry_date->isPast() ? 'danger' : 'success' }}">
                                Exp: {{ $doc->expiry_date->format('M Y') }}
                            </span>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-muted text-center">No documents uploaded.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Performance Metrics -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <h6 class="card-title opacity-75">Total Trips</h6>
                            <h2 class="mb-0">{{ $metrics['total_trips'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <h6 class="card-title opacity-75">On-Time Rate</h6>
                            <h2 class="mb-0">{{ $metrics['on_time_rate'] }}%</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body">
                            <h6 class="card-title opacity-75">Completion Rate</h6>
                            <h2 class="mb-0">{{ $metrics['completion_rate'] }}%</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-secondary text-white h-100">
                        <div class="card-body">
                            <h6 class="card-title opacity-75">Avg Duration</h6>
                            <h2 class="mb-0">{{ $metrics['avg_trip_duration_hrs'] }}h</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Rentals -->
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Trips</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Contract #</th>
                                <th>Vehicle</th>
                                <th>Customer</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentrentals as $rental)
                            <tr>
                                <td>{{ $rental->start_date->format('d M Y') }}</td>
                                <td><a href="{{ route('rentals.show', $rental) }}">{{ $rental->contract_number }}</a></td>
                                <td>{{ $rental->bus->plate_number ?? 'N/A' }}</td>
                                <td>{{ $rental->customer->name }}</td>
                                <td>
                                    <span class="badge bg-{{ $rental->status === 'completed' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($rental->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No recent trips found.</td>
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