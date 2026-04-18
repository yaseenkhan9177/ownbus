@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Rentals & Quotes</h1>
        <a href="{{ route('rentals.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> New Quote
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('rentals.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Contract # or Customer Name">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        @foreach(['draft', 'quoted', 'confirmed', 'active', 'completed', 'cancelled'] as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 me-2"><i class="bi bi-search"></i> Filter</button>
                    <a href="{{ route('rentals.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
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
                            <th>Ref #</th>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Dates</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rentals as $rental)
                        <tr>
                            <td class="fw-bold">
                                <a href="{{ route('rentals.show', $rental) }}" class="text-decoration-none">
                                    {{ $rental->contract_number }}
                                </a>
                            </td>
                            <td>
                                {{ $rental->customer->name }}
                                <div class="small text-muted">{{ $rental->customer->company_name }}</div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($rental->rental_type) }}</span>
                            </td>
                            <td>
                                <div>{{ $rental->start_date->format('d M Y, H:i') }}</div>
                                <div class="small text-muted">to {{ $rental->end_date->format('d M Y, H:i') }}</div>
                            </td>
                            <td>
                                @php
                                $badges = [
                                'draft' => 'secondary',
                                'quoted' => 'info',
                                'confirmed' => 'primary',
                                'active' => 'success',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                'closed' => 'dark',
                                ];
                                $badge = $badges[$rental->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ ucfirst($rental->status) }}</span>
                            </td>
                            <td>{{ number_format($rental->final_amount, 2) }}</td>
                            <td>
                                <a href="{{ route('rentals.show', $rental) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No rentals found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {{ $rentals->links() }}
            </div>
        </div>
    </div>
</div>
@endsection