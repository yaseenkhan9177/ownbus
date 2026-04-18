<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Services\Intelligence\ExecutiveDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExecutiveDashboardController extends Controller
{
    protected $intelligence;

    public function __construct(ExecutiveDashboardService $intelligence)
    {
        $this->intelligence = $intelligence;
    }

    /**
     * Display the Executive High-Fidelity Dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;
        $branchId = $request->get('branch_id'); // Optional filter

        if (!$company) {
            return redirect()->back()->with('error', 'Company context required.');
        }

        $stats = $this->intelligence->getDashboardStats($company->id, $branchId);

        return view('portal.intelligence.executive', compact('stats'));
    }
}
