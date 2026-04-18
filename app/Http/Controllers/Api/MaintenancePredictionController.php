<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaintenancePrediction;
use App\Models\VehicleUnavailability;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MaintenancePredictionController extends Controller
{
    /**
     * List all active predictions for a company.
     */
    public function index(Request $request): JsonResponse
    {
        $predictions = MaintenancePrediction::query()
            ->with('vehicle')
            ->whereIn('status', ['pending', 'acknowledged', 'scheduled'])
            ->orderBy('predicted_date', 'asc')
            ->get();

        return response()->json($predictions);
    }

    /**
     * Schedule a maintenance prediction (blocks availability).
     */
    public function schedule(Request $request, $id): JsonResponse
    {
        $prediction = MaintenancePrediction::findOrFail($id);

        $prediction->update(['status' => 'scheduled']);

        // Create a formal unavailability block
        VehicleUnavailability::create([
            'vehicle_id' => $prediction->vehicle_id,
            'start_datetime' => $prediction->predicted_date->startOfDay(),
            'end_datetime' => $prediction->predicted_date->endOfDay(),
            'reason_type' => 'maintenance',
            'description' => 'Automatically scheduled via Predictive Maintenance: ' . $prediction->reason,
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Maintenance scheduled successfully.',
            'prediction' => $prediction
        ]);
    }
}
