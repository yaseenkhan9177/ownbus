<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Contract #{{ $contract->contract_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 20px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #1a202c;
        }

        .doc-title {
            font-size: 18px;
            color: #4a5568;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 10px;
        }

        .row {
            width: 100%;
            margin-bottom: 20px;
            clear: both;
        }

        .col-half {
            width: 48%;
            float: left;
        }

        .col-half-right {
            width: 48%;
            float: right;
            text-align: right;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
            color: #2d3748;
        }

        .info-table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table th,
        .info-table td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #edf2f7;
        }

        .info-table th {
            width: 35%;
            color: #718096;
            text-transform: uppercase;
            font-size: 10px;
        }

        .info-table td {
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #a0aec0;
            border-top: 1px solid #edf2f7;
            padding-top: 10px;
        }

        .page-break {
            page-break-after: always;
        }

        .signature-box {
            margin-top: 50px;
            border-top: 1px solid #333;
            padding-top: 10px;
            width: 250px;
            text-align: center;
            display: inline-block;
            font-size: 11px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .terms {
            font-size: 10px;
            color: #4a5568;
            line-height: 1.6;
            text-align: justify;
        }

        .clear {
            clear: both;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="company-name">{{ $company->name }}</div>
        <div style="color: #718096;">RENTAL AGREEMENT & CONTRACT</div>
        <div class="doc-title">CONTRACT #{{ $contract->contract_number }}</div>
    </div>

    <div class="row">
        <div class="col-half">
            <div class="section-title">Client Information</div>
            <table class="info-table">
                <tr>
                    <th>Name/Company</th>
                    <td>{{ $contract->customer->company_name ?: $contract->customer->name }}</td>
                </tr>
                <tr>
                    <th>Contact</th>
                    <td>{{ $contract->customer->phone }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $contract->customer->email ?: 'N/A' }}</td>
                </tr>
            </table>
        </div>
        <div class="col-half-right">
            <div class="section-title">Contract Details</div>
            <table class="info-table">
                <tr>
                    <th>Date Created</th>
                    <td class="text-right">{{ $contract->created_at->format('d M Y') }}</td>
                </tr>
                <tr>
                    <th>Start Date</th>
                    <td class="text-right">{{ $contract->start_date->format('d M Y') }} {{ $contract->start_time }}</td>
                </tr>
                <tr>
                    <th>End Date</th>
                    <td class="text-right">{{ $contract->end_date->format('d M Y') }} {{ $contract->end_time }}</td>
                </tr>
                <tr>
                    <th>Total Days</th>
                    <td class="text-right">{{ $contract->start_date->diffInDays($contract->end_date) }} Days</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="clear"></div>

    <div class="row">
        <div class="col-half">
            <div class="section-title">Asset Deployment</div>
            <table class="info-table">
                <tr>
                    <th>Vehicle Reg</th>
                    <td>{{ $contract->vehicle->vehicle_number }}</td>
                </tr>
                <tr>
                    <th>Model</th>
                    <td>{{ $contract->vehicle->make }} {{ $contract->vehicle->model }} ({{ $contract->vehicle->year }})</td>
                </tr>
                @if($contract->driver)
                <tr>
                    <th>Driver</th>
                    <td>{{ $contract->driver->name }} (Lic: {{ $contract->driver->license_number }})</td>
                </tr>
                @else
                <tr>
                    <th>Driver</th>
                    <td>Self-Drive</td>
                </tr>
                @endif
            </table>
        </div>
        <div class="col-half-right">
            <div class="section-title">Financial & Billing Matrix</div>
            <table class="info-table">
                <tr>
                    <th>Billing Cycle</th>
                    <td class="text-right">{{ ucfirst($contract->billing_cycle) }}</td>
                </tr>
                <tr>
                    <th>Base Value</th>
                    <td class="text-right">{{ number_format($contract->contract_value, 2) }} AED</td>
                </tr>
                <tr>
                    <th>Surcharges</th>
                    <td class="text-right">+ {{ number_format($contract->extra_charges, 2) }} AED</td>
                </tr>
                <tr>
                    <th>Discount</th>
                    <td class="text-right">- {{ number_format($contract->discount, 2) }} AED</td>
                </tr>
                <tr>
                    <th style="color: #1a202c; font-size: 12px; border-top: 1px solid #cbd5e0;">Net Payable</th>
                    <td class="text-right" style="color: #1a202c; font-size: 14px; border-top: 1px solid #cbd5e0;">
                        {{ number_format($contract->contract_value + $contract->extra_charges - $contract->discount, 2) }} AED
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="clear"></div>

    <div class="row" style="margin-top: 30px;">
        <div class="section-title">Payment Terms</div>
        <p class="terms">
            {{ $contract->payment_terms ?: 'Standard Commercial Tranches Appended.' }}
            @if($contract->payment_due_date)
            <br><strong>Initial Settlement Deadline:</strong> {{ $contract->payment_due_date->format('d M Y') }}
            @endif
        </p>
    </div>

    <div class="row" style="margin-top: 30px;">
        <div class="section-title">Standard Policy Framework & Terms</div>
        <div class="terms">
            {!! nl2br(e($contract->terms ?: 'Standard operative terms and conditions apply as per general service agreement.')) !!}
        </div>
    </div>

    <div class="row" style="margin-top: 80px;">
        <div class="col-half text-center">
            <div class="signature-box">
                Authorized Signatory - {{ $company->name }}<br>
                Date: __________________
            </div>
        </div>
        <div class="col-half text-center">
            <div class="signature-box">
                Authorized Signatory - Client<br>
                Date: __________________
            </div>
        </div>
    </div>

    <div class="footer">
        Generated securely by {{ $company->name }} ERP Engine on {{ now()->format('Y-m-d H:i:s') }}
    </div>
</body>

</html>