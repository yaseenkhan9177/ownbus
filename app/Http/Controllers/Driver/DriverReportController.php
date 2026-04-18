<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\DriverTripReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DriverReportController extends Controller
{
    /* ──────────────────────── FUEL UPLOAD ──────────────────────── */

    public function createFuel()
    {
        return view('driver.fuel.create');
    }

    public function storeFuel(Request $request)
    {
        $request->validate([
            'rental_id'    => 'nullable|exists:rentals,id',
            'fuel_liters'  => 'required|numeric|min:1',
            'fuel_cost'    => 'required|numeric|min:0',
            'odometer'     => 'nullable|integer|min:0',
            'photo'        => 'nullable|image|max:5120', // 5MB max
            'notes'        => 'nullable|string|max:500',
        ]);

        $driverId  = session('driver_id');
        $companyId = session('driver_company_id');

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store("driver-reports/{$companyId}/{$driverId}/fuel", 'public');
        }

        DriverTripReport::create([
            'driver_id'   => $driverId,
            'rental_id'   => $request->rental_id,
            'company_id'  => $companyId,
            'type'        => DriverTripReport::TYPE_FUEL,
            'status'      => DriverTripReport::STATUS_PENDING,
            'notes'       => $request->notes,
            'photo_path'  => $photoPath,
            'metadata'    => [
                'fuel_liters' => $request->fuel_liters,
                'fuel_cost'   => $request->fuel_cost,
                'odometer'    => $request->odometer,
            ],
            'reported_at' => now(),
        ]);

        return redirect()->route('driver.dashboard')
            ->with('success', '⛽ Fuel receipt submitted successfully!');
    }

    /* ──────────────────────── BREAKDOWN REPORT ──────────────────── */

    public function createBreakdown()
    {
        return view('driver.breakdown.create');
    }

    public function storeBreakdown(Request $request)
    {
        $request->validate([
            'rental_id'    => 'nullable|exists:rentals,id',
            'location'     => 'required|string|max:300',
            'description'  => 'required|string|max:1000',
            'photo'        => 'nullable|image|max:5120',
        ]);

        $driverId  = session('driver_id');
        $companyId = session('driver_company_id');

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store("driver-reports/{$companyId}/{$driverId}/breakdown", 'public');
        }

        DriverTripReport::create([
            'driver_id'   => $driverId,
            'rental_id'   => $request->rental_id,
            'company_id'  => $companyId,
            'type'        => DriverTripReport::TYPE_BREAKDOWN,
            'status'      => DriverTripReport::STATUS_PENDING,
            'notes'       => $request->description,
            'photo_path'  => $photoPath,
            'metadata'    => ['location' => $request->location],
            'reported_at' => now(),
        ]);

        return redirect()->route('driver.dashboard')
            ->with('success', '🚨 Breakdown reported! A manager will contact you shortly.');
    }

    /* ──────────────────────── PROFILE ──────────────────────── */

    public function profile()
    {
        $driver = Driver::findOrFail(session('driver_id'));
        $complianceStatus = $driver->getUaeComplianceStatus(30);
        return view('driver.profile', compact('driver', 'complianceStatus'));
    }
}
