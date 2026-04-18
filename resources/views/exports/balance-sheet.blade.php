<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Balance Sheet Report</title>
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
        <div>Balance Sheet</div>
        <div>As of: {{ $asOfDate->format('d M Y') }}</div>
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
                <td colspan="2">Assets</td>
            </tr>
            @foreach($report['assets']['accounts'] ?? [] as $account)
            <tr>
                <td>{{ $account['code'] }} - {{ $account['name'] }}</td>
                <td class="text-right">{{ number_format($account['balance'], 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <th>Total Assets</th>
                <th class="text-right">{{ number_format($report['assets']['total'] ?? 0, 2) }}</th>
            </tr>

            <tr class="section-header">
                <td colspan="2">Liabilities</td>
            </tr>
            @foreach($report['liabilities']['accounts'] ?? [] as $account)
            <tr>
                <td>{{ $account['code'] }} - {{ $account['name'] }}</td>
                <td class="text-right">{{ number_format($account['balance'], 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <th>Total Liabilities</th>
                <th class="text-right">{{ number_format($report['liabilities']['total'] ?? 0, 2) }}</th>
            </tr>

            <tr class="section-header">
                <td colspan="2">Equity</td>
            </tr>
            @foreach($report['equity']['accounts'] ?? [] as $account)
            <tr>
                <td>{{ $account['code'] }} - {{ $account['name'] }}</td>
                <td class="text-right">{{ number_format($account['balance'], 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <th>Total Equity</th>
                <th class="text-right">{{ number_format($report['equity']['total'] ?? 0, 2) }}</th>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th>Total Liabilities & Equity</th>
                <th class="text-right">{{ number_format(($report['liabilities']['total'] ?? 0) + ($report['equity']['total'] ?? 0), 2) }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>