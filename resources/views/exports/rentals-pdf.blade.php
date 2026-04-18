<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #1e293b;
            background: #fff;
        }

        .header {
            background: #0f172a;
            color: white;
            padding: 14px 20px;
            margin-bottom: 12px;
        }

        .header h1 {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .header p {
            font-size: 8px;
            color: #94a3b8;
            margin-top: 2px;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            padding: 0 20px 10px;
            font-size: 8px;
            color: #64748b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            padding: 0 20px;
            font-size: 8px;
        }

        th {
            background: #f1f5f9;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 6px 8px;
            text-align: left;
            border-bottom: 1.5px solid #e2e8f0;
        }

        td {
            padding: 5px 8px;
            border-bottom: 1px solid #f1f5f9;
        }

        tr:nth-child(even) td {
            background: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-active {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-overdue {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-completed {
            background: #e0e7ff;
            color: #3730a3;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .footer {
            margin-top: 10px;
            padding: 6px 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 7px;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
        }

        .amount {
            text-align: right;
            font-weight: 700;
            font-family: monospace;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $company->name }} – Rentals Report</h1>
        <p>Generated: {{ now()->format('d F Y, H:i') }} | UAE Standard Time</p>
    </div>
    <div class="meta">
        <span>Total Records: {{ $rentals->count() }}</span>
        <span>Total Revenue: AED {{ number_format($rentals->sum('final_amount'), 2) }}</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Vehicle</th>
                <th>Customer</th>
                <th>Start</th>
                <th>End</th>
                <th>Days</th>
                <th style="text-align:right;">Amount (AED)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rentals as $rental)
            @php
            $days = $rental->start_date && $rental->end_date ? $rental->start_date->diffInDays($rental->end_date) : '-';
            $badgeClass = 'badge-' . ($rental->status ?? 'pending');
            @endphp
            <tr>
                <td style="font-family:monospace;">#{{ substr($rental->uuid ?? '', 0, 8) }}</td>
                <td>{{ $rental->bus->vehicle_number ?? 'N/A' }}</td>
                <td>{{ $rental->customer->name ?? 'N/A' }}</td>
                <td>{{ $rental->start_date?->format('d/m/Y') }}</td>
                <td>{{ $rental->end_date?->format('d/m/Y') }}</td>
                <td>{{ $days }}</td>
                <td class="amount">{{ number_format($rental->final_amount ?? 0, 2) }}</td>
                <td><span class="badge {{ $badgeClass }}">{{ $rental->status }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        <span>{{ $company->name }} | ERP System</span>
        <span>AED amounts include applicable taxes. UAE VAT 5% may apply.</span>
    </div>
</body>

</html>