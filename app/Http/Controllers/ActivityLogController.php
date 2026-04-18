<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->can('view_activity_logs') && !$user->hasRole('admin')) {
            abort(403);
        }

        $logs = ActivityLog::where('company_id', $user->company_id)
            ->with(['user', 'subject'])
            ->latest()
            ->paginate(50);

        return view('admin.activity.index', compact('logs'));
    }
}
