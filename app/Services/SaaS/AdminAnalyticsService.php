<?php

namespace App\Services\SaaS;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AdminAnalyticsService
{
    /**
     * Get primary platform operational statistics.
     * NOTE: vehicles & rentals are in per-tenant DBs — only central DB stats available here.
     */
    public function getPlatformStats(): array
    {
        return Cache::remember('admin_analytics_platform_stats', 3600, function () {

            $totalActiveCompanies = Company::withoutGlobalScopes()->where('status', 'active')->count();

            // total_vehicles is declared at registration time on the company record
            $totalVehicles = Company::withoutGlobalScopes()->sum('total_vehicles');

            // Drivers in central DB are users with role = driver
            $totalDrivers = User::withoutGlobalScopes()->where('role', 'driver')->count();

            $avgVehiclesPerCompany = $totalActiveCompanies > 0
                ? round($totalVehicles / $totalActiveCompanies, 1)
                : 0;

            return [
                'total_vehicles'           => (int) $totalVehicles,
                'total_drivers'            => $totalDrivers,
                'rentals_this_month'       => 0, // Tenant DB — not queryable centrally
                'avg_vehicles_per_company' => $avgVehiclesPerCompany,
            ];
        });
    }

    /**
     * Company registration growth over the last 12 months.
     * Replaces cross-DB rental trend since rentals are in each tenant's DB.
     */
    public function getRentalGrowth(): array
    {
        return Cache::remember('admin_analytics_rental_growth', 3600, function () {
            $trend = [];

            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);

                $newCompanies = Company::withoutGlobalScopes()
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();

                $trend[] = [
                    'month'   => $date->format('M Y'),
                    'rentals' => $newCompanies, // key kept for chart compatibility
                ];
            }

            return $trend;
        });
    }

    /**
     * Top companies by user count (central DB only).
     */
    public function getTopCompanies(int $limit = 10)
    {
        return Cache::remember('admin_analytics_top_companies', 3600, function () use ($limit) {

            return Company::withoutGlobalScopes()
                ->select('companies.*')
                ->where('status', 'active')
                ->withCount(['users'])
                ->with('subscription.plan')
                ->orderByDesc('users_count')
                ->limit($limit)
                ->get()
                ->map(function ($company) {
                    // Tenant-only counts: use declared value or 0
                    $company->rentals_count  = 0;
                    $company->vehicles_count = $company->total_vehicles ?? 0;
                    $company->drivers_count  = 0;
                    $company->growth_percent = 0;
                    return $company;
                });
        });
    }
}
