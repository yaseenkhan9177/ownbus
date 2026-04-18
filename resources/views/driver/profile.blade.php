@extends('layouts.driver')

@section('title', 'My Profile')
@section('page-title', '👤 My Profile')

@section('content')

{{-- Driver Info Card --}}
<div class="card card-teal" style="display: flex; align-items: center; gap: 1rem; position: relative; overflow: hidden;">
    <div style="position: absolute; top: -2rem; right: -2rem; width: 7rem; height: 7rem; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
    <div style="width: 3.5rem; height: 3.5rem; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: 800; color: #fff; flex-shrink: 0;">
        {{ strtoupper(substr($driver->first_name, 0, 1)) }}{{ strtoupper(substr($driver->last_name, 0, 1)) }}
    </div>
    <div>
        <p style="font-size: 1rem; font-weight: 800; color: #fff; margin: 0;">{{ $driver->name }}</p>
        <p style="font-size: 0.75rem; color: rgba(255,255,255,0.7); margin: 0.1rem 0 0;">{{ $driver->driver_code ?? 'Driver' }} • {{ $driver->license_type ?? '' }}</p>
        <p style="font-size: 0.7rem; color: rgba(255,255,255,0.6); margin: 0.25rem 0 0;">{{ $driver->phone }}</p>
    </div>
</div>

{{-- Document Compliance Status --}}
<div style="margin-top: 1.25rem;">
    <p style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #475569; margin: 0 0 0.75rem;">UAE Document Status</p>

    @if(count($complianceStatus) > 0)
    @foreach($complianceStatus as $doc)
    @php
    $colorMap = ['red' => ['bg' => 'rgba(239,68,68,0.08)', 'border' => 'rgba(239,68,68,0.25)', 'text' => '#f87171', 'badge' => 'badge-rose', 'label' => 'EXPIRED'],
    'orange' => ['bg' => 'rgba(249,115,22,0.08)', 'border' => 'rgba(249,115,22,0.25)', 'text' => '#fb923c', 'badge' => 'badge-amber', 'label' => $doc['days_left'] . 'D LEFT'],
    'green' => ['bg' => 'rgba(16,185,129,0.06)', 'border' => 'rgba(16,185,129,0.15)', 'text' => '#34d399', 'badge' => 'badge-emerald', 'label' => 'VALID']];
    $c = $colorMap[$doc['risk']] ?? $colorMap['green'];
    @endphp
    <div class="card" style="padding: 0.875rem 1rem; margin-bottom: 0.625rem; border-color: {{ $c['border'] }}; background: {{ $c['bg'] }};">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="font-size: 0.8rem; font-weight: 700; color: #f1f5f9; margin: 0;">{{ $doc['label'] }}</p>
                <p style="font-size: 0.7rem; color: #64748b; margin: 0.2rem 0 0;">Expires: {{ $doc['expiry']->format('d M Y') }}</p>
            </div>
            <span class="badge {{ $c['badge'] }}">{{ $c['label'] }}</span>
        </div>
    </div>
    @endforeach
    @else
    <div class="card" style="text-align: center; padding: 1.5rem;">
        <p style="color: #64748b; font-size: 0.8rem;">No document records found.</p>
    </div>
    @endif
</div>

{{-- Driver Details --}}
<div style="margin-top: 1.25rem;">
    <p style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #475569; margin: 0 0 0.75rem;">Details</p>

    <div class="card" style="padding: 0;">
        @foreach([
        ['label' => 'Email', 'value' => $driver->email ?? 'N/A'],
        ['label' => 'License No.', 'value' => $driver->license_number ?? 'N/A'],
        ['label' => 'Hire Date', 'value' => $driver->hire_date?->format('d M Y') ?? 'N/A'],
        ['label' => 'Branch', 'value' => $driver->branch->name ?? 'N/A'],
        ['label' => 'Emergency Contact', 'value' => ($driver->emergency_contact_name ?? '—') . ' / ' . ($driver->emergency_contact_phone ?? '—')],
        ] as $i => $item)
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.875rem 1.25rem; {{ $i > 0 ? 'border-top: 1px solid rgba(255,255,255,0.05);' : '' }}">
            <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b;">{{ $item['label'] }}</span>
            <span style="font-size: 0.8rem; font-weight: 600; color: #94a3b8; text-align: right; max-width: 55%;">{{ $item['value'] }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- Logout --}}
<div style="margin-top: 1.5rem;">
    <form method="POST" action="{{ route('driver.logout') }}">
        @csrf
        <button type="submit" class="btn btn-danger" style="opacity: 0.8;">🔒 Log Out</button>
    </form>
</div>

@endsection