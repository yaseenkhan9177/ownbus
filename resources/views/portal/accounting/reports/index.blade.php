@extends('layouts.company')

@section('title', 'Financial Intelligence Reports')

@section('content')
<div class="container-fluid py-5">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h2 class="fw-bold text-dark mb-2">Financial Intelligence</h2>
            <p class="text-muted fs-5">Real-time ERP insights and decision-ready reporting</p>
            <div class="mx-auto bg-primary rounded-pill mt-3" style="width: 60px; height: 4px;"></div>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        @php
        $reports = [
        [
        'route' => 'company.accounting.coa',
        'title' => 'Chart of Accounts',
        'subtitle' => 'Ledger Architecture',
        'desc' => 'Hierarchical ledger structure & classification for the enterprise.',
        'icon' => 'fa-sitemap',
        'color' => '#4e73df',
        'bg_light' => '#eef2ff',
        'border_color' => 'rgba(78, 115, 223, 0.2)'
        ],
        [
        'route' => 'company.accounting.reports.pnl',
        'title' => 'Profit & Loss',
        'subtitle' => 'Income Statement',
        'desc' => 'Monitor revenue performance and operational efficiency over time.',
        'icon' => 'fa-chart-line',
        'color' => '#1cc88a',
        'bg_light' => '#e8f7f1',
        'border_color' => 'rgba(28, 200, 138, 0.2)'
        ],
        [
        'route' => 'company.accounting.reports.balance-sheet',
        'title' => 'Balance Sheet',
        'subtitle' => 'Financial Position',
        'desc' => 'Snapshot of your assets, liabilities, and equity at any given moment.',
        'icon' => 'fa-balance-scale',
        'color' => '#4e73df',
        'bg_light' => '#eef2ff',
        'border_color' => 'rgba(78, 115, 223, 0.2)'
        ],
        [
        'route' => 'company.accounting.reports.trial-balance',
        'title' => 'Trial Balance',
        'subtitle' => 'Ledger Integrity',
        'desc' => 'Validate double-entry consistency and accounting book health.',
        'icon' => 'fa-layer-group',
        'color' => '#36b9cc',
        'bg_light' => '#e7f7fa',
        'border_color' => 'rgba(54, 185, 204, 0.2)'
        ],
        [
        'route' => 'company.accounting.reports.general-ledger',
        'title' => 'General Ledger',
        'subtitle' => 'Transaction Trace',
        'desc' => 'Deep dive into specific account movements with running balances.',
        'icon' => 'fa-list-ul',
        'color' => '#858796',
        'bg_light' => '#f8f9fc',
        'border_color' => 'rgba(133, 135, 150, 0.2)'
        ],
        [
        'route' => 'company.accounting.reports.cash-flow',
        'title' => 'Cash Flow',
        'subtitle' => 'Liquidity View',
        'desc' => 'Analyze cash movements across operations, investments, and financing.',
        'icon' => 'fa-water',
        'color' => '#f6c23e',
        'bg_light' => '#fffbf0',
        'border_color' => 'rgba(246, 194, 62, 0.2)'
        ]
        ];
        @endphp

        @foreach($reports as $report)
        <div class="col-xl-4 col-md-6 hvr-float-shadow">
            <a href="{{ route($report['route']) }}" class="text-decoration-none h-100 d-block">
                <div class="card glass-card border-0 shadow-sm h-100 transition-all report-card"
                    style="--accent-color: {{ $report['color'] }}; background: white; overflow: hidden; position: relative;">

                    <!-- Decorative Soft Glow Background -->
                    <div class="card-glow" style="background: radial-gradient(circle at top right, {{ $report['bg_light'] }}, transparent 70%);"></div>

                    <div class="card-body p-4 position-relative z-index-1">
                        <div class="d-flex align-items-start mb-4">
                            <div class="icon-box rounded-3 d-flex align-items-center justify-content-center shadow-sm"
                                style="width: 56px; height: 56px; background: {{ $report['bg_light'] }}; border: 1px solid {{ $report['border_color'] }};">
                                <i class="fas {{ $report['icon'] }} fs-4" style="color: {{ $report['color'] }};"></i>
                            </div>
                            <div class="ms-3 pt-1">
                                <h5 class="fw-bold mb-0 text-dark">{{ $report['title'] }}</h5>
                                <span class="badge rounded-pill mt-1" style="background: {{ $report['bg_light'] }}; color: {{ $report['color'] }}; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ $report['subtitle'] }}</span>
                            </div>
                        </div>

                        <p class="text-muted small mb-4 leading-relaxed" style="min-height: 48px;">
                            {{ $report['desc'] }}
                        </p>

                        <div class="d-flex align-items-center justify-content-between mt-auto">
                            <span class="text-primary fw-600 small">Generate Analysis <i class="fas fa-arrow-right ms-1 transition-all arrow-icon"></i></span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; border: 1px solid #eee;">
                                <i class="fas fa-chevron-right text-muted" style="font-size: 0.6rem;"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Accent Border -->
                    <div class="position-absolute bottom-0 start-0 w-100 accent-bar" style="height: 3px; background: {{ $report['color'] }}; opacity: 0.3; transition: all 0.3s ease;"></div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }

    .glass-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(255, 255, 255, 0.4) !important;
        background-color: #ffffff;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .report-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 1.5rem 4rem rgba(0, 0, 0, 0.08) !important;
        border-color: var(--accent-color) !important;
    }

    .report-card:hover .accent-bar {
        height: 6px;
        opacity: 1;
    }

    .report-card:hover .arrow-icon {
        transform: translateX(5px);
    }

    .report-card:hover .icon-box {
        transform: scale(1.05);
        background: white !important;
    }

    .icon-box {
        transition: all 0.3s ease;
    }

    .card-glow {
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
        opacity: 0.6;
        transition: opacity 0.3s ease;
    }

    .report-card:hover .card-glow {
        opacity: 1;
    }

    .fw-600 {
        font-weight: 600;
    }

    .leading-relaxed {
        line-height: 1.6;
    }

    .transition-all {
        transition: all 0.3s ease;
    }

    /* Animation for Entrance */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .col-xl-4 {
        animation: fadeInUp 0.6s ease-out forwards;
    }

    .col-xl-4:nth-child(2) {
        animation-delay: 0.1s;
    }

    .col-xl-4:nth-child(3) {
        animation-delay: 0.2s;
    }

    .col-xl-4:nth-child(4) {
        animation-delay: 0.3s;
    }

    .col-xl-4:nth-child(5) {
        animation-delay: 0.4s;
    }
</style>
@endsection