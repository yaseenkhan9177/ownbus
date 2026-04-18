<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = SubscriptionPlan::withCount('subscriptions')->orderBy('price_monthly')->get();
        return view('admin.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.plans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'trial_days' => 'required|integer|min:0',
            'max_vehicles' => 'required|integer|min:-1',
            'max_users' => 'required|integer|min:-1',
            'features' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        $features = $request->input('features', []);
        $features['max_vehicles'] = $validated['max_vehicles'] == -1 ? 99999 : $validated['max_vehicles'];
        $features['max_users'] = $validated['max_users'] == -1 ? 99999 : $validated['max_users'];

        SubscriptionPlan::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'version' => '1.0',
            'price_monthly' => $validated['price_monthly'],
            'price_yearly' => $validated['price_yearly'],
            'trial_days' => $validated['trial_days'],
            'grace_period_days' => 3, // Default grace period
            'features' => $features,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.plans.index')->with('success', 'Subscription Plan created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Plans don't typically need a standalone show page right now
        return redirect()->route('admin.plans.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubscriptionPlan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'trial_days' => 'required|integer|min:0',
            'max_vehicles' => 'required|integer|min:-1',
            'max_users' => 'required|integer|min:-1',
            'features' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        $features = $request->input('features', []);
        $features['max_vehicles'] = $validated['max_vehicles'] == -1 ? 99999 : $validated['max_vehicles'];
        $features['max_users'] = $validated['max_users'] == -1 ? 99999 : $validated['max_users'];

        $plan->update([
            'name' => $validated['name'],
            // Don't change slug on update to avoid breaking existing links
            'price_monthly' => $validated['price_monthly'],
            'price_yearly' => $validated['price_yearly'],
            'trial_days' => $validated['trial_days'],
            'features' => $features,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.plans.index')->with('success', 'Subscription Plan updated successfully.');
    }

    /**
     * Remove the specified resource from storage or toggle.
     */
    public function destroy(SubscriptionPlan $plan)
    {
        if ($plan->subscriptions()->where('status', 'active')->exists()) {
            return back()->with('error', 'Cannot delete a plan with active subscriptions. Try deactivating it instead.');
        }

        $plan->delete();
        return redirect()->route('admin.plans.index')->with('success', 'Plan deleted successfully.');
    }
}
