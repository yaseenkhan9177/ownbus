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
            font-size: 10px;
            color: #1e293b;
        }

        .header {
            background: #059669;
            color: white;
            padding: 16px 20px;
            margin-bottom: 14px;
        }

        .header h1 {
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .header p {
            font-size: 8px;
            color: #a7f3d0;
            margin-top: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        th {
            background: #f0fdf4;
            color: #065f46;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 7px 10px;
            text-align: left;
            border-bottom: 2px solid #d1fae5;
        }

        td {
            padding: 7px 10px;
            border-bottom: 1px solid #f0fdf4;
        }

        tr:last-child td {
            font-weight: 700;
            background: #f8fafc;
            border-top: 2px solid #e2e8f0;
        }

        .num {
            text-align: right;
            font-family: monospace;
            font-weight: 600;
        }

        .footer {
            margin-top: 14px;
            font-size: 7px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $company->name }} – Revenue Report</h1>
        <p>Generated: {{ now()->format('d F Y, H:i') }} | {{ count($revenueData) }}-Month Overview</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th class="num">Rentals</th>
                <th class="num">Revenue (AED)</th>
                <th class="num">Avg Per Rental (AED)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenueData as $row)
            <tr>
                <td>{{ $row['month'] }}</td>
                <td class="num">{{ $row['count'] }}</td>
                <td class="num">{{ number_format($row['total'], 2) }}</td>
                <td class="num">{{ $row['count'] > 0 ? number_format($row['total'] / $row['count'], 2) : '0.00' }}</td>
            </tr>
            @endforeach
            <tr>
                <td><strong>TOTAL</strong></td>
                <td class="num">{{ collect($revenueData)->sum('count') }}</td>
                <td class="num">{{ number_format(collect($revenueData)->sum('total'), 2) }}</td>
                <td class="num">-</td>
            </tr>
        </tbody>
    </table>
    <div class="footer">All amounts in UAE Dirhams (AED). VAT 5% may apply per UAE Federal Tax Authority regulations.</div>
</body>

</html>