<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $rental->rental_number ?? 'RNT-'.$rental->id }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; font-size: 14px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); }
        .invoice-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #3b82f6; padding-bottom: 20px; margin-bottom: 20px; }
        .logo { font-size: 28px; font-weight: 800; color: #3b82f6; text-transform: uppercase; letter-spacing: 1px; }
        .title { text-align: right; }
        .title h2 { margin: 0; color: #3b82f6; }
        .details { display: table; width: 100%; margin-bottom: 30px; }
        .details-col { display: table-cell; width: 50%; vertical-align: top; }
        .label { font-size: 10px; font-weight: bold; color: #666; text-transform: uppercase; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 12px; text-align: left; font-size: 12px; font-weight: bold; text-transform: uppercase; color: #64748b; }
        td { padding: 12px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        .total-row td { border-bottom: none; }
        .total-box { float: right; width: 250px; background: #f8fafc; padding: 20px; border-radius: 8px; }
        .total-line { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; }
        .grand-total { border-top: 2px solid #e2e8f0; padding-top: 10px; margin-top: 10px; font-size: 18px; font-weight: 800; color: #3b82f6; }
        .status-stamp { display: inline-block; border: 4px solid #10b981; color: #10b981; padding: 5px 15px; font-size: 24px; font-weight: 900; text-transform: uppercase; border-radius: 8px; opacity: 0.6; transform: rotate(-15deg); position: absolute; top: 150px; right: 50px; }
    </style>
</head>
<body>
    <div class="invoice-box">
        @if($rental->payment_status === 'paid')
            <div class="status-stamp">PAID</div>
        @endif

        <div class="invoice-header">
            <div class="logo">FLEET SERVICE</div>
            <div class="title">
                <h2>INVOICE</h2>
                <p>#{{ $rental->rental_number ?? 'RNT-'.$rental->id }}</p>
            </div>
        </div>

        <div class="details">
            <div class="details-col">
                <div class="label">Billed To:</div>
                <strong>{{ auth()->user()->name }}</strong><br>
                Customer Portal Account<br>
                Dubai, United Arab Emirates
            </div>
            <div class="details-col text-right" style="text-align: right;">
                <div class="label">Date:</div>
                {{ now()->format('d M Y') }}<br>
                <div class="label" style="margin-top: 10px;">Reference:</div>
                {{ $rental->rental_type }} rental
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: center;">Days</th>
                    <th style="text-align: right;">Rate</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $rental->vehicle?->name ?? 'Vehicle Service' }}</strong><br>
                        <span style="font-size: 12px; color: #666;">Plate: {{ $rental->vehicle?->plate_number ?? 'N/A' }}</span><br>
                        <span style="font-size: 11px; color: #999;">Period: {{ optional($rental->start_date)->format('d M y') }} - {{ optional($rental->end_date)->format('d M y') }}</span>
                    </td>
                    <td style="text-align: center;">{{ $rental->start_date && $rental->end_date ? $rental->start_date->diffInDays($rental->end_date) : 1 }}</td>
                    <td style="text-align: right;">AED {{ number_format($rental->rate ?? 0, 2) }}</td>
                    <td style="text-align: right;">AED {{ number_format($rental->sub-total ?? $rental->grand_total ?? 0, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total-box">
            <div class="total-line">
                <span>Subtotal:</span>
                <span>AED {{ number_format($rental->sub_total ?? $rental->grand_total, 2) }}</span>
            </div>
            @if($rental->vat_amount)
            <div class="total-line">
                <span>VAT (5%):</span>
                <span>AED {{ number_format($rental->vat_amount, 2) }}</span>
            </div>
            @endif
            <div class="total-line grand-total">
                <span>Total:</span>
                <span>AED {{ number_format($rental->final_amount ?? $rental->grand_total, 2) }}</span>
            </div>
        </div>

        <div style="clear: both; margin-top: 50px;">
            <div class="label">Terms & Conditions:</div>
            <p style="font-size: 10px; color: #999;">This is a computer-generated document. Payment is due within 7 days. If you have any questions, please contact our support team via the portal.</p>
        </div>
    </div>
</body>
</html>
