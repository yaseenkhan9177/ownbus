@extends('layouts.company')
@section('title', 'Edit Rental #'.$rental->rental_number.' — OwnBus')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
body,.edit-wrap{font-family:'DM Sans',sans-serif;background:#0A0F1E;}
.edit-wrap{min-height:100vh;padding:24px;}
.card{background:#111827;border:1px solid #1F2937;border-radius:12px;padding:24px;}
.form-label{font-size:11px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:.8px;display:block;margin-bottom:6px;}
.form-input{width:100%;background:#0D1526;border:1px solid #1F2937;border-radius:8px;color:#F9FAFB;font-size:13px;padding:10px 14px;font-family:'DM Sans',sans-serif;outline:none;transition:border-color .2s;}
.form-input:focus{border-color:#00BCD4;}
.form-input option{background:#111827;}
.btn-primary{background:linear-gradient(135deg,#00BCD4,#0097A7);color:#fff;border:none;border-radius:10px;padding:12px 24px;font-weight:700;font-size:13px;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:8px;}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(0,188,212,.35);}
.btn-ghost{background:#1F2937;color:#9CA3AF;border:none;border-radius:10px;padding:12px 20px;font-weight:700;font-size:13px;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .2s;text-decoration:none;display:inline-flex;align-items:center;gap:8px;}
.btn-ghost:hover{color:#F9FAFB;}
.warn-banner{background:rgba(249,115,22,.08);border:1px solid rgba(249,115,22,.25);border-radius:10px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:12px;}
.vehicle-card{border:2px solid #1F2937;border-radius:10px;padding:14px;cursor:pointer;transition:all .2s;background:#0D1526;}
.vehicle-card:hover,.vehicle-card.selected{border-color:#00BCD4;background:rgba(0,188,212,.08);}
.rental-type-card{border:2px solid #1F2937;border-radius:10px;padding:14px;cursor:pointer;transition:all .2s;text-align:center;background:#0D1526;}
.rental-type-card:hover,.rental-type-card.selected{border-color:#00BCD4;background:rgba(0,188,212,.08);}
.price-sidebar{background:#111827;border:1px solid #1F2937;border-radius:12px;padding:24px;position:sticky;top:24px;}
.price-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #1F2937;font-size:13px;}
.price-row:last-child{border-bottom:none;}
.total-row{background:rgba(0,188,212,.08);border-radius:8px;padding:14px;margin-top:12px;}
.section-title{font-size:15px;font-weight:700;color:#F9FAFB;margin-bottom:16px;margin-top:24px;}
.flash-error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#EF4444;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;font-weight:600;}
</style>
@endpush

@section('content')

@php
$isLocked = in_array($rental->status, ['completed','cancelled']);
$days = $rental->start_date->diffInDays($rental->end_date);
@endphp

@if($isLocked)
<div style="text-align:center;padding:80px 24px;font-family:'DM Sans',sans-serif;">
    <div style="font-size:48px;margin-bottom:16px;">🔒</div>
    <h2 style="font-size:20px;font-weight:800;color:#F9FAFB;margin-bottom:8px;">Cannot Edit This Rental</h2>
    <p style="color:#6B7280;font-size:13px;margin-bottom:24px;">Rental {{ $rental->rental_number }} is {{ $rental->status }} and cannot be modified.</p>
    <a href="{{ route('company.rentals.show', $rental) }}" style="background:#00BCD4;color:#fff;padding:12px 24px;border-radius:10px;text-decoration:none;font-weight:700;font-size:13px;">← View Rental</a>
</div>
@else

<div class="edit-wrap" x-data="{
    rentalType:'{{ old('rental_type', $rental->rental_type) }}',
    vehicleId:'{{ old('vehicle_id', $rental->vehicle_id) }}',
    driverId:'{{ old('driver_id', $rental->driver_id) }}',
    baseRate:{{ old('rate_amount', $rental->rate_amount) }},
    deposit:{{ old('security_deposit', $rental->security_deposit) }},
    discountPct:0,
    pickupDate:'{{ old('start_date', $rental->start_date->format('Y-m-d\TH:i')) }}',
    returnDate:'{{ old('end_date', $rental->end_date->format('Y-m-d\TH:i')) }}',
    get duration(){
        if(!this.pickupDate||!this.returnDate) return 0;
        const d=Math.ceil((new Date(this.returnDate)-new Date(this.pickupDate))/(86400000));
        return d>0?d:0;
    },
    get subtotal(){return this.baseRate*this.duration;},
    get discountAmt(){return this.subtotal*(this.discountPct/100);},
    get vat(){return (this.subtotal-this.discountAmt)*0.05;},
    get grandTotal(){return this.subtotal-this.discountAmt+this.vat;}
}">

    {{-- Breadcrumb --}}
    <div style="margin-bottom:16px;display:flex;align-items:center;gap:8px;font-size:12px;color:#6B7280;font-family:'DM Sans',sans-serif;">
        <a href="{{ route('company.rentals.index') }}" style="color:#00BCD4;text-decoration:none;">🚌 Rentals</a>
        <span>›</span>
        <a href="{{ route('company.rentals.show', $rental) }}" style="color:#00BCD4;text-decoration:none;">{{ $rental->rental_number }}</a>
        <span>›</span>
        <span style="color:#F9FAFB;">Edit</span>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <h1 style="font-size:22px;font-weight:800;color:#F9FAFB;margin:0;font-family:'DM Sans',sans-serif;">✏️ Edit #{{ $rental->rental_number }}</h1>
    </div>

    @if(session('error'))<div class="flash-error">❌ {{ session('error') }}</div>@endif
    @if($errors->any())<div class="flash-error">❌ {{ $errors->first() }}</div>@endif

    {{-- Warning for confirmed rentals --}}
    @if($rental->status === 'confirmed')
    <div class="warn-banner">
        <span style="font-size:20px;">⚠️</span>
        <div>
            <div style="font-size:13px;font-weight:700;color:#F97316;">Editing a Confirmed Rental</div>
            <div style="font-size:11px;color:#6B7280;">Saving changes will reset this rental back to draft status.</div>
        </div>
    </div>
    @endif

    <form action="{{ route('company.rentals.update', $rental) }}" method="POST">
        @csrf @method('PUT')
        <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start;">

            {{-- MAIN FORM --}}
            <div class="card">

                {{-- Customer --}}
                <div style="margin-bottom:20px;">
                    <label class="form-label">Customer *</label>
                    <select name="customer_id" class="form-input" required>
                        <option value="">Select customer...</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id',$rental->customer_id)==$c->id?'selected':'' }}>{{ $c->name }} — {{ $c->phone }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Rental Type --}}
                <div style="margin-bottom:20px;">
                    <label class="form-label">Rental Type *</label>
                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;">
                        @foreach(['daily'=>['📅','Daily'],'weekly'=>['📆','Weekly'],'monthly'=>['🗓️','Monthly'],'distance'=>['📍','Distance']] as $type=>$info)
                        <div class="rental-type-card" :class="rentalType=='{{ $type }}'?'selected':''" @click="rentalType='{{ $type }}'">
                            <div style="font-size:20px;margin-bottom:4px;">{{ $info[0] }}</div>
                            <div style="font-size:12px;font-weight:700;color:#F9FAFB;">{{ $info[1] }}</div>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="rental_type" :value="rentalType">
                    <input type="hidden" name="rate_type" :value="rentalType=='daily'?'per_day':rentalType=='weekly'?'per_week':rentalType=='monthly'?'per_month':'per_km'">
                </div>

                {{-- Dates --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                    <div>
                        <label class="form-label">📅 Pickup *</label>
                        <input type="datetime-local" name="start_date" class="form-input" x-model="pickupDate" required>
                    </div>
                    <div>
                        <label class="form-label">📅 Return *</label>
                        <input type="datetime-local" name="end_date" class="form-input" x-model="returnDate" required>
                    </div>
                </div>
                <div x-show="duration>0" style="display:inline-flex;align-items:center;gap:8px;background:rgba(0,188,212,.1);border:1px solid rgba(0,188,212,.2);border-radius:8px;padding:8px 14px;margin-bottom:20px;">
                    <span style="color:#00BCD4;font-size:13px;font-weight:700;">⏱️ <span x-text="duration"></span> Days</span>
                </div>

                {{-- Locations --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                    <div>
                        <label class="form-label">📍 Pickup Location *</label>
                        <input type="text" name="pickup_location" class="form-input" value="{{ old('pickup_location',$rental->pickup_location) }}" required>
                    </div>
                    <div>
                        <label class="form-label">🏁 Return Location</label>
                        <input type="text" name="dropoff_location" class="form-input" value="{{ old('dropoff_location',$rental->dropoff_location) }}" placeholder="Same as pickup">
                    </div>
                </div>

                {{-- Vehicle --}}
                <div class="section-title">🚌 Vehicle (Optional)</div>
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-bottom:20px;">
                    <div class="vehicle-card" :class="vehicleId==''?'selected':''" @click="vehicleId=''">
                        <div style="font-size:20px;margin-bottom:6px;">🚫</div>
                        <div style="font-size:12px;font-weight:700;color:#F9FAFB;">No Vehicle</div>
                        <div style="font-size:11px;color:#6B7280;">Assign later</div>
                    </div>
                    @foreach($vehicles as $v)
                    <div class="vehicle-card" :class="vehicleId=='{{ $v->id }}'?'selected':''" @click="vehicleId='{{ $v->id }}';baseRate={{ $v->daily_rate ?? 0 }}">
                        <div style="font-size:20px;margin-bottom:6px;">🚌</div>
                        <div style="font-size:12px;font-weight:700;color:#F9FAFB;">{{ $v->vehicle_number }}</div>
                        <div style="font-size:11px;color:#6B7280;">{{ $v->make }} {{ $v->model }}</div>
                        <div style="font-size:11px;color:#F59E0B;margin-top:4px;font-weight:700;">AED {{ number_format($v->daily_rate??0,0) }}/day</div>
                    </div>
                    @endforeach
                </div>
                <input type="hidden" name="vehicle_id" :value="vehicleId||''">

                {{-- Driver --}}
                <div class="section-title">👨‍✈️ Driver (Optional)</div>
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-bottom:20px;">
                    <div class="vehicle-card" :class="driverId==''?'selected':''" @click="driverId=''">
                        <div style="font-size:20px;margin-bottom:6px;">🚗</div>
                        <div style="font-size:12px;font-weight:700;color:#F9FAFB;">Self Drive</div>
                    </div>
                    @foreach($drivers as $d)
                    <div class="vehicle-card" :class="driverId=='{{ $d->id }}'?'selected':''" @click="driverId='{{ $d->id }}'">
                        <div style="font-size:20px;margin-bottom:6px;">👨‍✈️</div>
                        <div style="font-size:12px;font-weight:700;color:#F9FAFB;">{{ $d->name }}</div>
                        <div style="font-size:11px;color:#10B981;">✅ Available</div>
                    </div>
                    @endforeach
                </div>
                <input type="hidden" name="driver_id" :value="driverId||''">

                {{-- Notes --}}
                <div style="margin-bottom:24px;">
                    <label class="form-label">📝 Notes</label>
                    <textarea name="notes" class="form-input" rows="3">{{ old('notes',$rental->notes) }}</textarea>
                </div>

                <div style="display:flex;gap:10px;">
                    <a href="{{ route('company.rentals.show', $rental) }}" class="btn-ghost">← Cancel</a>
                    <button type="submit" class="btn-primary">💾 Update Rental</button>
                </div>
            </div>

            {{-- SIDEBAR --}}
            <div class="price-sidebar">
                <div style="font-size:14px;font-weight:800;color:#F9FAFB;margin-bottom:20px;">💰 Pricing</div>
                <div style="margin-bottom:14px;">
                    <label class="form-label">Base Rate (AED)</label>
                    <input type="number" name="rate_amount" class="form-input" x-model="baseRate" step="0.01" min="0" required>
                </div>
                <div style="margin-bottom:14px;">
                    <label class="form-label">Deposit (AED)</label>
                    <input type="number" name="security_deposit" class="form-input" x-model="deposit" step="0.01" min="0">
                </div>
                <div style="margin-bottom:20px;">
                    <label class="form-label">Discount (%)</label>
                    <input type="number" name="discount_percent" class="form-input" x-model="discountPct" min="0" max="100">
                </div>
                <div style="border-top:1px solid #1F2937;padding-top:14px;">
                    <div class="price-row"><span style="color:#6B7280;">Subtotal</span><span style="color:#F9FAFB;font-weight:700;">AED <span x-text="subtotal.toFixed(2)"></span></span></div>
                    <div class="price-row"><span style="color:#6B7280;">Discount</span><span style="color:#10B981;font-weight:700;">-AED <span x-text="discountAmt.toFixed(2)"></span></span></div>
                    <div class="price-row"><span style="color:#6B7280;">VAT 5%</span><span style="color:#F9FAFB;font-weight:700;">AED <span x-text="vat.toFixed(2)"></span></span></div>
                </div>
                <div class="total-row">
                    <div style="font-size:11px;color:#6B7280;text-transform:uppercase;font-weight:700;">Grand Total</div>
                    <div style="font-size:22px;font-weight:800;color:#00BCD4;">AED <span x-text="grandTotal.toFixed(2)"></span></div>
                    <div style="font-size:11px;color:#6B7280;margin-top:2px;"><span x-text="duration"></span> days</div>
                </div>
                <button type="submit" class="btn-primary" style="width:100%;justify-content:center;margin-top:16px;">💾 Update Rental</button>
            </div>

        </div>
    </form>
</div>
@endif
@endsection