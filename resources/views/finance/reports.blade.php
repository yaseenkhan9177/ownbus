@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Financial Intelligence</h1>
        <div>
            <form action="{{ route('finance.reports') }}" method="GET" class="d-flex gap-2">
                <select name="branch_id" class="form-select">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                    @endforeach
                </select>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate->toDateString() }}">
                <input type="date" name="end_date" class="form-control" value="{{ $endDate->toDateString() }}">
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Profit & Loss -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between">
                    <h5 class="mb-0">Profit & Loss</h5>
                    <small>{{ $startDate->format('d M') }} - {{ $endDate->format('d M Y') }}</small>
                </div>
                <div class="card-body">
                    <h6 class="text-success border-bottom pb-2">Income</h6>
                    <table class="table table-sm table-borderless">
                        @foreach($pnl['income'] as $account)
                        <tr>
                            <td>{{ $account['account_name'] }}</td>
                            <td class="text-end text-success">+{{ number_format($account['balance'], 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="fw-bold fs-5 border-top">
                            <td>Total Income</td>
                            <td class="text-end text-success">{{ number_format($pnl['total_income'], 2) }}</td>
                        </tr>
                    </table>

                    <h6 class="text-danger border-bottom pb-2 mt-4">Expenses</h6>
                    <table class="table table-sm table-borderless">
                        @foreach($pnl['expenses'] as $account)
                        <tr>
                            <td>{{ $account['account_name'] }}</td>
                            <td class="text-end text-danger">-{{ number_format($account['balance'], 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="fw-bold fs-5 border-top">
                            <td>Total Expenses</td>
                            <td class="text-end text-danger">{{ number_format($pnl['total_expenses'], 2) }}</td>
                        </tr>
                    </table>

                    <div class="alert {{ $pnl['net_profit'] >= 0 ? 'alert-success' : 'alert-danger' }} mt-3 text-center">
                        <h4>Net Profit: {{ number_format($pnl['net_profit'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Balance Sheet -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white d-flex justify-content-between">
                    <h5 class="mb-0">Balance Sheet</h5>
                    <small>As of {{ $endDate->format('d M Y') }}</small>
                </div>
                <div class="card-body">
                    <h6 class="text-primary border-bottom pb-2">Assets</h6>
                    <table class="table table-sm table-borderless">
                        @foreach($balanceSheet['assets'] as $account)
                        <tr>
                            <td>{{ $account['account_name'] }}</td>
                            <td class="text-end">{{ number_format($account['balance'], 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="fw-bold border-top">
                            <td>Total Assets</td>
                            <td class="text-end">{{ number_format($balanceSheet['total_assets'], 2) }}</td>
                        </tr>
                    </table>

                    <h6 class="text-warning border-bottom pb-2 mt-4">Liabilities</h6>
                    <table class="table table-sm table-borderless">
                        @foreach($balanceSheet['liabilities'] as $account)
                        <tr>
                            <td>{{ $account['account_name'] }}</td>
                            <td class="text-end">{{ number_format($account['balance'], 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="fw-bold border-top">
                            <td>Total Liabilities</td>
                            <td class="text-end">{{ number_format($balanceSheet['total_liabilities'], 2) }}</td>
                        </tr>
                    </table>

                    <h6 class="text-info border-bottom pb-2 mt-4">Equity</h6>
                    <table class="table table-sm table-borderless">
                        @foreach($balanceSheet['equity'] as $account)
                        <tr>
                            <td>{{ $account['account_name'] }}</td>
                            <td class="text-end">{{ number_format($account['balance'], 2) }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td><em>Retained Earnings (Current Period)</em></td>
                            <td class="text-end">{{ number_format($balanceSheet['net_profit_accumulator'], 2) }}</td>
                        </tr>
                        <tr class="fw-bold border-top">
                            <td>Total Equity</td>
                            <td class="text-end">{{ number_format($balanceSheet['total_equity'], 2) }}</td>
                        </tr>
                    </table>

                    <div class="mt-3 text-center border p-2 rounded bg-light">
                        <small class="d-block text-muted">Accounting Equation Check</small>
                        <span class="fw-bold {{ abs($balanceSheet['total_assets'] - ($balanceSheet['total_liabilities'] + $balanceSheet['total_equity'])) < 0.01 ? 'text-success' : 'text-danger' }}">
                            Assets = Liab + Equity
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection