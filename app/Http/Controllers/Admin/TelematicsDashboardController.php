<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TelematicsDashboardController extends Controller
{
    /**
     * Show the live GPS tracking dashboard.
     */
    public function index()
    {
        // For Phase 1, we assume the user belongs to a company or is super admin inspecting a company.
        // We'll use auth()->user()->company_id
        $companyId = auth()->user()->company_id;

        return view('admin.telematics.dashboard', compact('companyId'));
    }
}
