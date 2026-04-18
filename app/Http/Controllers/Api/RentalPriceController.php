<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Services\RentalPriceCalculator;
use Illuminate\Http\Request;

class RentalPriceController extends Controller
{
    protected $calculator;

    public function __construct(RentalPriceCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function calculate(Request $request)
    {
        // 1. If existing Rental ID provided, use it
        if ($request->has('rental_id')) {
            $rental = Rental::findOrFail($request->rental_id);
        } else {
            // 2. Simulate a Rental object with enterprise field names
            $rental = new Rental([
                'company_id' => $request->user()->company_id,
                'rental_type' => $request->input('rental_type', 'daily'),
                'rate_type' => $request->input('rate_type', 'daily'),
                'rate_amount' => $request->input('rate_amount', 0),
                'discount' => $request->input('discount', 0),
                'start_date' => $request->input('pickup_time') ?: $request->input('start_date'),
                'end_date' => $request->input('dropoff_time') ?: $request->input('end_date'),
            ]);
        }

        $breakdown = $this->calculator->calculate($rental);

        return response()->json($breakdown->toArray());
    }
}
