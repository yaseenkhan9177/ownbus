<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleFine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FineCheckerController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        $branchId = Auth::user()->branch_id;
        
        $vehicles = Vehicle::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('status', '!=', 'inactive')
            ->get();
            
        return view('portal.fines.checker', compact('vehicles'));
    }

    public function record(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:tenant.vehicles,id',
            'fine_number' => 'required|string|unique:tenant.vehicle_fines,fine_number',
            'fine_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'authority' => 'required|string',
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);

        $fine = new VehicleFine();
        $fine->vehicle_id = $vehicle->id;
        $fine->branch_id = $vehicle->branch_id;
        $fine->fine_number = $validated['fine_number'];
        $fine->fine_date = $validated['fine_date'];
        $fine->amount = $validated['amount'];
        $fine->authority = $validated['authority'];
        $fine->status = 'unpaid';
        $fine->source = 'Manual Check';
        $fine->description = 'Recorded via Fine Checker Tool';
        $fine->responsible_type = 'company';
        $fine->last_checked_at = now();
        $fine->created_by = Auth::id();
        $fine->save();

        // Update the last_checked_at on the vehicle, wait, vehicle doesn't have last_checked_at. VehicleFine has.
        // I will just return success.
        return redirect()->route('company.fines.checker')->with('success', 'Traffic fine recorded successfully & logged inside the system!');
    }
}
