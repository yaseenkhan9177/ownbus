<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;

class SuperAdminController extends Controller
{
    public function index()
    {
        $pendingCompanies = Company::where('status', 'pending')->latest()->get();
        return view('admin.requests', compact('pendingCompanies'));
    }

    public function approve(Company $company)
    {
        $company->update(['status' => 'active']);

        // Optional: Send email notification here

        return back()->with('success', "Company '{$company->name}' has been approved successfully.");
    }

    public function reject(Company $company)
    {
        $company->update(['status' => 'suspended']);
        return back()->with('success', "Company '{$company->name}' has been rejected/suspended.");
    }
}
