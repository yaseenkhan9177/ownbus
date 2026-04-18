<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Carbon\Carbon;

class AuditLogController extends Controller
{
    /**
     * Display a paginated, filterable grid of all global audit logs.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with(['user', 'company'])
            ->latest()
            ->when($request->filled('action'), function ($q) use ($request) {
                return $q->where('action', $request->action);
            })
            ->when($request->filled('module'), function ($q) use ($request) {
                // Allows filtering by "Vehicle", "User", etc.
                return $q->where('module', 'like', '%' . $request->module . '%');
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                return $q->where(function ($subQ) use ($search) {
                    $subQ->where('module', 'like', "%{$search}%")
                        ->orWhere('action', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uQ) use ($search) {
                            $uQ->where('first_name', 'like', "%{$search}%")
                               ->orWhere('last_name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('date_range'), function ($q) use ($request) {
                $dates = explode(' to ', $request->date_range);
                $start = Carbon::parse($dates[0])->startOfDay();
                $end = isset($dates[1]) ? Carbon::parse($dates[1])->endOfDay() : $start->copy()->endOfDay();

                return $q->whereBetween('created_at', [$start, $end]);
            });

        $logs = $query->paginate(50)->withQueryString();

        return view('admin.audit-logs.index', compact('logs'));
    }
}
