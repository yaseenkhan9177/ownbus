<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminBroadcast;
use App\Models\Company;
use Illuminate\Http\Request;

class BroadcastController extends Controller
{
    public function index()
    {
        $broadcasts = AdminBroadcast::with('company')->latest()->paginate(20);
        $companies = Company::where('status', 'active')->orderBy('name')->get();
        return view('admin.broadcasts.index', compact('broadcasts', 'companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'target_role' => 'required|in:all,company_admin,driver,customer',
            'company_id' => 'nullable|exists:companies,id',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $validated['is_active'] = $request->has('is_active');

        AdminBroadcast::create($validated);

        return redirect()->back()->with('success', 'System Broadcast deployed successfully.');
    }

    public function destroy(AdminBroadcast $broadcast)
    {
        // Actually acts as a toggle to preserve history instead of hard deleting
        $broadcast->update(['is_active' => !$broadcast->is_active]);

        $action = $broadcast->is_active ? 'reactivated' : 'deactivated';
        return redirect()->back()->with('success', "Broadcast has been {$action}.");
    }
}
