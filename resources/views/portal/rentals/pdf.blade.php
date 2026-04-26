<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Rental Agreement — {{ $rental->rental_number }}</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #1A202C; background: #fff; line-height: 1.5; }
.page { padding: 30px 36px; max-width: 794px; margin: 0 auto; }

/* HEADER */
.header { display: table; width: 100%; border-bottom: 3px solid #00BCD4; padding-bottom: 16px; margin-bottom: 16px; }
.header-left { display: table-cell; vertical-align: top; width: 55%; }
.header-right { display: table-cell; vertical-align: top; text-align: right; }
.company-name { font-size: 20px; font-weight: 900; color: #00BCD4; letter-spacing: -0.5px; }
.company-sub { font-size: 9px; color: #718096; margin-top: 2px; }
.doc-title { font-size: 16px; font-weight: 900; text-transform: uppercase; color: #1A202C; letter-spacing: 1px; }
.doc-sub { font-size: 10px; color: #718096; margin-top: 4px; }
.trn { font-size: 10px; color: #718096; margin-top: 6px; }

/* COLORED BANNER */
.banner { background: linear-gradient(135deg, #00BCD4, #0097A7); color: #fff; text-align: center; padding: 12px; margin-bottom: 16px; border-radius: 6px; }
.banner-text { font-size: 14px; font-weight: 900; letter-spacing: 2px; text-transform: uppercase; }

/* STATUS BADGE */
.status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
.status-confirmed { background: #EBF8FF; color: #2B6CB0; border: 1px solid #BEE3F8; }
.status-active { background: #F0FFF4; color: #276749; border: 1px solid #C6F6D5; }
.status-completed { background: #FAF5FF; color: #553C9A; border: 1px solid #E9D8FD; }
.status-draft { background: #F7FAFC; color: #718096; border: 1px solid #E2E8F0; }

/* PARTIES TABLE */
.parties { display: table; width: 100%; margin-bottom: 16px; border: 1px solid #E2E8F0; border-radius: 6px; overflow: hidden; }
.party { display: table-cell; width: 50%; padding: 14px; vertical-align: top; }
.party-left { border-right: 1px solid #E2E8F0; }
.party-title { font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 1.5px; color: #00BCD4; background: #F0FDFF; padding: 6px 10px; margin: -14px -14px 12px -14px; border-bottom: 1px solid #E2E8F0; }
.party-name { font-size: 13px; font-weight: 700; color: #1A202C; margin-bottom: 6px; }
.party-row { font-size: 10px; color: #4A5568; margin-bottom: 3px; }
.party-row strong { color: #2D3748; font-weight: 700; }

/* DETAIL TABLE */
.detail-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
.detail-table th { background: #F7FAFC; padding: 8px 12px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #718096; text-align: left; border: 1px solid #E2E8F0; width: 35%; }
.detail-table td { padding: 8px 12px; font-size: 11px; color: #2D3748; border: 1px solid #E2E8F0; font-weight: 500; }
.section-header { background: #00BCD4; color: #fff; font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 1.5px; padding: 7px 12px; border-radius: 4px; margin-bottom: 8px; margin-top: 14px; }

/* FINANCIAL TABLE */
.finance-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
.finance-table td { padding: 8px 14px; font-size: 11px; border-bottom: 1px solid #F0F4F8; }
.finance-table .f-label { color: #718096; font-weight: 500; width: 65%; }
.finance-table .f-val { text-align: right; font-weight: 700; color: #2D3748; }
.finance-table .f-discount { color: #38A169; }
.finance-total { background: #00BCD4; }
.finance-total td { color: #fff !important; font-size: 13px; font-weight: 900; border-bottom: none; padding: 12px 14px; }

/* TERMS */
.terms { border: 1px solid #E2E8F0; border-radius: 6px; padding: 14px; margin-bottom: 16px; background: #FFFDF0; }
.terms-title { font-size: 10px; font-weight: 900; text-transform: uppercase; color: #744210; margin-bottom: 8px; letter-spacing: 1px; }
.terms ol { padding-left: 16px; }
.terms li { font-size: 9.5px; color: #4A5568; margin-bottom: 4px; line-height: 1.5; }

/* SIGNATURES */
.sig-table { display: table; width: 100%; margin-bottom: 16px; }
.sig-col { display: table-cell; width: 48%; vertical-align: top; padding: 14px; border: 1px solid #E2E8F0; border-radius: 6px; }
.sig-gap { display: table-cell; width: 4%; }
.sig-title { font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: #718096; margin-bottom: 40px; }
.sig-line { border-bottom: 1.5px solid #2D3748; margin-bottom: 4px; }
.sig-label { font-size: 9px; color: #718096; }

/* FOOTER */
.footer { border-top: 2px solid #E2E8F0; padding-top: 12px; display: table; width: 100%; }
.footer-left { display: table-cell; vertical-align: middle; font-size: 9px; color: #A0AEC0; }
.footer-right { display: table-cell; vertical-align: middle; text-align: right; font-size: 9px; color: #A0AEC0; }
.footer-brand { font-weight: 900; color: #00BCD4; }

@media print {
    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .page { padding: 20px; }
}
</style>
</head>
<body>
<div class="page">

    {{-- HEADER --}}
    <div class="header">
        <div class="header-left">
            <div class="company-name">{{ $rental->company->name ?? config('app.name', 'OwnBus') }}</div>
            <div class="company-sub">{{ $rental->company->address ?? 'Dubai, United Arab Emirates' }}</div>
            @if(isset($rental->company->trn) && $rental->company->trn)
            <div class="trn">TRN: {{ $rental->company->trn }}</div>
            @endif
            <div class="trn">
                {{ $rental->company->phone ?? '' }}
                @if(isset($rental->company->phone) && isset($rental->company->email)) | @endif
                {{ $rental->company->email ?? '' }}
            </div>
        </div>
        <div class="header-right">
            <div class="doc-title">Vehicle Rental Agreement</div>
            <div class="doc-sub">#{{ $rental->rental_number }}</div>
            <div class="doc-sub">Date: {{ now()->format('d M Y') }}</div>
            <div style="margin-top:8px;">
                @php
                    $statusClass = 'status-'.$rental->status;
                @endphp
                <span class="status-badge {{ $statusClass }}">{{ strtoupper($rental->status) }}</span>
            </div>
        </div>
    </div>

    {{-- BANNER --}}
    <div class="banner">
        <div class="banner-text">🚌 Vehicle Rental Agreement — UAE</div>
    </div>

    {{-- PARTIES --}}
    <div class="parties">
        <div class="party party-left">
            <div class="party-title">Lessor (Company)</div>
            <div class="party-name">{{ $rental->company->name ?? 'OwnBus Fleet' }}</div>
            @if(isset($rental->company->trn) && $rental->company->trn)
            <div class="party-row"><strong>TRN:</strong> {{ $rental->company->trn }}</div>
            @endif
            <div class="party-row"><strong>Address:</strong> {{ $rental->company->address ?? 'Dubai, UAE' }}</div>
            <div class="party-row"><strong>Phone:</strong> {{ $rental->company->phone ?? 'N/A' }}</div>
            <div class="party-row"><strong>Email:</strong> {{ $rental->company->email ?? 'N/A' }}</div>
        </div>
        <div class="party">
            <div class="party-title">Lessee (Customer)</div>
            <div class="party-name">{{ $rental->customer->name ?? 'N/A' }}</div>
            @if($rental->customer->emirates_id)
            <div class="party-row"><strong>Emirates ID:</strong> {{ $rental->customer->emirates_id }}</div>
            @endif
            <div class="party-row"><strong>Phone:</strong> {{ $rental->customer->phone ?? 'N/A' }}</div>
            <div class="party-row"><strong>Email:</strong> {{ $rental->customer->email ?? 'N/A' }}</div>
            @if($rental->customer->company_name)
            <div class="party-row"><strong>Company:</strong> {{ $rental->customer->company_name }}</div>
            @endif
        </div>
    </div>

    {{-- VEHICLE & SCHEDULE DETAILS --}}
    <div class="section-header">Vehicle & Schedule Details</div>
    <table class="detail-table">
        <tr><th>Vehicle</th><td>{{ $rental->vehicle->vehicle_number ?? 'UNASSIGNED' }}</td></tr>
        <tr><th>Make / Model</th><td>{{ ($rental->vehicle->make ?? '').' '.($rental->vehicle->model ?? '') ?: 'N/A' }}</td></tr>
        <tr><th>Seat Capacity</th><td>{{ $rental->vehicle->seating_capacity ?? 'N/A' }} Seats</td></tr>
        <tr><th>Driver</th><td>{{ $rental->driver->name ?? 'Self Drive' }}</td></tr>
        @if($rental->driver && $rental->driver->license_number)
        <tr><th>Driver License</th><td>{{ $rental->driver->license_number }}</td></tr>
        @endif
        <tr><th>Pickup Date & Time</th><td>{{ $rental->start_date->format('d M Y, h:i A') }}</td></tr>
        <tr><th>Return Date & Time</th><td>{{ $rental->end_date->format('d M Y, h:i A') }}</td></tr>
        <tr><th>Duration</th><td>{{ $rental->start_date->diffInDays($rental->end_date) }} Days</td></tr>
        <tr><th>Pickup Location</th><td>{{ $rental->pickup_location ?: 'N/A' }}</td></tr>
        <tr><th>Return Location</th><td>{{ $rental->dropoff_location ?: 'Same as Pickup' }}</td></tr>
        <tr><th>Rental Type</th><td>{{ ucfirst($rental->rental_type) }}</td></tr>
    </table>

    {{-- FINANCIAL --}}
    <div class="section-header">Financial Breakdown</div>
    <table class="finance-table">
        <tr><td class="f-label">Base Rate ({{ ucfirst(str_replace('_',' ',$rental->rate_type ?? 'per_day')) }})</td><td class="f-val">AED {{ number_format($rental->rate_amount, 2) }}</td></tr>
        <tr><td class="f-label">Duration</td><td class="f-val">{{ $rental->start_date->diffInDays($rental->end_date) }} Days</td></tr>
        @if($rental->discount > 0)
        <tr><td class="f-label">Discount</td><td class="f-val f-discount">- AED {{ number_format($rental->discount, 2) }}</td></tr>
        @endif
        <tr><td class="f-label">VAT (5%)</td><td class="f-val">AED {{ number_format($rental->tax, 2) }}</td></tr>
        @if($rental->security_deposit > 0)
        <tr><td class="f-label">Security Deposit (Refundable)</td><td class="f-val">AED {{ number_format($rental->security_deposit, 2) }}</td></tr>
        @endif
        <tr class="finance-total">
            <td class="f-label" style="color:#fff;font-weight:900;">GRAND TOTAL</td>
            <td class="f-val" style="color:#fff;font-size:14px;">AED {{ number_format($rental->final_amount, 2) }}</td>
        </tr>
    </table>

    {{-- TERMS --}}
    <div class="terms">
        <div class="terms-title">⚖️ Terms & Conditions</div>
        <ol>
            <li>The vehicle must be returned in the same condition as received. Any damage will be charged to the lessee.</li>
            <li>The lessee is solely responsible for all traffic fines, Salik charges, and parking violations during the rental period.</li>
            <li>The security deposit will be refunded within 15–30 business days after vehicle inspection and fine clearance.</li>
            <li>Smoking is strictly prohibited inside the vehicle. A cleaning fee of AED 200 applies if violated.</li>
            <li>The vehicle must not be driven outside the UAE without prior written consent from the lessor.</li>
            <li>In case of breakdown or accident, please contact: <strong>{{ $rental->company->phone ?? '+971 XX XXX XXXX' }}</strong> immediately.</li>
            <li>This agreement is governed by the laws of the United Arab Emirates.</li>
        </ol>
    </div>

    {{-- SIGNATURES --}}
    <div class="sig-table">
        <div class="sig-col">
            <div class="sig-title">Lessor Signature</div>
            <div class="sig-line"></div>
            <div class="sig-label">Name: ________________________</div>
            <div class="sig-label" style="margin-top:4px;">Date: ________________________</div>
            <div class="sig-label" style="margin-top:4px;">Stamp: _______________________</div>
        </div>
        <div class="sig-gap"></div>
        <div class="sig-col">
            <div class="sig-title">Lessee Signature</div>
            <div class="sig-line"></div>
            <div class="sig-label">Name: ________________________</div>
            <div class="sig-label" style="margin-top:4px;">Date: ________________________</div>
            <div class="sig-label" style="margin-top:4px;">Emirates ID: _________________</div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <div class="footer-left">
            Generated by <span class="footer-brand">OwnBus</span> &mdash; ownbus.software<br>
            {{ $rental->company->phone ?? '' }} | {{ $rental->company->email ?? '' }}
        </div>
        <div class="footer-right">
            Document: {{ $rental->rental_number }}<br>
            Generated: {{ now()->format('d M Y H:i') }}<br>
            Page 1 of 1
        </div>
    </div>

</div>
</body>
</html>
