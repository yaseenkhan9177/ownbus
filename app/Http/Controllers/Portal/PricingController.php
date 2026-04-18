<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Services\Intelligence\PricingEngineService;
use App\Models\Vehicle;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\SeasonalPricingRule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PricingController extends Controller
{
    protected $pricingService;

    public function __construct(PricingEngineService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Show pricing rules and calculator
     */
    public function index()
    {
        $vehicles = Vehicle::where('status', 'available')->get(['id', 'name', 'vehicle_number', 'type', 'daily_rate']);
        $branches = Branch::all(['id', 'name']);
        $customers = Customer::where('status', 'active')->get(['id', 'first_name', 'last_name', 'company_name', 'type']);
        
        $rules = SeasonalPricingRule::where('is_active', true)
            ->with('branch')
            ->orderBy('start_date')
            ->get();

        return view('portal.pricing.index', compact('vehicles', 'branches', 'customers', 'rules'));
    }

    /**
     * AJAX calculation
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:tenant.vehicles,id',
            'branch_id' => 'required|exists:tenant.branches,id',
            'customer_id' => 'required|exists:tenant.customers,id',
            'start_date' => 'required|date',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $branch = Branch::findOrFail($request->branch_id);
        $customer = Customer::findOrFail($request->customer_id);
        $startDate = Carbon::parse($request->start_date);

        $result = $this->pricingService->calculateRate($vehicle, $branch, $customer, $startDate);

        return response()->json($result);
    }
}
