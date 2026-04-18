@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Rental #{{ $rental->contract_number }}</h1>
            <span class="badge bg-secondary">{{ ucfirst($rental->status) }}</span>
        </div>
        <div>
            <a href="{{ route('rentals.index') }}" class="btn btn-outline-secondary me-2">Back</a>
            @if($rental->status === 'draft' || $rental->status === 'quoted')
            <!-- Edit Button? -->
            @endif
        </div>
    </div>

    <!-- Status Actions -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Actions</h5>
            <div class="d-flex gap-2">
                @if($rental->status === 'draft')
                <form action="{{ route('rentals.transition', $rental) }}" method="POST">
                    @csrf
                    <input type="hidden" name="to_status" value="quoted">
                    <button type="submit" class="btn btn-info text-white">Send Quote</button>
                </form>
                <form action="{{ route('rentals.transition', $rental) }}" method="POST">
                    @csrf
                    <input type="hidden" name="to_status" value="confirmed">
                    <button type="submit" class="btn btn-success">Confirm Booking</button>
                </form>
                @elseif($rental->status === 'quoted')
                <form action="{{ route('rentals.transition', $rental) }}" method="POST">
                    @csrf
                    <input type="hidden" name="to_status" value="confirmed">
                    <button type="submit" class="btn btn-success">Approve & Confirm</button>
                </form>
                @elseif($rental->status === 'confirmed')
                <button class="btn btn-primary" disabled>Assign (Edit)</button>
                <form action="{{ route('rentals.transition', $rental) }}" method="POST">
                    @csrf
                    <input type="hidden" name="to_status" value="assigned">
                    <button type="submit" class="btn btn-warning">Ready for Dispatch</button>
                </form>
                @elseif($rental->status === 'assigned')
                <form action="{{ route('rentals.transition', $rental) }}" method="POST">
                    @csrf
                    <input type="hidden" name="to_status" value="dispatched">
                    <button type="submit" class="btn btn-warning">Dispatch Bus</button>
                </form>
                @endif

                @if(!in_array($rental->status, ['cancelled', 'closed']))
                <form action="{{ route('rentals.transition', $rental) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    <input type="hidden" name="to_status" value="cancelled">
                    <button type="submit" class="btn btn-danger">Cancel</button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Details Column -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Trip Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Customer:</strong>
                            <p>{{ $rental->customer->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Type:</strong>
                            <p>{{ ucfirst($rental->rental_type) }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Start:</strong>
                            <p>{{ $rental->start_date->format('d M Y, H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>End:</strong>
                            <p>{{ $rental->end_date->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Pickup:</strong>
                            <p>{{ $rental->pickup_location }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Dropoff:</strong>
                            <p>{{ $rental->dropoff_location ?? 'Same as Pickup' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Audit Log -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">History</h5>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($rental->statusLogs as $log)
                    <li class="list-group-item">
                        <small class="text-muted">{{ $log->created_at->format('d M Y, H:i') }}</small>
                        <br>
                        <strong>{{ ucfirst($log->from_status ?? 'New') }}</strong> &rarr; <strong>{{ ucfirst($log->to_status) }}</strong>
                        @if($log->user)
                        <span class="text-muted">by {{ $log->user->name }}</span>
                        @endif
                        @if($log->reason)
                        <div class="text-muted small mt-1">"{{ $log->reason }}"</div>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Helpers Column -->
        <div class="col-md-4">
            <!-- Assignment -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Assignment</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Vehicle:</strong>
                        <div>
                            @if($rental->bus)
                            <a href="#">{{ $rental->bus->vehicle_number }}</a> ({{ $rental->bus->name }})
                            @else
                            <span class="text-warning">Unassigned</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-2">
                        <strong>Driver:</strong>
                        <div>
                            @if($rental->driver)
                            <a href="#">{{ $rental->driver->name }}</a>
                            @else
                            <span class="text-warning">Unassigned</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financials -->
            <div class="card shadow-sm border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Financials</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Base Price:</span>
                        <span>{{ number_format($quote->base_rent, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Projected Extras:</span>
                        <span>{{ number_format($quote->extra_hours_charge + $quote->extra_km_charge, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total Estimate:</span>
                        <span>{{ number_format($quote->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection