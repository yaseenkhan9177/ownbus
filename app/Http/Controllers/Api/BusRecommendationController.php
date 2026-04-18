<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Services\BusRecommendationService;
use Illuminate\Http\JsonResponse;

class BusRecommendationController extends Controller
{
    protected $recommendationService;

    public function __construct(BusRecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Get recommendations for a specific rental.
     */
    public function index(Rental $rental): JsonResponse
    {
        $recommendations = $this->recommendationService->recommendBuses($rental);

        return response()->json([
            'rental_uuid' => $rental->uuid,
            'contract_number' => $rental->contract_number,
            'period' => [
                'start' => $rental->start_date?->toDateTimeString(),
                'end' => $rental->end_date?->toDateTimeString(),
            ],
            'recommendations' => $recommendations->map(function ($rec) {
                return [
                    'vehicle' => [
                        'id' => $rec['vehicle']->id,
                        'name' => $rec['vehicle']->name,
                        'vehicle_number' => $rec['vehicle']->vehicle_number,
                        'capacity' => $rec['vehicle']->capacity,
                        'type' => $rec['vehicle']->type,
                    ],
                    'score' => $rec['score'],
                    'breakdown' => $rec['breakdown'],
                    'recommendation_reason' => $rec['reason']
                ];
            })
        ]);
    }
}
