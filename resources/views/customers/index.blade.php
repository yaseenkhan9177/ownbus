@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Customers</h1>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('customers.index') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Name, Email, Phone or Company">
                </div>
                <div class="col-md-3">
                    <label for="type" class="form-label">Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">All Types</option>
                        <option value="individual" {{ request('type') == 'individual' ? 'selected' : '' }}>Individual</option>
                        <option value="corporate" {{ request('type') == 'corporate' ? 'selected' : '' }}>Corporate</option>
                        <option value="government" {{ request('type') == 'government' ? 'selected' : '' }}>Government</option>
                    </select>
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
                            <th>Name / Company</th>
                            <th>Contact Info</th>
                            <th>Type</th>
                            <th>Total Rentals</th>
                            <th>Total Spend</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $customer->name }}</div>
                                @if($customer->company_name)
                                <div class="small text-muted"><i class="bi bi-building"></i> {{ $customer->company_name }}</div>
                                @endif
                            </td>
                            <td>
                                <div><i class="bi bi-envelope"></i> {{ $customer->email }}</div>
                                <div class="small text-muted"><i class="bi bi-telephone"></i> {{ $customer->phone }}</div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $customer->type === 'corporate' ? 'primary' : 'secondary' }}">
                                    {{ ucfirst($customer->type) }}
                                </span>
                            </td>
                            <td>{{ $customer->rentals_count }}</td>
                            <td>{{ number_format($customer->rentals_sum_final_amount, 2) }}</td>
                            <td>
                                <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-outline-primary">View Profile</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No customers found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {{ $customers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection