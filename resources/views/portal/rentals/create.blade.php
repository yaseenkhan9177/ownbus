@extends('layouts.company')
@section('title', 'New Rental — OwnBus')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
body,.create-wrap{font-family:'DM Sans',sans-serif;background:#0A0F1E;}
.create-wrap{min-height:100vh;padding:24px;}
.card{background:#111827;border:1px solid #1F2937;border-radius:12px;padding:24px;}
.form-label{font-size:11px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:.8px;display:block;margin-bottom:6px;}
.form-input{width:100%;background:#0D1526;border:1px solid #1F2937;border-radius:8px;color:#F9FAFB;font-size:13px;padding:10px 14px;font-family:'DM Sans',sans-serif;outline:none;transition:border-color .2s;}
.form-input:focus{border-color:#00BCD4;}
.form-input option{background:#111827;}
.btn-primary{background:linear-gradient(135deg,#00BCD4,#0097A7);color:#fff;border:none;border-radius:10px;padding:12px 24px;font-weight:700;font-size:13px;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:8px;}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(0,188,212,.35);}
.btn-ghost{background:#1F2937;color:#9CA3AF;border:none;border-radius:10px;padding:12px 20px;font-weight:700;font-size:13px;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .2s;text-decoration:none;display:inline-flex;align-items:center;gap:8px;}
.btn-ghost:hover{color:#F9FAFB;}
.step-indicator{display:flex;align-items:center;gap:0;margin-bottom:32px;}
.step{display:flex;align-items:center;gap:10px;flex:1;}
.step-circle{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0;transition:all .3s;}
.step-circle.active{background:#00BCD4;color:#fff;box-shadow:0 0 0 4px rgba(0,188,212,.2);}
.step-circle.done{background:#10B981;color:#fff;}
.step-circle.inactive{background:#1F2937;color:#6B7280;}
.step-label{font-size:11px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:.5px;}
.step-label.active{color:#00BCD4;}
.step-line{flex:1;height:2px;background:#1F2937;margin:0 8px;}
.step-line.done{background:#10B981;}
.rental-type-card{border:2px solid #1F2937;border-radius:10px;padding:16px;cursor:pointer;transition:all .2s;text-align:center;background:#0D1526;}
.rental-type-card:hover,.rental-type-card.selected{border-color:#00BCD4;background:rgba(0,188,212,.08);}
.rental-type-card.selected .rt-icon{color:#00BCD4;}
.rt-icon{font-size:24px;margin-bottom:6px;}
.rt-name{font-size:12px;font-weight:700;color:#F9FAFB;}
.rt-sub{font-size:10px;color:#6B7280;margin-top:2px;}
.vehicle-card{border:2px solid #1F2937;border-radius:10px;padding:16px;cursor:pointer;transition:all .2s;background:#0D1526;position:relative;}
.vehicle-card:hover,.vehicle-card.selected{border-color:#00BCD4;background:rgba(0,188,212,.08);}
.vehicle-card.unavailable{opacity:.45;cursor:not-allowed;}
.v-badge{position:absolute;top:10px;right:10px;font-size:9px;font-weight:700;padding:2px 7px;border-radius:4px;text-transform:uppercase;}
.v-badge.avail{background:rgba(16,185,129,.15);color:#10B981;}
.v-badge.busy{background:rgba(239,68,68,.15);color:#EF4444;}
.extra-card{border:2px solid #1F2937;border-radius:10px;padding:14px;cursor:pointer;transition:all .2s;background:#0D1526;display:flex;align-items:center;gap:12px;}
.extra-card.selected{border-color:#00BCD4;background:rgba(0,188,212,.08);}
.price-sidebar{background:#111827;border:1px solid #1F2937;border-radius:12px;padding:24px;position:sticky;top:24px;}
.price-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #1F2937;font-size:13px;}
.price-row:last-child{border-bottom:none;}
.price-label{color:#6B7280;font-weight:500;}
.price-val{color:#F9FAFB;font-weight:700;}
.total-row{background:rgba(0,188,212,.08);border-radius:8px;padding:14px;margin-top:12px;}
.total-label{font-size:12px;color:#6B7280;text-transform:uppercase;font-weight:700;}
.total-amount{font-size:22px;font-weight:800;color:#00BCD4;}
.flash-error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#EF4444;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;}
.section-title{font-size:16px;font-weight:700;color:#F9FAFB;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
</style>
@endpush

@section('content')
<div class="create-wrap" x-data="{
    step:1,
    rentalType:'daily',
    customerId:'',
    vehicleId:'',
    driverId:'',
    baseRate:0, deposit:500, discountPct:0,
    pickupDate:'', returnDate:'',
    get duration(){
        if(!this.pickupDate||!this.returnDate) return 0;
        const d=Math.ceil((new Date(this.returnDate)-new Date(this.pickupDate))/(86400000));
        return d>0?d:0;
    },
    get subtotal(){return this.baseRate*this.duration;},
    get discountAmt(){return this.subtotal*(this.discountPct/100);},
    get vat(){return (this.subtotal-this.discountAmt)*0.05;},
    get grandTotal(){return this.subtotal-this.discountAmt+this.vat;},
    extras:{gps:false,childSeat:false,insurance:false,overtime:false},
    get extrasTotal(){let t=0;if(this.extras.gps)t+=25*this.duration;if(this.extras.childSeat)t+=25*this.duration;if(this.extras.insurance)t+=50*this.duration;if(this.extras.overtime)t+=50*this.duration;return t;}
}">

    {{-- Breadcrumb --}}
    <div style="margin-bottom:20px; display:flex; align-items:center; gap:8px; font-size:12px; color:#6B7280;">
        <a href="{{ route('company.rentals.index') }}" style="color:#00BCD4; text-decoration:none;">🚌 Rentals</a>
        <span>›</span>
        <span style="color:#F9FAFB;">New Rental</span>
    </div>

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
        <h1 style="font-size:22px;font-weight:800;color:#F9FAFB;margin:0;">🚌 New Rental Booking</h1>
    </div>

    {{-- Step Indicator --}}
    <div class="step-indicator">
        <div class="step">
            <div class="step-circle" :class="step>=1?'active':'inactive'">1</div>
            <span class="step-label" :class="step>=1?'active':''">Client</span>
        </div>
        <div class="step-line" :class="step>1?'done':''"></div>
        <div class="step">
            <div class="step-circle" :class="step>2?'done':step==2?'active':'inactive'">2</div>
            <span class="step-label" :class="step>=2?'active':''">Vehicle</span>
        </div>
        <div class="step-line" :class="step>2?'done':''"></div>
        <div class="step">
            <div class="step-circle" :class="step>3?'done':step==3?'active':'inactive'">3</div>
            <span class="step-label" :class="step>=3?'active':''">Options</span>
        </div>
        <div class="step-line" :class="step>3?'done':''"></div>
        <div class="step">
            <div class="step-circle" :class="step==4?'active':'inactive'">4</div>
            <span class="step-label" :class="step>=4?'active':''">Confirm</span>
        </div>
    </div>

    @if($errors->any())
    <div class="flash-error">❌ Please fix: {{ $errors->first() }}</div>
    @endif

    <form action="{{ route('company.rentals.store') }}" method="POST" id="rentalForm">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start;">

            {{-- MAIN FORM --}}
            <div>

                {{-- STEP 1: Client & Schedule --}}
                <div x-show="step==1" class="card">
                    <div class="section-title">👤 Client & Schedule</div>

                    <div style="margin-bottom:20px;">
                        <label class="form-label">Customer *</label>
                        <select name="customer_id" class="form-input" x-model="customerId" required>
                            <option value="">🔍 Select customer...</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id')==$c->id?'selected':'' }}>
                                {{ $c->name }} — {{ $c->phone }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom:20px;">
                        <label class="form-label">Rental Type *</label>
                        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;">
                            @foreach(['daily'=>['📅','Daily','Per Day'],'weekly'=>['📆','Weekly','Per Week'],'monthly'=>['🗓️','Monthly','Per Month'],'distance'=>['📍','Distance','Per KM']] as $type=>$info)
                            <div class="rental-type-card" :class="rentalType=='{{ $type }}'?'selected':''" @click="rentalType='{{ $type }}'">
                                <div class="rt-icon">{{ $info[0] }}</div>
                                <div class="rt-name">{{ $info[1] }}</div>
                                <div class="rt-sub">{{ $info[2] }}</div>
                            </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="rental_type" :value="rentalType">
                        <input type="hidden" name="rate_type" :value="rentalType=='daily'?'per_day':rentalType=='weekly'?'per_week':rentalType=='monthly'?'per_month':'per_km'">
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                        <div>
                            <label class="form-label">📅 Pickup Date & Time *</label>
                            <input type="datetime-local" name="start_date" class="form-input" x-model="pickupDate" @change="baseRate=baseRate||0" value="{{ old('start_date') }}" required>
                        </div>
                        <div>
                            <label class="form-label">📅 Return Date & Time *</label>
                            <input type="datetime-local" name="end_date" class="form-input" x-model="returnDate" value="{{ old('end_date') }}" required>
                        </div>
                    </div>

                    <div x-show="duration>0" style="display:inline-flex;align-items:center;gap:8px;background:rgba(0,188,212,.1);border:1px solid rgba(0,188,212,.2);border-radius:8px;padding:8px 14px;margin-bottom:20px;">
                        <span style="color:#00BCD4;font-size:13px;">⏱️</span>
                        <span style="color:#00BCD4;font-size:13px;font-weight:700;" x-text="duration+' Days'"></span>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div>
                            <label class="form-label">📍 Pickup Location *</label>
                            <input type="text" name="pickup_location" class="form-input" placeholder="e.g. Dubai Airport T3" value="{{ old('pickup_location') }}" required>
                            <div style="margin-top:8px;display:flex;gap:6px;flex-wrap:wrap;">
                                @foreach(['DXB Airport','Dubai Mall','Marina','Business Bay','Abu Dhabi'] as $loc)
                                <span onclick="document.querySelector('[name=pickup_location]').value='{{ $loc }}'" style="font-size:10px;color:#00BCD4;background:rgba(0,188,212,.1);padding:3px 8px;border-radius:4px;cursor:pointer;">{{ $loc }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label class="form-label">🏁 Return Location</label>
                            <input type="text" name="dropoff_location" class="form-input" placeholder="Same as pickup (optional)" value="{{ old('dropoff_location') }}">
                        </div>
                    </div>

                    <div style="margin-top:24px;display:flex;justify-content:flex-end;">
                        <button type="button" class="btn-primary" @click="if(customerId&&pickupDate&&returnDate)step=2">Next: Vehicle →</button>
                    </div>
                </div>

                {{-- STEP 2: Vehicle & Driver --}}
                <div x-show="step==2" class="card">
                    <div class="section-title">🚌 Vehicle & Driver</div>
                    <p style="font-size:12px;color:#6B7280;margin-bottom:16px;">Vehicle selection is optional — you can assign later.</p>

                    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:24px;">
                        @foreach($vehicles as $v)
                        <div class="vehicle-card" :class="vehicleId=='{{ $v->id }}'?'selected':''" @click="vehicleId='{{ $v->id }}';baseRate={{ $v->daily_rate ?? 0 }}">
                            <span class="v-badge avail">Available</span>
                            <div style="font-size:24px;margin-bottom:8px;">🚌</div>
                            <div style="font-size:13px;font-weight:700;color:#F9FAFB;">{{ $v->vehicle_number }}</div>
                            <div style="font-size:11px;color:#6B7280;margin-top:2px;">{{ $v->make }} {{ $v->model }}</div>
                            <div style="font-size:11px;color:#6B7280;">🪑 {{ $v->seating_capacity ?? '—' }} Seats</div>
                            <div style="font-size:12px;color:#F59E0B;margin-top:6px;font-weight:700;">AED {{ number_format($v->daily_rate ?? 0, 0) }}/day</div>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="vehicle_id" :value="vehicleId||''">

                    <div style="margin-bottom:24px;">
                        <div class="section-title" style="font-size:14px;">👨‍✈️ Driver</div>
                        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                            <div class="vehicle-card" :class="driverId==''?'selected':''" @click="driverId=''">
                                <div style="font-size:24px;margin-bottom:8px;">🚗</div>
                                <div style="font-size:13px;font-weight:700;color:#F9FAFB;">Self Drive</div>
                                <div style="font-size:11px;color:#6B7280;margin-top:2px;">No driver assigned</div>
                            </div>
                            @foreach($drivers as $d)
                            <div class="vehicle-card" :class="driverId=='{{ $d->id }}'?'selected':''" @click="driverId='{{ $d->id }}'">
                                <div style="font-size:24px;margin-bottom:8px;">👨‍✈️</div>
                                <div style="font-size:13px;font-weight:700;color:#F9FAFB;">{{ $d->name }}</div>
                                <div style="font-size:11px;color:#10B981;">✅ Available</div>
                            </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="driver_id" :value="driverId||''">
                    </div>

                    <div style="display:flex;gap:10px;justify-content:space-between;">
                        <button type="button" class="btn-ghost" @click="step=1">← Back</button>
                        <button type="button" class="btn-primary" @click="step=3">Next: Options →</button>
                    </div>
                </div>

                {{-- STEP 3: Options --}}
                <div x-show="step==3" class="card">
                    <div class="section-title">⚙️ Additional Options</div>

                    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:24px;">
                        @foreach([['gps','📡','GPS Device','AED 25/day'],['childSeat','👶','Child Seat','AED 25/day'],['insurance','🛡️','Extra Insurance','AED 50/day'],['overtime','⏰','Driver Overtime','AED 50/hr']] as $ex)
                        <div class="extra-card" :class="extras.{{ $ex[0] }}?'selected':''" @click="extras.{{ $ex[0] }}=!extras.{{ $ex[0] }}">
                            <span style="font-size:24px;">{{ $ex[1] }}</span>
                            <div>
                                <div style="font-size:13px;font-weight:700;color:#F9FAFB;">{{ $ex[2] }}</div>
                                <div style="font-size:11px;color:#6B7280;">{{ $ex[3] }}</div>
                            </div>
                            <span x-show="extras.{{ $ex[0] }}" style="margin-left:auto;color:#10B981;font-size:18px;">✓</span>
                        </div>
                        @endforeach
                    </div>

                    <div style="margin-bottom:20px;">
                        <label class="form-label">📝 Notes / Special Instructions</label>
                        <textarea name="notes" class="form-input" rows="3" placeholder="Any special requirements...">{{ old('notes') }}</textarea>
                    </div>

                    <div style="display:flex;gap:10px;justify-content:space-between;">
                        <button type="button" class="btn-ghost" @click="step=2">← Back</button>
                        <button type="button" class="btn-primary" @click="step=4">Review & Confirm →</button>
                    </div>
                </div>

                {{-- STEP 4: Confirm --}}
                <div x-show="step==4" class="card">
                    <div class="section-title">✅ Rental Summary</div>
                    <div style="background:#0D1526;border:1px solid #1F2937;border-radius:10px;padding:20px;margin-bottom:20px;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-size:13px;">
                            <div><span style="color:#6B7280;">Pickup:</span> <span style="color:#F9FAFB;" x-text="pickupDate||'—'"></span></div>
                            <div><span style="color:#6B7280;">Return:</span> <span style="color:#F9FAFB;" x-text="returnDate||'—'"></span></div>
                            <div><span style="color:#6B7280;">Duration:</span> <span style="color:#00BCD4;font-weight:700;" x-text="duration+' Days'"></span></div>
                            <div><span style="color:#6B7280;">Type:</span> <span style="color:#F9FAFB;" x-text="rentalType"></span></div>
                        </div>
                    </div>
                    <div style="margin-bottom:16px;">
                        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #1F2937;font-size:13px;">
                            <span style="color:#6B7280;">Base Rate × Duration</span>
                            <span style="color:#F9FAFB;font-weight:700;">AED <span x-text="subtotal.toFixed(2)"></span></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #1F2937;font-size:13px;">
                            <span style="color:#6B7280;">Discount</span>
                            <span style="color:#10B981;font-weight:700;">- AED <span x-text="discountAmt.toFixed(2)"></span></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #1F2937;font-size:13px;">
                            <span style="color:#6B7280;">VAT (5%)</span>
                            <span style="color:#F9FAFB;font-weight:700;">AED <span x-text="vat.toFixed(2)"></span></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding:12px;background:rgba(0,188,212,.08);border-radius:8px;margin-top:8px;">
                            <span style="color:#00BCD4;font-weight:700;font-size:14px;">Grand Total</span>
                            <span style="color:#00BCD4;font-weight:800;font-size:18px;">AED <span x-text="grandTotal.toFixed(2)"></span></span>
                        </div>
                    </div>
                    <div style="display:flex;gap:10px;justify-content:space-between;">
                        <button type="button" class="btn-ghost" @click="step=3">← Back</button>
                        <button type="submit" class="btn-primary">✅ Create Rental</button>
                    </div>
                </div>

            </div>

            {{-- SIDEBAR --}}
            <div class="price-sidebar">
                <div style="font-size:14px;font-weight:800;color:#F9FAFB;margin-bottom:20px;">💰 Pricing Summary</div>

                <div style="margin-bottom:16px;">
                    <label class="form-label">Base Rate (AED)</label>
                    <input type="number" name="rate_amount" class="form-input" x-model="baseRate" placeholder="500" step="0.01" min="0" required>
                </div>
                <div style="margin-bottom:16px;">
                    <label class="form-label">Security Deposit (AED)</label>
                    <input type="number" name="security_deposit" class="form-input" x-model="deposit" placeholder="500" step="0.01" min="0">
                </div>
                <div style="margin-bottom:20px;">
                    <label class="form-label">Discount (%)</label>
                    <input type="number" name="discount_percent" class="form-input" x-model="discountPct" placeholder="0" min="0" max="100">
                </div>

                <div style="border-top:1px solid #1F2937;padding-top:16px;">
                    <div class="price-row"><span class="price-label">Subtotal</span><span class="price-val">AED <span x-text="subtotal.toFixed(2)"></span></span></div>
                    <div class="price-row"><span class="price-label">Discount</span><span style="color:#10B981;font-weight:700;">-AED <span x-text="discountAmt.toFixed(2)"></span></span></div>
                    <div class="price-row"><span class="price-label">Extras</span><span class="price-val">AED <span x-text="extrasTotal.toFixed(2)"></span></span></div>
                    <div class="price-row"><span class="price-label">VAT 5%</span><span class="price-val">AED <span x-text="vat.toFixed(2)"></span></span></div>
                </div>

                <div class="total-row">
                    <div class="total-label">Grand Total</div>
                    <div class="total-amount">AED <span x-text="grandTotal.toFixed(2)"></span></div>
                    <div style="font-size:11px;color:#6B7280;margin-top:4px;"><span x-text="duration"></span> days</div>
                </div>

                <div style="margin-top:16px;">
                    <button type="submit" class="btn-primary" style="width:100%;justify-content:center;">✅ Create Rental</button>
                </div>
                <a href="{{ route('company.rentals.index') }}" class="btn-ghost" style="margin-top:8px;width:100%;justify-content:center;text-align:center;">← Back to Rentals</a>
            </div>

        </div>
    </form>
</div>
@endsection