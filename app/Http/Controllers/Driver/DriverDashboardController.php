<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Rental;
use Carbon\Carbon;

class DriverDashboardController extends Controller
{
    public function index()
    {
        $driverId = session('driver_id');
        $driver = Driver::with(['company'])->findOrFail($driverId);

        // Today's active rental assigned to this driver
        $todayRental = Rental::where('driver_id', $driverId)
            ->whereIn('status', ['active', 'confirmed'])
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->with(['vehicle', 'customer'])
            ->first();

        // Upcoming rentals (next 7 days)
        $upcomingRentals = Rental::where('driver_id', $driverId)
            ->whereIn('status', ['confirmed', 'pending'])
            ->whereDate('start_date', '>', today())
            ->whereDate('start_date', '<=', today()->addDays(7))
            ->with(['vehicle', 'customer'])
            ->orderBy('start_date')
            ->take(5)
            ->get();

        // Recent reports submitted by this driver
        $recentReports = \App\Models\DriverTripReport::where('driver_id', $driverId)
            ->orderByDesc('reported_at')
            ->take(5)
            ->get();

        // Driver document status
        $complianceStatus = $driver->getUaeComplianceStatus(30);

        return view('driver.dashboard', compact(
            'driver',
            'todayRental',
            'upcomingRentals',
            'recentReports',
            'complianceStatus'
        ));
    }
}
