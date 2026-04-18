<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Drivers Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 15px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 14px;
            font-weight: bold;
            color: #64748b;
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
            padding: 8px;
            text-transform: uppercase;
            font-size: 9px;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        .status-badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-active {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-suspended {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-name">{{ $company->name }}</div>
        <div class="report-title">DRIVERS REPORT</div>
        <div class="period">Generated on {{ now()->format('d M Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Phone</th>
                <th>License #</th>
                <th>Expiry</th>
                <th>Status</th>
                <th>Hire Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($drivers as $driver)
            <tr>
                <td>{{ $driver->driver_code }}</td>
                <td>{{ $driver->name }}</td>
                <td>{{ $driver->phone }}</td>
                <td>{{ $driver->license_number }}</td>
                <td>{{ $driver->license_expiry_date?->format('d/m/Y') ?? 'N/A' }}</td>
                <td>
                    <span class="status-badge {{ $driver->status === 'active' ? 'status-active' : 'status-suspended' }}">
                        {{ $driver->status }}
                    </span>
                </td>
                <td>{{ $driver->hire_date?->format('d/m/Y') ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        {{ $company->name }} Fleet Management System
    </div>
</body>

</html>