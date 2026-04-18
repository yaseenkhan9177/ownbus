<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SaaS\AdminAnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AdminAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index()
    {
        $stats = $this->analyticsService->getPlatformStats();
        $rentalGrowth = $this->analyticsService->getRentalGrowth();
        $topCompanies = $this->analyticsService->getTopCompanies();

        return view('admin.analytics.index', compact(
            'stats',
            'rentalGrowth',
            'topCompanies'
        ));
    }
}
