@extends('layouts.company')

@section('title', 'Breakdown Management')

@section('content')
<div class="container-fluid px-4 py-6">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">
                <i class="bi bi-exclamation-triangle me-2 text-danger"></i> Breakdown Reports
            </h1>
            <p class="text-muted mb-0">Manage vehicle breakdowns and emergency incident reports</p>
        </div>
        @if($pendingCount > 0)
        <span class="badge bg-danger px-3 py-2 rounded-pill">
            <i class="bi bi-bell-fill me-1"></i> {{ $pendingCount }} Unacknowledged
        </span>
        @endif
    </div>

    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="acknowledged" {{ request('status') == 'acknowledged' ? 'selected' : '' }}>Acknowledged</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Resolved</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    {{-- Reports List --}}
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Report Info</th>
                        <th>Vehicle / Driver</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Reported</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">#BR-{{ $report->id }}</div>
                            <div class="text-muted small text-truncate" style="max-width: 250px;">
                                {{ $report->notes }}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary me-2">
                                    {{ $report->vehicle->vehicle_number ?? 'N/A' }}
                                </span>
                                <div class="small">
                                    <div class="fw-semibold text-dark">{{ $report->driver->name ?? 'Unknown' }}</div>
                                    <div class="text-muted">{{ $report->driver->phone ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small text-dark">
                                <i class="bi bi-geo-alt me-1 text-danger"></i>
                                {{ $report->metadata['location'] ?? 'Not specified' }}
                            </div>
                        </td>
                        <td>
                            @php
                                $statusClass = match($report->status) {
                                    'pending' => 'bg-danger',
                                    'acknowledged' => 'bg-warning text-dark',
                                    'completed' => 'bg-success',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }} rounded-pill px-3">
                                {{ strtoupper($report->status) }}
                            </span>
                        </td>
                        <td class="small text-muted">
                            <div>{{ $report->reported_at->format('d M, Y') }}</div>
                            <div>{{ $report->reported_at->format('H:i') }}</div>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('company.breakdowns.show', $report) }}" class="btn btn-outline-primary" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($report->status === 'pending')
                                <form action="{{ route('company.breakdowns.acknowledge', $report) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-outline-warning" title="Acknowledge">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                </form>
                                @endif
                                @if($report->status !== 'completed')
                                <form action="{{ route('company.breakdowns.resolve', $report) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-outline-success" title="Mark Resolved" onclick="return confirm('Mark this issue as resolved?')">
                                        <i class="bi bi-check2-all"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-check-circle-fill text-success h1 d-block mb-3 opacity-25"></i>
                            <div class="text-muted">No active breakdown reports</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reports->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $reports->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
