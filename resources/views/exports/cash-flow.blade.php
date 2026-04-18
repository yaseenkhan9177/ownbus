<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Cash Flow Statement</title>
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

        .section-header {
            background-color: #e9ecef;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-name">{{ $company->name }}</div>
        <div>Cash Flow Statement</div>
        <div>Period: {{ $start->format('d M Y') }} to {{ $end->format('d M Y') }}</div>
        @if($branchId)
        <div>Branch: {{ $branches->where('id', $branchId)->first()?->name ?? 'All Branches' }}</div>
        @else
        <div>Branch: All Branches</div>
        @endif
        <div>Generated: {{ now()->format('Y-m-d H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr class="section-header">
                <td colspan="2">Cash Inflow</td>
            </tr>
            @foreach($report['inflows'] ?? [] as $desc => $amount)
            <tr>
                <td>{{ $desc }}</td>
                <td class="text-right">{{ number_format($amount, 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <th>Total Inflows</th>
                <th class="text-right">{{ number_format($report['total_inflows'] ?? 0, 2) }}</th>
            </tr>

            <tr class="section-header">
                <td colspan="2">Cash Outflow</td>
            </tr>
            @foreach($report['outflows'] ?? [] as $desc => $amount)
            <tr>
                <td>{{ $desc }}</td>
                <td class="text-right">-{{ number_format(abs($amount), 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <th>Total Outflows</th>
                <th class="text-right">-{{ number_format(abs($report['total_outflows'] ?? 0), 2) }}</th>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th>Net Cash Flow</th>
                <th class="text-right">{{ number_format($report['net_cashflow'] ?? 0, 2) }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>