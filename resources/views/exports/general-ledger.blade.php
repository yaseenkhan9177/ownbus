<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>General Ledger</title>
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
        <div>General Ledger</div>
        <div>Account: {{ $account->account_code }} - {{ $account->name }}</div>
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
                <th>Date</th>
                <th>Reference</th>
                <th>Description</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Credit</th>
                <th class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="5" style="font-weight: bold; text-align: right;">Opening Balance</td>
                <td class="text-right" style="font-weight: bold;">{{ number_format($report['opening_balance'] ?? 0, 2) }}</td>
            </tr>
            @foreach($report['entries'] ?? [] as $entry)
            <tr>
                <td>{{ \Carbon\Carbon::parse($entry['date'])->format('Y-m-d') }}</td>
                <td>{{ $entry['reference'] }}</td>
                <td>{{ $entry['description'] }}</td>
                <td class="text-right">{{ number_format($entry['debit'], 2) }}</td>
                <td class="text-right">{{ number_format($entry['credit'], 2) }}</td>
                <td class="text-right">{{ number_format($entry['running_balance'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total:</th>
                <th class="text-right">{{ number_format(collect($report['entries'] ?? [])->sum('debit'), 2) }}</th>
                <th class="text-right">{{ number_format(collect($report['entries'] ?? [])->sum('credit'), 2) }}</th>
                <th class="text-right">{{ number_format($report['closing_balance'] ?? 0, 2) }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>