<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BusRecommendationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FleetRecommendationController extends Controller
{
    protected $recommendationService;

    public function __construct(BusRecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    public function index(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'nullable|string',
            'limit' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->end_date);
            $type = $request->input('type');
            $limit = $request->input('limit', 5);

            $recommendations = $this->recommendationService->recommend($start, $end, $type, $limit);

            return response()->json([
                'status' => 'success',
                'data' => $recommendations
            ]);
        } catch (\Exception $e) {
            Log::error('Fleet Recommendation Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to generate recommendations.'
            ], 500);
        }
    }
}
