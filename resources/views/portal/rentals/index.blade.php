@extends('layouts.company')

@section('title', 'Rental Management — OwnBus')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root {
    --bg: #0A0F1E;
    --card: #111827;
    --border: #1F2937;
    --primary: #00BCD4;
    --gold: #F59E0B;
    --success: #10B981;
    --warning: #F97316;
    --danger: #EF4444;
    --purple: #8B5CF6;
    --text: #F9FAFB;
    --muted: #6B7280;
    --radius: 12px;
}
.rnt-wrap * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
.rnt-wrap { background: var(--bg); min-height: 100vh; padding: 28px; }

/* Header */
.rnt-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; }
.rnt-header-left h1 { font-size: 24px; font-weight: 800; color: var(--text); margin: 0; letter-spacing: -0.5px; }
.rnt-header-left p { color: var(--muted); font-size: 13px; margin: 4px 0 0; }
.btn-new { display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, var(--primary), #0097A7); color: #fff; font-weight: 700; font-size: 13px; padding: 11px 20px; border-radius: var(--radius); text-decoration: none; transition: all .2s; box-shadow: 0 4px 20px rgba(0,188,212,.3); border: none; cursor: pointer; }
.btn-new:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,188,212,.4); color: #fff; }

/* Stats */
.stats-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 14px; margin-bottom: 24px; }
@media (max-width:1200px){ .stats-grid { grid-template-columns: repeat(3,1fr); } }
@media (max-width:768px){ .stats-grid { grid-template-columns: repeat(2,1fr); } }
.stat-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 18px; position: relative; overflow: hidden; transition: transform .2s, border-color .2s; }
.stat-card:hover { transform: translateY(-3px); border-color: var(--primary); }
.stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius: var(--radius) var(--radius) 0 0; }
.stat-teal::before { background: var(--primary); }
.stat-green::before { background: var(--success); }
.stat-orange::before { background: var(--warning); }
.stat-purple::before { background: var(--purple); }
.stat-gold::before { background: var(--gold); }
.stat-red::before { background: var(--danger); }
.stat-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-bottom: 12px; }
.stat-icon.teal { background: rgba(0,188,212,.15); }
.stat-icon.green { background: rgba(16,185,129,.15); }
.stat-icon.orange { background: rgba(249,115,22,.15); }
.stat-icon.purple { background: rgba(139,92,246,.15); }
.stat-icon.gold { background: rgba(245,158,11,.15); }
.stat-icon.red { background: rgba(239,68,68,.15); }
.stat-label { font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
.stat-value { font-size: 26px; font-weight: 800; color: var(--text); line-height: 1; }
.stat-sub { font-size: 11px; color: var(--muted); margin-top: 4px; }
.pulse-dot { display: inline-block; width: 8px; height: 8px; background: var(--success); border-radius: 50%; margin-right: 6px; animation: pulse 2s infinite; }
@keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.3)} }

/* Filter Bar */
.filter-bar { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 20px; margin-bottom: 24px; }
.filter-row { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; }
.filter-input { background: #0D1526; border: 1px solid var(--border); border-radius: 8px; color: var(--text); font-size: 13px; font-family: 'DM Sans',sans-serif; padding: 9px 13px; flex: 1; min-width: 150px; outline: none; transition: border-color .2s; }
.filter-input:focus { border-color: var(--primary); }
.filter-input::placeholder { color: var(--muted); }
.filter-search { position: relative; flex: 2; min-width: 200px; }
.filter-search input { padding-left: 38px; width: 100%; }
.filter-search svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--muted); }
.btn-filter { background: var(--primary); color: #fff; border: none; border-radius: 8px; padding: 9px 18px; font-weight: 700; font-size: 13px; cursor: pointer; font-family: 'DM Sans',sans-serif; transition: all .2s; white-space: nowrap; }
.btn-filter:hover { background: #00ACC1; }
.btn-clear { background: #1F2937; color: var(--muted); border: none; border-radius: 8px; padding: 9px 14px; font-weight: 700; font-size: 13px; cursor: pointer; font-family: 'DM Sans',sans-serif; text-decoration: none; display: inline-flex; align-items: center; white-space: nowrap; transition: all .2s; }
.btn-clear:hover { color: var(--text); }
.btn-export { background: #1F2937; color: var(--gold); border: 1px solid rgba(245,158,11,.2); border-radius: 8px; padding: 9px 14px; font-weight: 700; font-size: 12px; cursor: pointer; font-family: 'DM Sans',sans-serif; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; transition: all .2s; }
.btn-export:hover { background: rgba(245,158,11,.1); color: var(--gold); }

/* Table */
.table-wrap { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
.table-header-bar { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--border); }
.table-title { font-size: 14px; font-weight: 700; color: var(--text); }
.table-count { font-size: 12px; color: var(--muted); }
table.rnt-table { width: 100%; border-collapse: collapse; }
table.rnt-table thead th { padding: 12px 16px; font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid var(--border); background: rgba(255,255,255,.02); white-space: nowrap; }
table.rnt-table tbody tr { border-bottom: 1px solid rgba(31,41,55,.5); transition: background .15s; }
table.rnt-table tbody tr:last-child { border-bottom: none; }
table.rnt-table tbody tr:hover { background: rgba(0,188,212,.04); }
table.rnt-table td { padding: 14px 16px; vertical-align: middle; }

/* Rental # */
.rnt-num { font-family: monospace; font-size: 11px; font-weight: 700; color: var(--primary); background: rgba(0,188,212,.1); padding: 3px 8px; border-radius: 5px; text-decoration: none; display: inline-block; transition: all .2s; }
.rnt-num:hover { background: rgba(0,188,212,.2); color: var(--primary); }

/* Avatar */
.avatar { width: 32px; height: 32px; border-radius: 8px; font-size: 13px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.customer-cell { display: flex; align-items: center; gap: 10px; }
.customer-name { font-size: 13px; font-weight: 600; color: var(--text); }
.customer-sub { font-size: 11px; color: var(--muted); margin-top: 2px; }

/* Vehicle cell */
.vehicle-cell .v-name { font-size: 13px; font-weight: 600; color: var(--text); }
.vehicle-cell .v-plate { font-size: 11px; color: var(--muted); margin-top: 2px; }
.unassigned-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; color: var(--warning); background: rgba(249,115,22,.1); padding: 2px 8px; border-radius: 4px; }

/* Period */
.period-dates { font-size: 12px; font-weight: 600; color: var(--text); }
.period-duration { display: inline-block; background: rgba(139,92,246,.15); color: var(--purple); font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 4px; margin-top: 4px; }

/* Amount */
.amount { font-size: 14px; font-weight: 700; color: var(--gold); }
.amount-sub { font-size: 11px; color: var(--muted); margin-top: 2px; }

/* Status badges */
.badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; }
.badge-draft { background: rgba(107,114,128,.15); color: #9CA3AF; }
.badge-pending { background: rgba(249,115,22,.15); color: var(--warning); }
.badge-confirmed { background: rgba(59,130,246,.15); color: #60A5FA; }
.badge-active { background: rgba(16,185,129,.15); color: var(--success); }
.badge-completed { background: rgba(139,92,246,.15); color: var(--purple); }
.badge-cancelled { background: rgba(239,68,68,.15); color: var(--danger); }
.badge-active .pulse-dot { width:6px; height:6px; margin-right:3px; }

/* Actions */
.actions-cell { display: flex; align-items: center; gap: 6px; justify-content: flex-end; }
.btn-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border); background: transparent; color: var(--muted); cursor: pointer; transition: all .2s; text-decoration: none; font-size: 14px; }
.btn-icon:hover { border-color: var(--primary); color: var(--primary); background: rgba(0,188,212,.1); }
.btn-icon.edit:hover { border-color: var(--gold); color: var(--gold); background: rgba(245,158,11,.1); }
.btn-icon.pdf:hover { border-color: var(--danger); color: var(--danger); background: rgba(239,68,68,.1); }
.action-dropdown { position: relative; }
.action-dropdown-menu { position: absolute; right: 0; top: 38px; background: #1A2235; border: 1px solid var(--border); border-radius: var(--radius); padding: 6px; min-width: 160px; z-index: 100; display: none; box-shadow: 0 20px 60px rgba(0,0,0,.5); }
.action-dropdown-menu.show { display: block; }
.dropdown-item { display: flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; color: var(--muted); cursor: pointer; transition: all .15s; text-decoration: none; border: none; background: none; width: 100%; font-family: 'DM Sans',sans-serif; }
.dropdown-item:hover { background: rgba(255,255,255,.05); color: var(--text); }
.dropdown-item.confirm { color: var(--success); }
.dropdown-item.cancel { color: var(--danger); }

/* Empty state */
.empty-state { text-align: center; padding: 80px 24px; }
.empty-icon { font-size: 56px; margin-bottom: 16px; }
.empty-title { font-size: 18px; font-weight: 700; color: var(--text); margin-bottom: 8px; }
.empty-sub { font-size: 13px; color: var(--muted); margin-bottom: 24px; }
.btn-create-first { display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, var(--primary), #0097A7); color: #fff; font-weight: 700; font-size: 13px; padding: 12px 24px; border-radius: var(--radius); text-decoration: none; transition: all .2s; box-shadow: 0 4px 20px rgba(0,188,212,.3); }

/* Flash messages */
.flash-success { background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.3); color: var(--success); padding: 12px 18px; border-radius: var(--radius); margin-bottom: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
.flash-error { background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.3); color: var(--danger); padding: 12px 18px; border-radius: var(--radius); margin-bottom: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; }

/* Pagination override */
.pagination-wrap { padding: 16px 24px; border-top: 1px solid var(--border); }
.pagination-wrap nav { display: flex; justify-content: flex-end; }
</style>
@endpush

@section('content')
<div class="rnt-wrap">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="flash-success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="flash-error">❌ {{ session('error') }}</div>
    @endif

    {{-- Header --}}
    <div class="rnt-header">
        <div class="rnt-header-left">
            <h1>🚌 Rental Management</h1>
            <p>Manage all vehicle rentals for your fleet</p>
        </div>
        <a href="{{ route('company.rentals.create') }}" class="btn-new">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            New Rental
        </a>
    </div>

    {{-- Stats Grid --}}
    <div class="stats-grid">
        <div class="stat-card stat-teal">
            <div class="stat-icon teal">🚌</div>
            <div class="stat-label">Total Rentals</div>
            <div class="stat-value">{{ $rentals->total() }}</div>
            <div class="stat-sub">All time</div>
        </div>
        <div class="stat-card stat-green">
            <div class="stat-icon green">
                <span class="pulse-dot"></span>
            </div>
            <div class="stat-label">Active Now</div>
            <div class="stat-value">{{ $stats['active'] ?? 0 }}</div>
            <div class="stat-sub">Running rentals</div>
        </div>
        <div class="stat-card stat-orange">
            <div class="stat-icon orange">⏳</div>
            <div class="stat-label">Pending</div>
            <div class="stat-value">{{ $stats['upcoming'] ?? 0 }}</div>
            <div class="stat-sub">Awaiting confirm</div>
        </div>
        <div class="stat-card stat-purple">
            <div class="stat-icon purple">✅</div>
            <div class="stat-label">Completed</div>
            <div class="stat-value">{{ $stats['completed'] ?? 0 }}</div>
            <div class="stat-sub">This month</div>
        </div>
        <div class="stat-card stat-gold">
            <div class="stat-icon gold">💰</div>
            <div class="stat-label">Revenue MTD</div>
            <div class="stat-value" style="font-size:18px;">AED {{ number_format($stats['revenue_mtd'] ?? 0, 0) }}</div>
            <div class="stat-sub">Month to date</div>
        </div>
        <div class="stat-card stat-red">
            <div class="stat-icon red">🔴</div>
            <div class="stat-label">Outstanding</div>
            <div class="stat-value" style="font-size:18px; color: var(--danger);">AED {{ number_format($stats['outstanding'] ?? 0, 0) }}</div>
            <div class="stat-sub">Unpaid amount</div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <form action="{{ route('company.rentals.index') }}" method="GET">
            <div class="filter-row">
                <div class="filter-search">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search rental # or customer..." class="filter-input">
                </div>
                <select name="status" class="filter-input" style="flex:0;min-width:150px;">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                    <option value="confirmed" {{ request('status')=='confirmed'?'selected':'' }}>Confirmed</option>
                    <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
                    <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
                    <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
                </select>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="filter-input" style="flex:0;min-width:145px;" placeholder="Date From">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="filter-input" style="flex:0;min-width:145px;" placeholder="Date To">
                <button type="submit" class="btn-filter">🔍 Filter</button>
                <a href="{{ route('company.rentals.index') }}" class="btn-clear">✕ Clear</a>
                <a href="{{ route('company.rentals.index') }}?export=pdf{{ request()->has('status') ? '&status='.request('status') : '' }}" class="btn-export">📄 PDF</a>
                <a href="{{ route('company.rentals.index') }}?export=excel{{ request()->has('status') ? '&status='.request('status') : '' }}" class="btn-export">📊 Excel</a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-wrap">
        <div class="table-header-bar">
            <span class="table-title">All Rentals</span>
            <span class="table-count">{{ $rentals->total() }} records found</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="rnt-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Rental #</th>
                        <th>Customer</th>
                        <th>Vehicle</th>
                        <th>Driver</th>
                        <th>Period</th>
                        <th>Amount</th>
                        <th style="text-align:center;">Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rentals as $index => $rental)
                    <tr>
                        <td style="color:var(--muted); font-size:12px;">{{ $rentals->firstItem() + $index }}</td>
                        <td>
                            <a href="{{ route('company.rentals.show', $rental) }}" class="rnt-num">{{ $rental->rental_number }}</a>
                        </td>
                        <td>
                            <div class="customer-cell">
                                <div class="avatar" style="background:linear-gradient(135deg,#00BCD4,#0097A7);color:#fff;">
                                    {{ strtoupper(substr($rental->customer->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="customer-name">{{ $rental->customer->name ?? '—' }}</div>
                                    <div class="customer-sub">{{ $rental->customer->phone ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="vehicle-cell">
                                @if($rental->vehicle)
                                    <div class="v-name">🚌 {{ $rental->vehicle->vehicle_number }}</div>
                                    <div class="v-plate">{{ $rental->vehicle->make }} {{ $rental->vehicle->model }}</div>
                                @else
                                    <span class="unassigned-badge">⚠️ Unassigned</span>
                                @endif
                            </div>
                        </td>
                        <td style="font-size:12px; color:var(--text);">
                            {{ $rental->driver->name ?? 'Self Drive' }}
                        </td>
                        <td>
                            @php
                                $days = $rental->start_date->diffInDays($rental->end_date);
                            @endphp
                            <div class="period-dates">{{ $rental->start_date->format('d M') }} – {{ $rental->end_date->format('d M Y') }}</div>
                            <span class="period-duration">{{ $days }} days</span>
                        </td>
                        <td>
                            <div class="amount">AED {{ number_format($rental->final_amount, 0) }}</div>
                            <div class="amount-sub">{{ ucfirst($rental->payment_status ?? 'unpaid') }}</div>
                        </td>
                        <td style="text-align:center;">
                            @php
                                $badgeMap = [
                                    'draft'     => 'badge-draft',
                                    'pending'   => 'badge-pending',
                                    'confirmed' => 'badge-confirmed',
                                    'active'    => 'badge-active',
                                    'completed' => 'badge-completed',
                                    'cancelled' => 'badge-cancelled',
                                ];
                                $bc = $badgeMap[$rental->status] ?? 'badge-draft';
                            @endphp
                            <span class="badge {{ $bc }}">
                                @if($rental->status === 'active')<span class="pulse-dot"></span>@endif
                                {{ strtoupper($rental->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="actions-cell">
                                <a href="{{ route('company.rentals.show', $rental) }}" class="btn-icon" title="View">👁️</a>
                                @if(!in_array($rental->status, ['completed','cancelled']))
                                <a href="{{ route('company.rentals.edit', $rental) }}" class="btn-icon edit" title="Edit">✏️</a>
                                @endif
                                @if(in_array($rental->status, ['confirmed','active','completed']))
                                <a href="{{ route('company.rentals.pdf', $rental) }}" class="btn-icon pdf" title="PDF">📄</a>
                                @endif
                                <div class="action-dropdown" onclick="toggleDropdown(this)">
                                    <button class="btn-icon" title="More">⋮</button>
                                    <div class="action-dropdown-menu">
                                        @if($rental->status === 'draft' && $rental->vehicle_id)
                                        <form action="{{ route('company.rentals.confirm', $rental) }}" method="POST">
                                            @csrf
                                            <button class="dropdown-item confirm" type="submit">✅ Confirm</button>
                                        </form>
                                        @endif
                                        @if($rental->status === 'confirmed')
                                        <form action="{{ route('company.rentals.transition', $rental) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="to_status" value="active">
                                            <button class="dropdown-item confirm" type="submit">🚀 Activate</button>
                                        </form>
                                        @endif
                                        @if(in_array($rental->status, ['draft','confirmed','active']))
                                        <form action="{{ route('company.rentals.transition', $rental) }}" method="POST" onsubmit="return confirm('Cancel this rental?')">
                                            @csrf
                                            <input type="hidden" name="to_status" value="cancelled">
                                            <button class="dropdown-item cancel" type="submit">❌ Cancel</button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <div class="empty-icon">🚌</div>
                                <div class="empty-title">No rentals found</div>
                                <div class="empty-sub">Create your first rental to get started managing your fleet</div>
                                <a href="{{ route('company.rentals.create') }}" class="btn-create-first">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    Create First Rental
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($rentals->hasPages())
        <div class="pagination-wrap">
            {{ $rentals->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleDropdown(el) {
    event.stopPropagation();
    const menu = el.querySelector('.action-dropdown-menu');
    document.querySelectorAll('.action-dropdown-menu.show').forEach(m => { if(m !== menu) m.classList.remove('show'); });
    menu.classList.toggle('show');
}
document.addEventListener('click', () => {
    document.querySelectorAll('.action-dropdown-menu.show').forEach(m => m.classList.remove('show'));
});
</script>
@endpush