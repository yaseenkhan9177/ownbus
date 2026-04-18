<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Trial Balance</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 15px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 16px;
            font-weight: bold;
            color: #64748b;
            margin-bottom: 5px;
        }

        .period {
            font-size: 11px;
            color: #94a3b8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #0f172a;
            color: white;
            text-align: left;
            padding: 10px;
            text-transform: uppercase;
            font-size: 10px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .total-row {
            background-color: #f8fafc;
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-name">{{ $company->name }}</div>
        <div class="report-title">TRIAL BALANCE</div>
        <div class="period">
            Period: {{ $start->format('d M Y') }} - {{ $end->format('d M Y') }}
            @if($branchId) | Branch: {{ collect($branches)->firstWhere('id', $branchId)->name }} @else | Consolidated @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Account Name</th>
                <th class="text-right">Debit (AED)</th>
                <th class="text-right">Credit (AED)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['accounts'] as $acc)
            <tr>
                <td>{{ $acc['account_code'] }}</td>
                <td>{{ $acc['account_name'] }}</td>
                <td class="text-right">{{ number_format($acc['debit'], 2) }}</td>
                <td class="text-right">{{ number_format($acc['credit'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2" class="text-right">TOTAL</td>
                <td class="text-right">{{ number_format($report['total_debit'], 2) }}</td>
                <td class="text-right">{{ number_format($report['total_credit'], 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Generated on {{ now()->format('d M Y H:i:s') }} | {{ $company->name }} ERP System
    </div>
</body>

</html>