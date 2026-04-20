<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container { padding: 40px; }
        .header { margin-bottom: 50px; }
        .invoice-title {
            font-size: 24pt;
            font-weight: bold;
            text-transform: uppercase;
            text-align: right;
            color: #1e293b;
            margin: 0;
        }
        .vat-badge {
            font-size: 9pt;
            background: #f1f5f9;
            color: #4f46e5;
            padding: 4px 8px;
            font-weight: bold;
            display: inline-block;
            text-align: right;
            float: right;
            margin-top: 5px;
        }
        .company-info { margin-bottom: 30px; }
        .company-name { font-size: 16pt; font-weight: bold; margin-bottom: 5px; }
        .details-grid {
            width: 100%;
            margin-bottom: 40px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 20px;
        }
        .details-grid td { vertical-align: top; }
        .label { font-size: 9pt; color: #94a3b8; text-transform: uppercase; font-weight: bold; margin-bottom: 5px; display: block; }
        .value { font-weight: bold; }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        table.items th {
            background: #f8fafc;
            padding: 12px;
            text-align: left;
            font-size: 9pt;
            text-transform: uppercase;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }
        table.items td {
            padding: 15px 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        .totals-container {
            width: 100%;
        }
        .totals-table {
            float: right;
            width: 250px;
        }
        .totals-table td {
            padding: 8px 0;
        }
        .total-row {
            font-size: 14pt;
            font-weight: bold;
            color: #4f46e5;
            border-top: 2px solid #f1f5f9;
        }
        .qr-section { margin-top: 50px; }
        .footer {
            margin-top: 50px;
            font-size: 9pt;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }
        .page-break { page-break-after: always; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table style="width:100%">
                <tr>
                    <td style="width: 50%">
                        <div class="company-name">{{ Auth::user()->company->name ?? 'OWNBUS' }}</div>
                        <div style="font-size: 9pt; color: #64748b;">
                            {{ Auth::user()->company->address ?? 'Dubai, UAE' }}<br>
                            TRN: {{ Auth::user()->company->trn_number ?? '100XXXXXXXXX003' }}
                        </div>
                    </td>
                    <td style="width: 50%; text-align: right;">
                        <h1 class="invoice-title">Tax Invoice</h1>
                        <div class="vat-badge">VAT COMPLIANT (5%)</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="details-grid">
            <tr>
                <td style="width: 35%">
                    <span class="label">Bill To</span>
                    <div class="value">{{ $invoice->customer->name }}</div>
                    <div style="font-size: 9pt; color: #64748b; margin-top: 5px;">
                        {{ $invoice->customer->address ?? 'N/A' }}<br>
                        Phone: {{ $invoice->customer->phone }}
                    </div>
                </td>
                <td style="width: 35%">
                    <span class="label">Invoice Details</span>
                    <table style="width: 100%; font-size: 9pt;">
                        <tr>
                            <td style="color: #94a3b8">Invoice #:</td>
                            <td class="value">{{ $invoice->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td style="color: #94a3b8">Date:</td>
                            <td class="value">{{ $invoice->created_at->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td style="color: #94a3b8">Due Date:</td>
                            <td class="value">{{ $invoice->due_date->format('d M Y') }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 30%; text-align: right;">
                    @if($invoice->qr_code_data)
                    <!-- We'd normally use a library here, but for the mock we'll use a placeholder -->
                    <div style="font-size: 8pt; color: #94a3b8; font-family: monospace; margin-bottom: 5px;">SCAN TO VERIFY</div>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; width: 80px; height: 80px; margin-left: auto;">
                        <!-- In real world: <img src="data:image/png;base64,{{-- gen qr --}}"> -->
                        <div style="padding-top: 30px; text-align: center; color: #cbd5e1; font-weight: bold; font-size: 6pt;">UAE FTA QR</div>
                    </div>
                    @endif
                </td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th style="width: 50%">Description</th>
                    <th style="width: 10%; text-align: center;">Qty</th>
                    <th style="width: 15%; text-align: right;">Rate</th>
                    <th style="width: 10%; text-align: right;">VAT</th>
                    <th style="width: 15%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->total - ($item->unit_price * $item->quantity), 2) }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-container">
            <table class="totals-table">
                <tr>
                    <td style="color: #94a3b8; font-size: 9pt;">Subtotal Excl. VAT</td>
                    <td class="text-right" style="font-weight: bold;">AED {{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td style="color: #94a3b8; font-size: 9pt;">VAT (5%) Total</td>
                    <td class="text-right" style="font-weight: bold;">AED {{ number_format($invoice->vat_amount, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total Amount Due</td>
                    <td class="text-right">AED {{ number_format($invoice->total, 2) }}</td>
                </tr>
            </table>
            <div style="clear: both;"></div>
        </div>

        @if($invoice->notes)
        <div style="margin-top: 40px; padding: 20px; background: #f8fafc; border-radius: 10px;">
            <span class="label">Important Notes</span>
            <p style="font-size: 9pt; color: #64748b; margin: 5px 0 0 0;">{{ $invoice->notes }}</p>
        </div>
        @endif

        <div class="footer">
            <p>Thank you for choosing {{ Auth::user()->company->name ?? 'OwnBus' }} for your transportation needs.</p>
            <p style="font-size: 7pt;">This is a computer generated document and does not require a signature.</p>
        </div>
    </div>
</body>
</html>
