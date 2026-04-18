<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoices Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .header {
            margin-bottom: 30px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-name">{{ $company->name }}</div>
        <div>Invoices Report</div>
        <div>Generated: {{ now()->format('Y-m-d H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Vehicle</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
            <tr>
                <td>#{{ substr($invoice->uuid ?? $invoice->id, 0, 8) }}</td>
                <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                <td>{{ $invoice->customer->name ?? 'N/A' }}</td>
                <td>{{ $invoice->bus->vehicle_number ?? 'N/A' }}</td>
                <td class="text-right">{{ number_format($invoice->final_amount, 2) }}</td>
                <td>{{ ucfirst($invoice->status) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">Total:</th>
                <th class="text-right">{{ number_format($invoices->sum('final_amount'), 2) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</body>

</html>