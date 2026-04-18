<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anomaly;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnomalyController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        // Permission check...

        $query = Anomaly::query();

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        $anomalies = $query->latest()->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $anomalies
        ]);
    }

    public function resolve(Request $request, $id)
    {
        $user = Auth::user();
        $anomaly = Anomaly::findOrFail($id);

        $anomaly->update([
            'status' => 'resolved',
            'resolved_at' => Carbon::now(),
            'resolved_by' => $user->id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Anomaly marked as resolved.'
        ]);
    }
}
