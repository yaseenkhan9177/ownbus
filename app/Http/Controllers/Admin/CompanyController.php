<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\CompanyRepository;
use App\Models\SubscriptionPlan;

class CompanyController extends Controller
{
    protected CompanyRepository $companyRepo;

    public function __construct(CompanyRepository $companyRepo)
    {
        $this->companyRepo = $companyRepo;
    }
    /**
     * Impersonate a Company Owner
     * 
     * @param Request $request
     * @param \App\Models\Company $company
     */
    public function impersonate(Request $request, \App\Models\Company $company)
    {
        if ($company->owner) {
            $request->session()->put('impersonator_id', auth()->id());
            auth()->loginUsingId($company->owner->id);
            return redirect()->route('company.dashboard')->with('success', "Now impersonating {$company->name}");
        }

        return back()->with('error', 'Company does not have an owner to impersonate.');
    }

    public function leaveImpersonation(Request $request)
    {
        if ($request->session()->has('impersonator_id')) {
            $adminId = $request->session()->get('impersonator_id');
            $request->session()->forget('impersonator_id');
            auth()->loginUsingId($adminId);
            return redirect()->route('admin.dashboard')->with('success', 'Restored to Super Admin account.');
        }

        return back()->with('error', 'You are not impersonating anyone.');
    }

    /**
     * Approve a pending Company
     * 
     * @param Request $request
     * @param \App\Models\Company $company
     */
    public function approve(Request $request, \App\Models\Company $company)
    {
        if ($company->status !== 'pending') {
            return back()->with('error', 'Only pending companies can be approved.');
        }

        $company->update(['status' => 'active']);

        $settings = $company->companyNotificationSettings;
        if ($settings && $settings->whatsapp_enabled && $settings->whatsapp_number) {
            \App\Jobs\SendWhatsAppJob::dispatch(
                $settings->whatsapp_number,
                'new_registration',
                [
                    'company_name' => $company->name,
                    'email' => $company->email,
                    'plan_name' => $company->subscription->plan->name ?? 'Standard',
                ]
            );
        }

        return back()->with('success', "Tenant {$company->name} has been approved and is now active.");
    }

    /**
     * Suspend or Unsuspend a Company
     */
    public function toggleStatus(Request $request, \App\Models\Company $company)
    {
        if ($company->status === 'active') {
            $company->update(['status' => 'suspended']);
            $action = 'suspended';
        } elseif ($company->status === 'suspended') {
            $company->update(['status' => 'active']);
            $action = 'activated';
        } else {
            return back()->with('error', 'Cannot toggle status of a pending company.');
        }

        return back()->with('success', "Company {$company->name} has been successfully {$action}.");
    }

    /**
     * Manually Grant or Extend a License/Subscription
     */
    public function grantLicense(Request $request, \App\Models\Company $company)
    {
        $request->validate([
            'plan_id' => 'nullable|exists:subscription_plans,id',
            'duration' => 'required|in:week,month,year',
        ]);

        $now = now();
        $endDate = clone $now;

        switch ($request->duration) {
            case 'week':
                $endDate->addWeek();
                break;
            case 'month':
                $endDate->addMonth();
                break;
            case 'year':
                $endDate->addYear();
                break;
        }

        $company->update(['subscription_status' => 'active']);

        $planId = $request->plan_id ?? ($company->subscription->plan_id ?? 1);

        \App\Models\Subscription::updateOrCreate(
            ['company_id' => $company->id],
            [
                'plan_id' => $planId,
                'status' => 'active',
                'current_period_start' => $now,
                'current_period_end' => $endDate,
            ]
        );

        return back()->with('success', "License successfully applied to {$company->name} until {$endDate->format('M d, Y')}.");
    }

    /**
     * Extend Trial for 7 days
     */
    public function extendTrial(Request $request, \App\Models\Company $company)
    {
        $now = now();
        // If they still have time left, add 7 days to that. Otherwise, start 7 days from now.
        $endsAt = $company->trial_ends_at && $company->trial_ends_at > $now ? clone $company->trial_ends_at : clone $now;
        $endsAt->addDays(7);
        
        $company->update([
            'trial_ends_at' => $endsAt,
            'subscription_status' => 'trial'
        ]);

        if ($company->subscription) {
            $company->subscription->update([
                'status' => 'trial',
                'trial_ends_at' => $endsAt
            ]);
        } else {
            \App\Models\Subscription::create([
                'company_id' => $company->id,
                'plan_id' => 1,
                'status' => 'trial',
                'trial_starts_at' => $now,
                'trial_ends_at' => $endsAt,
                'trial_used' => true,
            ]);
        }

        return back()->with('success', "Trial extended for {$company->name} by 7 days. New expiration: {$endsAt->format('d M Y')}.");
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'plan_id', 'date_from', 'date_to']);
        $companies = $this->companyRepo->getPaginatedAdminList($filters, 15);
        $plans = SubscriptionPlan::where('is_active', true)->get();

        return view('admin.companies.index', compact('companies', 'filters', 'plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $company = $this->companyRepo->getAdminDetails($id);

        return view('admin.companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
