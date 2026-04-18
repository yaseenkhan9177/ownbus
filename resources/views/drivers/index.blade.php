@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Drivers</h1>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('company.drivers.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Name or Email">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" id="license_expiring_soon" name="license_expiring_soon" value="1" {{ request('license_expiring_soon') ? 'checked' : '' }}>
                        <label class="form-check-label" for="license_expiring_soon">
                            License Expiring Soon
                        </label>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>License Expiry</th>
                            <th>Assigned Branch</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drivers as $driver)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3 bg-secondary text-white d-flex justify-content-center align-items-center rounded-circle" style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($driver->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $driver->name }}</div>
                                        <div class="small text-muted">{{ $driver->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                $status = $driver->driverProfile->status ?? 'unknown';
                                $badges = [
                                'active' => 'success',
                                'inactive' => 'secondary',
                                'on_leave' => 'warning',
                                'terminated' => 'danger',
                                ];
                                $badge = $badges[$status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                            </td>
                            <td>
                                @php
                                $license = $driver->driverProfile->documents->where('document_type', 'license')->first();
                                @endphp
                                @if($license)
                                <div class="{{ $license->expiry_date->isPast() ? 'text-danger fw-bold' : ($license->expiry_date->diffInDays(now()) < 30 ? 'text-warning fw-bold' : '') }}">
                                    {{ $license->expiry_date->format('d M Y') }}
                                </div>
                                @else
                                <span class="text-muted small">Not Uploaded</span>
                                @endif
                            </td>
                            <td>
                                {{ $driver->branches->first()->name ?? 'Unassigned' }}
                            </td>
                            <td>
                                <a href="{{ route('company.drivers.show', $driver) }}" class="btn btn-sm btn-outline-primary">View Profile</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No drivers found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {{ $drivers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection