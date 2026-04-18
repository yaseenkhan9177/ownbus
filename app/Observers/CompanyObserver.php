<?php

namespace App\Observers;

use App\Models\Company;
use Database\Seeders\ChartOfAccountsSeeder;

class CompanyObserver
{
    /**
     * Handle the Company "created" event.
     */
    public function created(Company $company): void
    {
        // Guard: only seed tenant data when the tenant DB has already been provisioned.
        // During registration Phase 1 (central DB transaction), database_name may not be
        // set yet or the tenant DB may not exist. Seeding happens explicitly in Phase 2.
        if (empty($company->database_name)) {
            return;
        }

        // Defer seeding to allow the observer to finish without blocking the request.
        // The RegistrationController handles explicit seeding via DB::connection('tenant').
    }

    /**
     * Handle the Company "updated" event.
     */
    public function updated(Company $company): void
    {
        //
    }

    /**
     * Handle the Company "deleted" event.
     */
    public function deleted(Company $company): void
    {
        //
    }

    /**
     * Handle the Company "restored" event.
     */
    public function restored(Company $company): void
    {
        //
    }

    /**
     * Handle the Company "force deleted" event.
     */
    public function forceDeleted(Company $company): void
    {
        //
    }
}
