@extends('layouts.driver')

@section('title', 'Dashboard')
@section('page-title', 'Good ' . (now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening' )) . ', ' . explode(' ', session(' driver_name', 'Driver' ))[0] . '! 👋' )

        @section('content')

        {{-- ── Today's Trip Card ─────────────────────────────── --}}
        @if($todayRental)
        <div class="card card-teal" style="position: relative; overflow: hidden;">
        <div style="position: absolute; top: -2rem; right: -2rem; width: 8rem; height: 8rem; background: rgba(255,255,255,0.07); border-radius: 50%;"></div>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
            <div>
                <p style="font-size: 0.6rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.7); margin: 0 0 0.25rem;">Today's Active Trip</p>
                <h2 style="font-size: 1.1rem; font-weight: 800; color: #fff; margin: 0;">{{ $todayRental->vehicle->name ?? 'N/A' }}</h2>
                <p style="font-size: 0.75rem; color: rgba(255,255,255,0.7); margin: 0.2rem 0 0;">{{ $todayRental->vehicle->vehicle_number ?? '' }}</p>
            </div>
            <span class="badge" style="background: rgba(255,255,255,0.2); color: #fff;">
                {{ strtoupper($todayRental->status) }}
            </span>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem;">
            <div>
                <p style="font-size: 0.6rem; color: rgba(255,255,255,0.6); margin: 0 0 0.2rem; text-transform: uppercase;">Customer</p>
                <p style="font-size: 0.85rem; font-weight: 700; color: #fff; margin: 0;">{{ $todayRental->customer->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p style="font-size: 0.6rem; color: rgba(255,255,255,0.6); margin: 0 0 0.2rem; text-transform: uppercase;">Contract</p>
                <p style="font-size: 0.85rem; font-weight: 700; color: #fff; margin: 0;">{{ $todayRental->contract_number ?? '#' . $todayRental->id }}</p>
            </div>
            <div>
                <p style="font-size: 0.6rem; color: rgba(255,255,255,0.6); margin: 0 0 0.2rem; text-transform: uppercase;">Start Date</p>
                <p style="font-size: 0.85rem; font-weight: 700; color: #fff; margin: 0;">{{ $todayRental->start_date->format('d M Y') }}</p>
            </div>
            <div>
                <p style="font-size: 0.6rem; color: rgba(255,255,255,0.6); margin: 0 0 0.2rem; text-transform: uppercase;">End Date</p>
                <p style="font-size: 0.85rem; font-weight: 700; color: #fff; margin: 0;">{{ $todayRental->end_date->format('d M Y') }}</p>
            </div>
        </div>

        <a href="{{ route('driver.trip.show', $todayRental) }}" class="btn" style="background: rgba(255,255,255,0.15); color: #fff; border: 1.5px solid rgba(255,255,255,0.25);">
            View Trip Details →
        </a>
        </div>
        @else
        <div class="card" style="text-align: center; padding: 2.5rem 1.25rem;">
            <div style="font-size: 3rem; margin-bottom: 0.75rem;">🚌</div>
            <p style="font-size: 1rem; font-weight: 700; color: #94a3b8; margin: 0 0 0.25rem;">No Active Trip Today</p>
            <p style="font-size: 0.75rem; color: #475569; margin: 0;">Check upcoming trips below</p>
        </div>
        @endif

        {{-- ── Quick Actions ─────────────────────────────────── --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-top: 1.25rem; margin-bottom: 1rem;">
            <a href="{{ route('driver.fuel.create') }}" class="card" style="text-align: center; padding: 1rem; margin-bottom: 0; text-decoration: none;">
                <div style="font-size: 1.75rem; margin-bottom: 0.5rem;">⛽</div>
                <p style="font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Fuel Upload</p>
            </a>
            <a href="{{ route('driver.breakdown.create') }}" class="card" style="text-align: center; padding: 1rem; margin-bottom: 0; text-decoration: none;">
                <div style="font-size: 1.75rem; margin-bottom: 0.5rem;">🚨</div>
                <p style="font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Report Issue</p>
            </a>
        </div>

        {{-- ── Upcoming Trips ─────────────────────────────────── --}}
        @if($upcomingRentals->count() > 0)
        <div style="margin-top: 1.25rem;">
            <p style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #475569; margin: 0 0 0.75rem;">Upcoming Trips</p>
            @foreach($upcomingRentals as $rental)
            <div class="card" style="padding: 0.875rem 1rem; margin-bottom: 0.75rem;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 2.25rem; height: 2.25rem; border-radius: 0.625rem; background: rgba(20,184,166,0.1); display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 800; color: #14b8a6;">
                            {{ $rental->start_date->format('d') }}
                        </div>
                        <div>
                            <p style="font-size: 0.825rem; font-weight: 700; color: #f1f5f9; margin: 0;">{{ $rental->vehicle->name ?? 'Vehicle' }}</p>
                            <p style="font-size: 0.7rem; color: #64748b; margin: 0;">{{ $rental->start_date->format('d M') }} → {{ $rental->end_date->format('d M') }}</p>
                        </div>
                    </div>
                    <span class="badge badge-teal">{{ $rental->start_date->diffForHumans() }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- ── Recent Reports ─────────────────────────────────── --}}
        @if($recentReports->count() > 0)
        <div style="margin-top: 1.25rem;">
            <p style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #475569; margin: 0 0 0.75rem;">Recent Reports</p>
            @foreach($recentReports as $report)
            <div class="card" style="padding: 0.875rem 1rem; margin-bottom: 0.75rem;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 1.25rem;">{{ $report->type === 'fuel_upload' ? '⛽' : ($report->type === 'breakdown_report' ? '🚨' : '📍') }}</span>
                        <div>
                            <p style="font-size: 0.8rem; font-weight: 600; color: #f1f5f9; margin: 0; text-transform: capitalize;">{{ str_replace('_', ' ', $report->type) }}</p>
                            <p style="font-size: 0.7rem; color: #64748b; margin: 0;">{{ $report->reported_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <span class="badge badge-{{ $report->status === 'pending' ? 'amber' : 'emerald' }}">{{ $report->status }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- ── Logout ─────────────────────────────────── --}}
        <div style="margin-top: 2rem;">
            <form method="POST" action="{{ route('driver.logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline" style="color: #64748b; border-color: rgba(255,255,255,0.1); font-size: 0.75rem;">
                    🔒 Log Out
                </button>
            </form>
        </div>

        @endsection