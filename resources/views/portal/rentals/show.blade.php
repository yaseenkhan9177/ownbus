@extends('layouts.company')
@section('title', 'Rental #'.$rental->rental_number.' — OwnBus')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
body,.show-wrap{font-family:'DM Sans',sans-serif;background:#0A0F1E;}
.show-wrap{min-height:100vh;padding:24px;}
.card{background:#111827;border:1px solid #1F2937;border-radius:12px;padding:24px;margin-bottom:20px;}
.card-title{font-size:13px;font-weight:700;color:#F9FAFB;margin-bottom:16px;display:flex;align-items:center;gap:8px;border-bottom:1px solid #1F2937;padding-bottom:12px;}
.info-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid rgba(31,41,55,.5);font-size:13px;}
.info-row:last-child{border-bottom:none;}
.info-label{color:#6B7280;font-weight:500;}
.info-val{color:#F9FAFB;font-weight:600;text-align:right;}
.btn{display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:9px;font-size:12px;font-weight:700;cursor:pointer;border:none;font-family:'DM Sans',sans-serif;text-decoration:none;transition:all .2s;}
.btn-primary{background:linear-gradient(135deg,#00BCD4,#0097A7);color:#fff;}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 4px 16px rgba(0,188,212,.3);}
.btn-success{background:rgba(16,185,129,.15);color:#10B981;border:1px solid rgba(16,185,129,.3);}
.btn-success:hover{background:rgba(16,185,129,.25);}
.btn-warning{background:rgba(245,158,11,.15);color:#F59E0B;border:1px solid rgba(245,158,11,.3);}
.btn-warning:hover{background:rgba(245,158,11,.25);}
.btn-danger{background:rgba(239,68,68,.15);color:#EF4444;border:1px solid rgba(239,68,68,.3);}
.btn-danger:hover{background:rgba(239,68,68,.25);}
.btn-ghost{background:#1F2937;color:#9CA3AF;border:1px solid #374151;}
.btn-ghost:hover{color:#F9FAFB;}
.btn-pdf{background:rgba(139,92,246,.15);color:#8B5CF6;border:1px solid rgba(139,92,246,.3);}
.btn-pdf:hover{background:rgba(139,92,246,.25);}
.badge{display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;}
.badge-draft{background:rgba(107,114,128,.15);color:#9CA3AF;}
.badge-confirmed{background:rgba(59,130,246,.15);color:#60A5FA;}
.badge-active{background:rgba(16,185,129,.15);color:#10B981;}
.badge-completed{background:rgba(139,92,246,.15);color:#8B5CF6;}
.badge-cancelled{background:rgba(239,68,68,.15);color:#EF4444;}
.pulse{animation:pulse 2s infinite;}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.5}}
.alert-warn{background:rgba(249,115,22,.08);border:1px solid rgba(249,115,22,.25);border-radius:10px;padding:14px 18px;display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:20px;}
.avatar{width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,#00BCD4,#0097A7);color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;flex-shrink:0;}
.timeline-item{position:relative;padding-left:28px;padding-bottom:16px;}
.timeline-item::before{content:'';position:absolute;left:7px;top:20px;bottom:0;width:2px;background:#1F2937;}
.timeline-item:last-child::before{display:none;}
.timeline-dot{position:absolute;left:0;top:4px;width:16px;height:16px;border-radius:50%;background:#00BCD4;border:2px solid #0A0F1E;}
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.7);backdrop-filter:blur(4px);z-index:50;display:flex;align-items:center;justify-content:center;padding:24px;}
.modal{background:#111827;border:1px solid #1F2937;border-radius:16px;padding:28px;width:100%;max-width:440px;box-shadow:0 25px 80px rgba(0,0,0,.6);}
.form-label{font-size:11px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:.8px;display:block;margin-bottom:6px;}
.form-input{width:100%;background:#0D1526;border:1px solid #1F2937;border-radius:8px;color:#F9FAFB;font-size:13px;padding:10px 14px;font-family:'DM Sans',sans-serif;outline:none;transition:border-color .2s;}
.form-input:focus{border-color:#00BCD4;}
.flash-success{background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#10B981;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;font-weight:600;}
.flash-error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#EF4444;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;font-weight:600;}
</style>
@endpush

@section('content')
<div class="show-wrap" x-data="{
    showAssignModal:false,
    showCancelModal:false,
    showPaymentModal:false,
    cancelReason:''
}">

    @if(session('success'))<div class="flash-success">✅ {{ session('success') }}</div>@endif
    @if(session('error'))<div class="flash-error">❌ {{ session('error') }}</div>@endif

    {{-- Breadcrumb --}}
    <div style="margin-bottom:16px;display:flex;align-items:center;gap:8px;font-size:12px;color:#6B7280;">
        <a href="{{ route('company.rentals.index') }}" style="color:#00BCD4;text-decoration:none;">🚌 Rentals</a>
        <span>›</span>
        <span style="color:#F9FAFB;">{{ $rental->rental_number }}</span>
    </div>

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
        <div style="display:flex;align-items:center;gap:14px;">
            <h1 style="font-size:22px;font-weight:800;color:#F9FAFB;margin:0;">🚌 #{{ $rental->rental_number }}</h1>
            @php
                $badgeMap=['draft'=>'badge-draft','confirmed'=>'badge-confirmed','active'=>'badge-active','completed'=>'badge-completed','cancelled'=>'badge-cancelled'];
                $bc=$badgeMap[$rental->status]??'badge-draft';
            @endphp
            <span class="badge {{ $bc }}">
                @if($rental->status==='active')<span class="pulse" style="display:inline-block;width:7px;height:7px;background:#10B981;border-radius:50%;margin-right:4px;"></span>@endif
                {{ strtoupper($rental->status) }}
            </span>
        </div>
        {{-- Action Bar --}}
        <div style="display:flex;flex-wrap:wrap;gap:8px;">
            @if(!in_array($rental->status,['completed','cancelled']))
            <a href="{{ route('company.rentals.edit', $rental) }}" class="btn btn-ghost">✏️ Edit</a>
            @endif
            @if($rental->status==='draft' && $rental->vehicle_id)
            <form action="{{ route('company.rentals.confirm', $rental) }}" method="POST" style="display:inline;">@csrf
                <button type="submit" class="btn btn-success">✅ Confirm</button>
            </form>
            @endif
            @if(!$rental->vehicle_id)
            <button class="btn btn-warning" @click="showAssignModal=true">🚌 Assign Vehicle</button>
            @endif
            @if($rental->status==='confirmed')
            <form action="{{ route('company.rentals.transition', $rental) }}" method="POST" style="display:inline;">@csrf
                <input type="hidden" name="to_status" value="active">
                <button type="submit" class="btn btn-success">🚀 Activate</button>
            </form>
            @endif
            @if($rental->status==='active')
            <form action="{{ route('company.rentals.complete', $rental) }}" method="POST" style="display:inline;">@csrf
                <button type="submit" class="btn" style="background:rgba(139,92,246,.15);color:#8B5CF6;border:1px solid rgba(139,92,246,.3);">🏁 Complete</button>
            </form>
            @endif
            @if(in_array($rental->status,['confirmed','active','completed']))
            <a href="{{ route('company.rentals.pdf', $rental) }}" class="btn btn-pdf">📄 PDF</a>
            @endif
            @if($rental->payment_status!=='paid')
            <button class="btn btn-primary" @click="showPaymentModal=true">💰 Payment</button>
            @endif
            @if(in_array($rental->status,['draft','confirmed','active']))
            <button class="btn btn-danger" @click="showCancelModal=true">❌ Cancel</button>
            @endif
        </div>
    </div>

    {{-- No vehicle alert --}}
    @if(!$rental->vehicle_id)
    <div class="alert-warn">
        <div style="display:flex;align-items:center;gap:10px;">
            <span style="font-size:18px;">⚠️</span>
            <div>
                <div style="font-size:13px;font-weight:700;color:#F9FAFB;">No vehicle assigned to this rental</div>
                <div style="font-size:11px;color:#F97316;">Assign a vehicle to proceed with confirmation.</div>
            </div>
        </div>
        <button class="btn btn-warning" @click="showAssignModal=true">🚌 Assign Now</button>
    </div>
    @endif

    {{-- 3-Column Grid --}}
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;align-items:start;">

        {{-- Col 1: Rental Details + Schedule --}}
        <div>
            <div class="card">
                <div class="card-title">📋 Rental Details</div>
                <div class="info-row"><span class="info-label">Rental #</span><span class="info-val" style="color:#00BCD4;font-family:monospace;">{{ $rental->rental_number }}</span></div>
                <div class="info-row"><span class="info-label">Type</span><span class="info-val">{{ ucfirst($rental->rental_type) }}</span></div>
                <div class="info-row"><span class="info-label">Rate Scale</span><span class="info-val">{{ ucfirst(str_replace('_',' ',$rental->rate_type)) }}</span></div>
                <div class="info-row"><span class="info-label">Created</span><span class="info-val">{{ $rental->created_at->format('d M Y H:i') }}</span></div>
                <div class="info-row"><span class="info-label">Created By</span><span class="info-val">{{ $rental->creator->name ?? 'System' }}</span></div>
            </div>
            <div class="card">
                <div class="card-title">📅 Schedule</div>
                <div class="info-row"><span class="info-label">Pickup</span><span class="info-val">{{ $rental->start_date->format('d M Y H:i') }}</span></div>
                <div class="info-row"><span class="info-label">Return</span><span class="info-val">{{ $rental->end_date->format('d M Y H:i') }}</span></div>
                <div class="info-row"><span class="info-label">Duration</span><span class="info-val" style="color:#8B5CF6;">{{ $rental->start_date->diffInDays($rental->end_date) }} Days</span></div>
                <div class="info-row"><span class="info-label">📍 Pickup</span><span class="info-val">{{ $rental->pickup_location ?: '—' }}</span></div>
                <div class="info-row"><span class="info-label">🏁 Return</span><span class="info-val">{{ $rental->dropoff_location ?: 'Same as pickup' }}</span></div>
            </div>
            @if($rental->notes)
            <div class="card">
                <div class="card-title">📝 Notes</div>
                <p style="font-size:13px;color:#9CA3AF;line-height:1.6;">{{ $rental->notes }}</p>
            </div>
            @endif
        </div>

        {{-- Col 2: Customer, Vehicle, Driver --}}
        <div>
            <div class="card">
                <div class="card-title">👤 Customer</div>
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                    <div class="avatar">{{ strtoupper(substr($rental->customer->name??'U',0,1)) }}</div>
                    <div>
                        <div style="font-size:14px;font-weight:700;color:#F9FAFB;">{{ $rental->customer->name ?? '—' }}</div>
                        <div style="font-size:12px;color:#6B7280;">{{ $rental->customer->company_name ?? '' }}</div>
                    </div>
                </div>
                <div class="info-row"><span class="info-label">📱 Phone</span><span class="info-val">{{ $rental->customer->phone ?? '—' }}</span></div>
                <div class="info-row"><span class="info-label">📧 Email</span><span class="info-val">{{ $rental->customer->email ?? '—' }}</span></div>
                @if($rental->customer->emirates_id)
                <div class="info-row"><span class="info-label">🪪 Emirates ID</span><span class="info-val">{{ $rental->customer->emirates_id }}</span></div>
                @endif
            </div>
            <div class="card">
                <div class="card-title">🚌 Vehicle</div>
                @if($rental->vehicle)
                <div style="text-align:center;font-size:40px;margin-bottom:12px;">🚌</div>
                <div class="info-row"><span class="info-label">Name</span><span class="info-val">{{ $rental->vehicle->vehicle_number }}</span></div>
                <div class="info-row"><span class="info-label">Make/Model</span><span class="info-val">{{ $rental->vehicle->make }} {{ $rental->vehicle->model }}</span></div>
                <div class="info-row"><span class="info-label">🪑 Seats</span><span class="info-val">{{ $rental->vehicle->seating_capacity ?? '—' }}</span></div>
                <div style="margin-top:12px;"><a href="{{ route('company.fleet.show', $rental->vehicle) }}" class="btn btn-ghost" style="width:100%;justify-content:center;">View Vehicle</a></div>
                @else
                <div style="text-align:center;padding:20px;color:#6B7280;font-size:13px;">No vehicle assigned</div>
                <button class="btn btn-warning" style="width:100%;justify-content:center;" @click="showAssignModal=true">🚌 Assign Vehicle</button>
                @endif
            </div>
            <div class="card">
                <div class="card-title">👨‍✈️ Driver</div>
                @if($rental->driver)
                <div style="text-align:center;font-size:40px;margin-bottom:12px;">👨‍✈️</div>
                <div class="info-row"><span class="info-label">Name</span><span class="info-val">{{ $rental->driver->name }}</span></div>
                <div class="info-row"><span class="info-label">📱 Phone</span><span class="info-val">{{ $rental->driver->phone ?? '—' }}</span></div>
                @else
                <div style="text-align:center;padding:20px;color:#6B7280;font-size:13px;">🚗 Self Drive — No driver assigned</div>
                @endif
            </div>
        </div>

        {{-- Col 3: Financials + Payment History + Timeline --}}
        <div>
            <div class="card" style="background:linear-gradient(135deg,#0D1526,#111827);">
                <div class="card-title" style="color:#F59E0B;">💰 Financial Summary</div>
                <div class="info-row"><span class="info-label">Base Rate</span><span class="info-val">AED {{ number_format($rental->rate_amount, 2) }}/{{ str_replace('per_','',$rental->rate_type??'day') }}</span></div>
                <div class="info-row"><span class="info-label">Duration</span><span class="info-val">{{ $rental->start_date->diffInDays($rental->end_date) }} days</span></div>
                <div class="info-row"><span class="info-label">Discount</span><span style="color:#10B981;font-weight:700;font-size:13px;">- AED {{ number_format($rental->discount, 2) }}</span></div>
                <div class="info-row"><span class="info-label">VAT (5%)</span><span class="info-val">AED {{ number_format($rental->tax, 2) }}</span></div>
                <div style="background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.2);border-radius:10px;padding:14px;margin:12px 0;">
                    <div style="font-size:11px;color:#6B7280;text-transform:uppercase;font-weight:700;margin-bottom:4px;">Grand Total</div>
                    <div style="font-size:24px;font-weight:800;color:#F59E0B;">AED {{ number_format($rental->final_amount, 2) }}</div>
                </div>
                <div class="info-row"><span class="info-label">Deposit</span><span class="info-val">AED {{ number_format($rental->security_deposit, 2) }}</span></div>
                @php
                    $paid = $rental->payments->sum('amount') ?? 0;
                    $outstanding = max(0, $rental->final_amount - $paid);
                @endphp
                <div class="info-row"><span class="info-label">Paid</span><span style="color:#10B981;font-weight:700;font-size:13px;">AED {{ number_format($paid, 2) }}</span></div>
                <div class="info-row"><span class="info-label">Outstanding</span><span style="color:{{ $outstanding>0?'#EF4444':'#10B981' }};font-weight:700;font-size:13px;">AED {{ number_format($outstanding, 2) }}</span></div>
                @if($outstanding>0)
                <button class="btn btn-primary" style="width:100%;justify-content:center;margin-top:12px;" @click="showPaymentModal=true">💰 Record Payment</button>
                @endif
            </div>

            {{-- Payment History --}}
            @if($rental->payments && $rental->payments->count())
            <div class="card">
                <div class="card-title">💳 Payment History</div>
                <table style="width:100%;border-collapse:collapse;font-size:12px;">
                    <thead><tr style="border-bottom:1px solid #1F2937;">
                        <th style="text-align:left;padding:6px 0;color:#6B7280;font-weight:700;text-transform:uppercase;font-size:10px;">Date</th>
                        <th style="text-align:right;padding:6px 0;color:#6B7280;font-weight:700;text-transform:uppercase;font-size:10px;">Amount</th>
                        <th style="text-align:right;padding:6px 0;color:#6B7280;font-weight:700;text-transform:uppercase;font-size:10px;">Method</th>
                    </tr></thead>
                    <tbody>
                    @foreach($rental->payments as $pmt)
                    <tr style="border-bottom:1px solid rgba(31,41,55,.5);">
                        <td style="padding:8px 0;color:#9CA3AF;">{{ \Carbon\Carbon::parse($pmt->paid_at)->format('d M Y') }}</td>
                        <td style="padding:8px 0;text-align:right;color:#10B981;font-weight:700;">AED {{ number_format($pmt->amount, 2) }}</td>
                        <td style="padding:8px 0;text-align:right;color:#9CA3AF;">{{ ucfirst($pmt->payment_method ?? 'cash') }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Activity Timeline --}}
            <div class="card">
                <div class="card-title">📊 Activity Timeline</div>
                <div>
                    @forelse($rental->statusLogs as $log)
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div style="font-size:12px;font-weight:700;color:#F9FAFB;text-transform:uppercase;">{{ $log->to_status }}</div>
                        <div style="font-size:11px;color:#6B7280;margin-top:2px;">{{ $log->created_at->format('d M Y H:i') }} • {{ $log->user->name ?? 'System' }}</div>
                        @if($log->reason)<div style="font-size:11px;color:#9CA3AF;margin-top:2px;font-style:italic;">"{{ $log->reason }}"</div>@endif
                    </div>
                    @empty
                    <p style="color:#6B7280;font-size:12px;text-align:center;">No activity yet</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ASSIGN VEHICLE MODAL --}}
    <div class="modal-overlay" x-show="showAssignModal" x-cloak @click.self="showAssignModal=false">
        <div class="modal">
            <h3 style="font-size:16px;font-weight:800;color:#F9FAFB;margin-bottom:16px;">🚌 Assign Vehicle</h3>
            <form action="{{ route('company.rentals.assign-vehicle', $rental) }}" method="POST">
                @csrf
                <label class="form-label">Available Vehicles</label>
                <select name="vehicle_id" required class="form-input" style="margin-bottom:20px;">
                    <option value="">Select a vehicle...</option>
                    @foreach(\App\Models\Vehicle::where('status','available')->get() as $v)
                    <option value="{{ $v->id }}">{{ $v->vehicle_number }} — {{ $v->make }} {{ $v->model }} ({{ $v->seating_capacity }} seats)</option>
                    @endforeach
                </select>
                <div style="display:flex;gap:10px;">
                    <button type="button" class="btn btn-ghost" style="flex:1;justify-content:center;" @click="showAssignModal=false">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">🚌 Assign</button>
                </div>
            </form>
        </div>
    </div>

    {{-- CANCEL MODAL --}}
    <div class="modal-overlay" x-show="showCancelModal" x-cloak @click.self="showCancelModal=false">
        <div class="modal">
            <h3 style="font-size:16px;font-weight:800;color:#EF4444;margin-bottom:8px;">❌ Cancel Rental</h3>
            <p style="font-size:13px;color:#6B7280;margin-bottom:16px;">⚠️ This action cannot be undone.</p>
            <form action="{{ route('company.rentals.transition', $rental) }}" method="POST">
                @csrf
                <input type="hidden" name="to_status" value="cancelled">
                <label class="form-label">Reason for cancellation *</label>
                <textarea name="reason" class="form-input" rows="3" required placeholder="Provide a reason..." style="margin-bottom:16px;"></textarea>
                <div style="display:flex;gap:10px;">
                    <button type="button" class="btn btn-ghost" style="flex:1;justify-content:center;" @click="showCancelModal=false">Keep Rental</button>
                    <button type="submit" class="btn btn-danger" style="flex:1;justify-content:center;">❌ Cancel Rental</button>
                </div>
            </form>
        </div>
    </div>

    {{-- PAYMENT MODAL --}}
    <div class="modal-overlay" x-show="showPaymentModal" x-cloak @click.self="showPaymentModal=false">
        <div class="modal">
            <h3 style="font-size:16px;font-weight:800;color:#F9FAFB;margin-bottom:8px;">💰 Record Payment</h3>
            <p style="font-size:13px;color:#EF4444;margin-bottom:16px;">Outstanding: AED {{ number_format($outstanding ?? 0, 2) }}</p>
            <form action="{{ route('company.finance.dashboard') }}" method="GET">
                <div style="display:grid;gap:14px;margin-bottom:20px;">
                    <div><label class="form-label">Amount (AED)</label><input type="number" name="amount" class="form-input" placeholder="{{ number_format($outstanding ?? 0, 2) }}" step="0.01" min="0"></div>
                    <div><label class="form-label">Method</label>
                        <select name="method" class="form-input">
                            <option>Cash</option><option>Bank Transfer</option><option>Card</option><option>Cheque</option>
                        </select>
                    </div>
                    <div><label class="form-label">Reference #</label><input type="text" name="ref" class="form-input" placeholder="Optional"></div>
                </div>
                <div style="display:flex;gap:10px;">
                    <button type="button" class="btn btn-ghost" style="flex:1;justify-content:center;" @click="showPaymentModal=false">Cancel</button>
                    <a href="{{ route('company.finance.dashboard') }}" class="btn btn-primary" style="flex:1;justify-content:center;">Go to Finance</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection