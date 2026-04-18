@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Maintenance Schedule</h1>
        <a href="{{ route('fleet.index') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row">
        <!-- Create Form -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Block Vehicle</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('fleet.maintenance.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Vehicle</label>
                            <select name="vehicle_id" class="form-select" required>
                                <option value="">Select Vehicle</option>
                                @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_number }} - {{ $vehicle->type }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Start Date & Time</label>
                            <input type="datetime-local" name="start_datetime" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">End Date & Time</label>
                            <input type="datetime-local" name="end_datetime" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Reason Type</label>
                            <select name="reason_type" class="form-select" required>
                                <option value="maintenance">Routine Maintenance</option>
                                <option value="repair">Urgent Repair</option>
                                <option value="inspection">Inspection / Registration</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-danger w-100">Block Dates</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- List -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Active & Upcoming Blocks</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Vehicle</th>
                                <th>Reason</th>
                                <th>From</th>
                                <th>To</th>
                                <th>By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($unavailabilities as $block)
                            <tr>
                                <td>{{ $block->vehicle->vehicle_number }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst($block->reason_type) }}</span>
                                </td>
                                <td>{{ $block->start_datetime->format('d M Y, H:i') }}</td>
                                <td>{{ $block->end_datetime->format('d M Y, H:i') }}</td>
                                <td><small class="text-muted">{{ $block->creator->name ?? 'System' }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No maintenance blocks found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $unavailabilities->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection