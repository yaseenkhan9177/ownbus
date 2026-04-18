<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Vendor Bills Report</title>
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
        <div>Vendor Bills Report</div>
        <div>Generated: {{ now()->format('Y-m-d H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Bill #</th>
                <th>Date</th>
                <th>Vendor</th>
                <th>Category</th>
                <th class="text-right">Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vendorBills as $bill)
            <tr>
                <td>{{ $bill->bill_number }}</td>
                <td>{{ $bill->bill_date->format('Y-m-d') }}</td>
                <td>{{ $bill->vendor->name ?? 'N/A' }}</td>
                <td>{{ $bill->items->first()?->expenseAccount->name ?? 'Multiple' }}</td>
                <td class="text-right">{{ number_format($bill->total_amount, 2) }}</td>
                <td>{{ ucfirst($bill->status) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">Total:</th>
                <th class="text-right">{{ number_format($vendorBills->sum('total_amount'), 2) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</body>

</html>