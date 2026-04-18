@extends('layouts.company')

@section('title', 'Breakdown Details')

@section('content')
<div class="container-fluid px-4 py-6">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('company.breakdowns.index') }}">Breakdowns</a></li>
            <li class="breadcrumb-item active">#BR-{{ $breakdown->id }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        {{-- Main Info --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2 text-primary"></i> Incident Details</h5>
                        <div class="d-flex gap-2">
                            @if($breakdown->status === 'pending')
                            <form action="{{ route('company.breakdowns.acknowledge', $breakdown) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="btn btn-warning btn-sm">Acknowledge Report</button>
                            </form>
                            @endif
                            @if($breakdown->status !== 'completed')
                            <form action="{{ route('company.breakdowns.resolve', $breakdown) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="btn btn-success btn-sm">Mark Resolved</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Issue Description</label>
                            <p class="lead fw-semibold text-dark">{{ $breakdown->notes }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Reported Location</label>
                            <p class="h6"><i class="bi bi-geo-alt me-1 text-danger"></i> {{ $breakdown->metadata['location'] ?? 'Unknown' }}</p>
                        </div>
                    </div>

                    @if($breakdown->photo_path)
                    <div class="mb-4">
                        <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Evidence Image</label>
                        <div class="rounded-3 overflow-hidden border" style="max-height: 500px; display: inline-block;">
                            <img src="{{ Storage::url($breakdown->photo_path) }}" class="img-fluid" alt="Breakdown Photo">
                        </div>
                    </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded-3">
                                <label class="small text-muted d-block mb-1">Reported At</label>
                                <div class="fw-bold">{{ $breakdown->reported_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded-3">
                                <label class="small text-muted d-block mb-1">Acknowledge At</label>
                                <div class="fw-bold">{{ $breakdown->acknowledged_at ? $breakdown->acknowledged_at->format('d M Y, H:i') : 'Pending' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded-3">
                                <label class="small text-muted d-block mb-1">Current Status</label>
                                <div class="fw-bold text-uppercase">{{ $breakdown->status }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Context --}}
        <div class="col-lg-4">
            {{-- Vehicle Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Vehicle Info</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-pill p-3 me-3">
                            <i class="bi bi-bus-front h4 mb-0"></i>
                        </div>
                        <div>
                            <div class="fw-bold h6 mb-0">{{ $breakdown->vehicle->vehicle_number ?? 'N/A' }}</div>
                            <div class="text-muted small">{{ $breakdown->vehicle->make ?? '' }} {{ $breakdown->vehicle->model ?? '' }}</div>
                        </div>
                    </div>
                    <a href="{{ route('company.fleet.index') }}" class="btn btn-light btn-sm w-100 border">View History</a>
                </div>
            </div>

            {{-- Driver Card --}}
            <div class="card border-0 shadow-sm mb-4 text-center">
                <div class="card-body py-4">
                    <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-person h1 mb-0"></i>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $breakdown->driver->name ?? 'Unknown Driver' }}</h5>
                    <p class="text-muted small mb-3">{{ $breakdown->driver->phone ?? '' }}</p>
                    <div class="d-grid">
                        <a href="tel:{{ $breakdown->driver->phone }}" class="btn btn-primary">
                            <i class="bi bi-telephone-fill me-2"></i> Call Driver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
