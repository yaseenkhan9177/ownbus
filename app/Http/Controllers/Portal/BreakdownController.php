<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\DriverTripReport;
use Illuminate\Http\Request;

class BreakdownController extends Controller
{
    public function index(Request $request)
    {
        $query = DriverTripReport::where('type', DriverTripReport::TYPE_BREAKDOWN)
            ->with(['driver.user', 'vehicle', 'rental'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        $reports = $query->paginate(15);
        $pendingCount = DriverTripReport::where('type', DriverTripReport::TYPE_BREAKDOWN)
            ->where('status', DriverTripReport::STATUS_PENDING)
            ->count();

        return view('portal.breakdowns.index', compact('reports', 'pendingCount'));
    }

    public function show(DriverTripReport $breakdown)
    {
        if ($breakdown->type !== DriverTripReport::TYPE_BREAKDOWN) {
            abort(404);
        }

        $breakdown->load(['driver.user', 'vehicle', 'rental']);
        return view('portal.breakdowns.show', compact('breakdown'));
    }

    public function acknowledge(DriverTripReport $breakdown)
    {
        if ($breakdown->type !== DriverTripReport::TYPE_BREAKDOWN) {
            abort(404);
        }

        $breakdown->update([
            'status' => DriverTripReport::STATUS_ACKNOWLEDGED,
            'acknowledged_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Breakdown report acknowledged.');
    }

    public function resolve(DriverTripReport $breakdown)
    {
        if ($breakdown->type !== DriverTripReport::TYPE_BREAKDOWN) {
            abort(404);
        }

        $breakdown->update([
            'status' => DriverTripReport::STATUS_COMPLETED,
        ]);

        return redirect()->back()->with('success', 'Breakdown issue marked as resolved.');
    }
}
