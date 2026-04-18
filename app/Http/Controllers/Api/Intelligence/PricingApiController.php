<?php

namespace App\Http\Controllers\Api\Intelligence;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\Branch;
use App\Services\Intelligence\PricingEngineService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PricingApiController extends Controller
{
    protected $pricingEngine;

    public function __construct(PricingEngineService $pricingEngine)
    {
        $this->pricingEngine = $pricingEngine;
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'customer_id' => 'required|exists:customers,id',
            'start_date' => 'required|date',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $customer = Customer::findOrFail($request->customer_id);
        $branch = $vehicle->branch ?: Branch::findOrFail($vehicle->branch_id);
        $startDate = Carbon::parse($request->start_date);

        $result = $this->pricingEngine->calculateRate($vehicle, $branch, $customer, $startDate);

        return response()->json($result);
    }
}
