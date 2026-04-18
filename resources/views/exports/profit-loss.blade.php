<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Profit &amp; Loss Report</title>
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
        <div>Profit &amp; Loss Statement</div>
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
                <th>Account</th>
                <th class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            <tr class="section-header">
                <td colspan="2">Revenue</td>
            </tr>
            @foreach($report['income'] ?? [] as $account)
            <tr>
                <td>{{ $account['account_code'] }} - {{ $account['account_name'] }}</td>
                <td class="text-right">{{ number_format($account['balance'], 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <th>Total Revenue</th>
                <th class="text-right">{{ number_format($report['total_income'] ?? 0, 2) }}</th>
            </tr>

            <tr class="section-header">
                <td colspan="2">Expenses</td>
            </tr>
            @foreach($report['expenses'] ?? [] as $account)
            <tr>
                <td>{{ $account['account_code'] }} - {{ $account['account_name'] }}</td>
                <td class="text-right">{{ number_format($account['balance'], 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <th>Total Expenses</th>
                <th class="text-right">{{ number_format($report['total_expenses'] ?? 0, 2) }}</th>
            </tr>

        </tbody>
        <tfoot>
            <tr>
                <th>Net Profit</th>
                <th class="text-right">{{ number_format($report['net_profit'] ?? 0, 2) }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>