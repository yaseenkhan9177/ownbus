<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinancialForecast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancialForecastController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'metric_type' => 'nullable|string|in:revenue,expense',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Ideally check permissions here, e.g., 'view_financial_reports'

        $query = FinancialForecast::query();

        if ($request->has('metric_type')) {
            $query->where('metric_type', $request->input('metric_type'));
        }

        if ($request->has('start_date')) {
            $query->where('forecast_date', '>=', $request->input('start_date'));
        }

        if ($request->has('end_date')) {
            $query->where('forecast_date', '<=', $request->input('end_date'));
        }

        $forecasts = $query->orderBy('forecast_date')->get();

        return response()->json([
            'status' => 'success',
            'data' => $forecasts
        ]);
    }
}
