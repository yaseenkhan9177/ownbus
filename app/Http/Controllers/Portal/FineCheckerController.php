<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleFine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FineCheckerController extends Controller
{
    public function index()
    {
        $branchId = Auth::user()->branch_id;

        $vehiclesQuery = Vehicle::query()
            ->with(['fines' => fn($q) => $q->whereIn('status', ['unpaid', 'under-processing'])])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('status', '!=', 'inactive')
            ->orderBy('vehicle_number');

        $vehicles = $vehiclesQuery->get();

        $finesQuery = VehicleFine::with(['vehicle'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->latest('fine_date');

        $fines = $finesQuery->get();

        // Stats
        $totalOutstanding = $fines->whereIn('status', ['unpaid', 'under-processing'])->sum('amount');
        $unpaidCount      = $fines->where('status', 'unpaid')->count();
        $paidThisMonth    = $fines->where('status', 'paid')
            ->filter(fn($f) => $f->paid_at && $f->paid_at->isCurrentMonth())
            ->sum('amount');
        $vehiclesChecked  = $vehicles->filter(fn($v) => $v->fines->isNotEmpty())->count();

        // Safely get last sync — falls back to updated_at if last_checked_at column doesn't exist yet
        try {
            $lastSync = VehicleFine::when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->max('last_checked_at');
        } catch (\Exception $e) {
            $lastSync = VehicleFine::when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->max('updated_at');
        }

        return view('portal.fines.checker', compact(
            'vehicles',
            'fines',
            'totalOutstanding',
            'unpaidCount',
            'paidThisMonth',
            'vehiclesChecked',
            'lastSync'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id'       => 'required|exists:tenant.vehicles,id',
            'authority'        => 'required|string|max:255',
            'fine_number'      => 'required|string|max:255',
            'fine_type'        => 'nullable|string|max:255',
            'fine_date'        => 'required|date',
            'due_date'         => 'nullable|date|after_or_equal:fine_date',
            'amount'           => 'required|numeric|min:0.01',
            'description'      => 'nullable|string|max:1000',
            'responsible_type' => 'nullable|in:company,driver,customer',
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);

        $fineData = [
            'vehicle_id'       => $vehicle->id,
            'branch_id'        => $vehicle->branch_id,
            'authority'        => $validated['authority'],
            'fine_number'      => $validated['fine_number'],
            'fine_type'        => $validated['fine_type'] ?? 'Traffic Violation',
            'fine_date'        => $validated['fine_date'],
            'due_date'         => $validated['due_date'] ?? null,
            'amount'           => $validated['amount'],
            'description'      => $validated['description'] ?? null,
            'responsible_type' => $validated['responsible_type'] ?? 'company',
            'status'           => 'unpaid',
            'source'           => 'Manual',
            'created_by'       => Auth::id(),
        ];

        // Only set last_checked_at if the column exists (migration may not have run)
        try {
            \Illuminate\Support\Facades\Schema::connection('tenant')->hasColumn('vehicle_fines', 'last_checked_at')
                && ($fineData['last_checked_at'] = now());
        } catch (\Exception $e) {}

        VehicleFine::create($fineData);

        return redirect()->route('company.fines.checker')
            ->with('success', 'Fine recorded successfully.');
    }

    public function markPaid(VehicleFine $fine)
    {
        $fine->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Fine marked as paid.');
    }

    public function dispute(VehicleFine $fine)
    {
        $fine->update(['status' => 'appealed']);
        return back()->with('success', 'Fine marked as disputed/appealed.');
    }

    public function destroy(VehicleFine $fine)
    {
        $fine->delete();
        return back()->with('success', 'Fine record deleted.');
    }
}
