@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Customer Profile: {{ $customer->name }}</h1>
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Back to List</a>
    </div>

    <div class="row">
        <!-- Sidebar Info -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="card-title">{{ $customer->name }}</h4>
                    @if($customer->company_name)
                    <h6 class="card-subtitle mb-2 text-muted">{{ $customer->company_name }}</h6>
                    @endif
                    <hr>
                    <div class="mb-2"><strong>Type:</strong> <span class="badge bg-secondary">{{ ucfirst($customer->type) }}</span></div>
                    <div class="mb-2"><strong>Email:</strong> {{ $customer->email }}</div>
                    <div class="mb-2"><strong>Phone:</strong> {{ $customer->phone }}</div>
                    <div class="mb-2"><strong>Address:</strong> {{ $customer->address }}</div>
                    @if($customer->trn_number)
                    <div class="mb-2"><strong>TRN:</strong> {{ $customer->trn_number }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Metrics -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <h6 class="card-title opacity-75">Total Spend</h6>
                            <h3 class="mb-0">{{ number_format($metrics['total_spend'], 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <h6 class="card-title opacity-75">Total Rentals</h6>
                            <h3 class="mb-0">{{ $metrics['total_rentals'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body">
                            <h6 class="card-title opacity-75">Active Rentals</h6>
                            <h3 class="mb-0">{{ $metrics['active_rentals'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-secondary text-white h-100">
                        <div class="card-body">
                            <h6 class="card-title opacity-75">Avg Value</h6>
                            <h3 class="mb-0">{{ number_format($metrics['average_rental_value'], 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Rentals -->
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Rentals</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Ref #</th>
                                <th>Vehicle</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->rentals as $rental)
                            <tr>
                                <td>{{ $rental->start_date->format('d M Y') }}</td>
                                <td><a href="{{ route('rentals.show', $rental) }}">{{ $rental->contract_number }}</a></td>
                                <td>{{ $rental->bus->plate_number ?? 'N/A' }}</td>
                                <td>{{ number_format($rental->final_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $rental->status === 'completed' ? 'success' : ($rental->status === 'cancelled' ? 'danger' : 'primary') }}">
                                        {{ ucfirst($rental->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No recent rentals found.</td>
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