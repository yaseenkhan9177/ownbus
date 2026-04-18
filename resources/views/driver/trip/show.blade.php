@extends('layouts.driver')

@section('title', 'Trip Details')
@section('page-title', '🚌 Trip Details')

@section('content')

{{-- Trip Status Banner --}}
@php
$statusColors = ['active' => 'teal', 'confirmed' => 'amber', 'completed' => 'emerald', 'cancelled' => 'rose'];
$statusColor = $statusColors[$rental->status] ?? 'slate';
@endphp

<div class="card" style="border-color: rgba(20,184,166,0.3); padding: 1.25rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div>
            <p style="font-size: 0.6rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; margin: 0 0 0.2rem;">Contract No.</p>
            <p style="font-size: 1rem; font-weight: 800; color: #14b8a6; margin: 0;">{{ $rental->contract_number ?? '#' . $rental->id }}</p>
        </div>
        <span class="badge badge-{{ $statusColor }}">{{ strtoupper($rental->status) }}</span>
    </div>

    {{-- Vehicle Info --}}
    <div style="background: rgba(0,0,0,0.2); border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem;">
        <p style="font-size: 0.6rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; margin: 0 0 0.5rem;">Vehicle</p>
        <p style="font-size: 1rem; font-weight: 800; color: #f1f5f9; margin: 0;">{{ $rental->vehicle->name ?? 'N/A' }}</p>
        <p style="font-size: 0.75rem; color: #64748b; margin: 0.2rem 0 0;">{{ $rental->vehicle->vehicle_number ?? '' }} • {{ $rental->vehicle->make ?? '' }} {{ $rental->vehicle->model ?? '' }}</p>
    </div>

    {{-- Customer Info --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
        <div>
            <p style="font-size: 0.6rem; color: #64748b; margin: 0 0 0.2rem; text-transform: uppercase;">Customer</p>
            <p style="font-size: 0.85rem; font-weight: 700; color: #f1f5f9; margin: 0;">{{ $rental->customer->name ?? 'N/A' }}</p>
        </div>
        <div>
            <p style="font-size: 0.6rem; color: #64748b; margin: 0 0 0.2rem; text-transform: uppercase;">Phone</p>
            <a href="tel:{{ $rental->customer->phone ?? '' }}" style="font-size: 0.85rem; font-weight: 700; color: #14b8a6; margin: 0; text-decoration: none;">{{ $rental->customer->phone ?? 'N/A' }}</a>
        </div>
        <div>
            <p style="font-size: 0.6rem; color: #64748b; margin: 0 0 0.2rem; text-transform: uppercase;">Start Date</p>
            <p style="font-size: 0.85rem; font-weight: 700; color: #f1f5f9; margin: 0;">{{ $rental->start_date->format('d M Y') }}</p>
        </div>
        <div>
            <p style="font-size: 0.6rem; color: #64748b; margin: 0 0 0.2rem; text-transform: uppercase;">End Date</p>
            <p style="font-size: 0.85rem; font-weight: 700; color: #f1f5f9; margin: 0;">{{ $rental->end_date->format('d M Y') }}</p>
        </div>
    </div>

    @if($rental->notes)
    <div style="background: rgba(0,0,0,0.2); border-radius: 0.625rem; padding: 0.75rem;">
        <p style="font-size: 0.6rem; color: #64748b; margin: 0 0 0.2rem; text-transform: uppercase; font-weight: 700;">Trip Notes</p>
        <p style="font-size: 0.8rem; color: #94a3b8; margin: 0;">{{ $rental->notes }}</p>
    </div>
    @endif
</div>

{{-- Action Buttons --}}
@if($rental->status === 'confirmed')
<form method="POST" action="{{ route('driver.trip.start', $rental) }}" style="margin-bottom: 0.75rem;">
    @csrf
    <div style="margin-bottom: 1rem; background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 0.75rem;">
        <label class="form-label" style="color: #94a3b8;">Current Odometer (Start)</label>
        <input type="number" name="odometer_start" class="form-input" 
               value="{{ $rental->vehicle->current_odometer ?? '' }}" 
               placeholder="Confirm current odometer..." required>
        <p style="font-size: 0.65rem; color: #64748b; margin-top: 0.4rem;">Please verify the odometer reading before starting.</p>
    </div>
    <input type="hidden" name="lat" id="trip_lat">
    <input type="hidden" name="lng" id="trip_lng">
    <button type="submit" class="btn btn-teal">
        ▶️ Start Trip Now
    </button>
</form>
@elseif($rental->status === 'active')
<div class="card" style="border-color: rgba(239,68,68,0.2); padding: 1.25rem;">
    <p style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; margin: 0 0 1rem;">Complete Trip</p>
    <form method="POST" action="{{ route('driver.trip.complete', $rental) }}">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.875rem;">
            <div>
                <label class="form-label text-muted x-small">Odometer (End)</label>
                <input type="number" name="odometer_end" class="form-input" placeholder="Current km..." required>
            </div>
            <div>
                <label class="form-label text-muted x-small">Fuel Used (Liters)</label>
                <input type="number" step="0.01" name="fuel_used_liters" class="form-input" placeholder="Liters...">
            </div>
        </div>

        <div style="margin-bottom: 1rem;">
            <label class="form-label text-muted x-small">Self Rating</label>
            <select name="rating" class="form-input">
                <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                <option value="4">⭐⭐⭐⭐ Good</option>
                <option value="3">⭐⭐⭐ Average</option>
                <option value="2">⭐⭐ Fair</option>
                <option value="1">⭐ Poor</option>
            </select>
        </div>

        <div style="margin-bottom: 1rem;">
            <label class="form-label text-muted x-small">Completion Notes</label>
            <textarea name="notes" class="form-input" rows="2" placeholder="Traffic, vehicle issues, etc..."></textarea>
        </div>

        <input type="hidden" name="lat" id="trip_lat_end">
        <input type="hidden" name="lng" id="trip_lng_end">

        <button type="submit" class="btn btn-danger">✅ Mark Trip Completed</button>
    </form>
</div>
@elseif($rental->status === 'completed')
<div class="card" style="text-align: center; padding: 1.5rem; border-color: rgba(16,185,129,0.3);">
    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">✅</div>
    <p style="font-size: 0.875rem; font-weight: 700; color: #10b981; margin: 0;">Trip Completed</p>
    @if($trip && $trip->distance_km)
        <p style="font-size: 0.75rem; color: #94a3b8; margin: 0.5rem 0 0;">
            Distance: <strong>{{ $trip->distance_km }} km</strong> • 
            Time: <strong>{{ $trip->getFormattedDuration() }}</strong>
        </p>
    @endif
</div>
@endif

<script>
    // Basic GPS acquisition for trip records
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            if(document.getElementById('trip_lat')) document.getElementById('trip_lat').value = lat;
            if(document.getElementById('trip_lng')) document.getElementById('trip_lng').value = lng;
            if(document.getElementById('trip_lat_end')) document.getElementById('trip_lat_end').value = lat;
            if(document.getElementById('trip_lng_end')) document.getElementById('trip_lng_end').value = lng;
        });
    }
</script>

<a href="{{ route('driver.dashboard') }}" class="btn btn-outline" style="margin-top: 1rem;">
    ← Back to Dashboard
</a>

@endsection