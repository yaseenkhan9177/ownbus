<?php

namespace App\Services;

use App\Models\Company;
use App\Models\DynamicPricingRule;
use App\Models\Vehicle;
use App\Models\Rental;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Centralized caching service for enterprise-scale performance
 * 
 * Handles caching for:
 * - Pricing rules (reduce DB hits on rate calculations)
 * - Fleet availability (fast vehicle status checks)
 * - Dashboard KPIs (instant metrics loading)
 */
class CacheService
{
    // Cache TTL constants (in seconds)
    const TTL_SHORT = 300;      // 5 minutes - for frequently changing data
    const TTL_MEDIUM = 1800;    // 30 minutes - for moderately stable data
    const TTL_LONG = 3600;      // 1 hour - for rarely changing data
    const TTL_VERY_LONG = 86400; // 24 hours - for very stable data

    /**
     * Cache pricing rules for a company
     * TTL: LONG (1 hour) - pricing rules don't change frequently
     */
    public function cachePricingRules(int $companyId): array
    {
        $cacheKey = "pricing_rules:company:{$companyId}";

        return Cache::remember($cacheKey, self::TTL_LONG, function () {
            return DynamicPricingRule::query()
                ->where('is_active', true)
                ->orderBy('priority', 'desc')
                ->get()
                ->toArray();
        });
    }

    /**
     * Cache fleet availability for a company
     * TTL: SHORT (5 minutes) - availability changes frequently
     */
    public function cacheFleetAvailability(int $companyId, ?string $vehicleType = null): array
    {
        $cacheKey = $vehicleType
            ? "fleet_availability:company:{$companyId}:type:{$vehicleType}"
            : "fleet_availability:company:{$companyId}";

        return Cache::remember($cacheKey, self::TTL_SHORT, function () use ($companyId, $vehicleType) {
            $query = Vehicle::where('status', 'active');

            if ($vehicleType) {
                $query->where('type', $vehicleType);
            }

            return $query->select('id', 'vehicle_number', 'name', 'type', 'status', 'daily_rate')
                ->get()
                ->toArray();
        });
    }

    /**
     * Cache dashboard KPIs for a company
     * TTL: MEDIUM (30 minutes) - balance between freshness and performance
     */
    public function cacheDashboardKPIs(int $companyId): array
    {
        $cacheKey = "dashboard_kpis:company:{$companyId}";

        return Cache::remember($cacheKey, self::TTL_MEDIUM, function () {
            $today = Carbon::today();
            $thisMonth = Carbon::now()->startOfMonth();

            return [
                'total_vehicles' => Vehicle::count(),
                'total_rentals_today' => Rental::whereDate('start_date', $today)->count(),
                'total_rentals_month' => Rental::whereDate('start_date', '>=', $thisMonth)->count(),
                'active_rentals' => Rental::whereIn('status', ['confirmed', 'in_progress'])->count(),
                'revenue_today' => Rental::whereDate('start_date', $today)
                    ->where('payment_status', 'paid')
                    ->sum('final_amount'),
                'revenue_month' => Rental::whereDate('start_date', '>=', $thisMonth)
                    ->where('payment_status', 'paid')
                    ->sum('final_amount'),
                'fleet_utilization' => Vehicle::count() > 0
                    ? round((Rental::whereIn('status', ['confirmed', 'in_progress'])->count() / Vehicle::count()) * 100)
                    : 0,
                'receivables' => Rental::where('payment_status', '!=', 'paid')
                    ->sum('final_amount'),
                'cached_at' => now()->toIso8601String(),
            ];
        });
    }

    /**
     * Cache active subscription for a company
     * TTL: MEDIUM (30 minutes) - subscriptions don't change often
     */
    public function cacheActiveSubscription(int $companyId): ?array
    {
        $cacheKey = "subscription:company:{$companyId}";

        return Cache::remember($cacheKey, self::TTL_MEDIUM, function () use ($companyId) {
            $subscription = DB::table('subscriptions')
                ->where('company_id', $companyId)
                ->whereIn('status', ['trialing', 'active', 'grace'])
                ->first();

            return $subscription ? (array) $subscription : null;
        });
    }

    /**
     * Cache company quota status
     * TTL: SHORT (5 minutes) - quota usage changes frequently
     */
    public function cacheQuotaStatus(int $companyId): array
    {
        $cacheKey = "quota_status:company:{$companyId}";

        return Cache::remember($cacheKey, self::TTL_SHORT, function () use ($companyId) {
            $subscription = $this->cacheActiveSubscription($companyId);

            if (!$subscription) {
                return ['error' => 'No active subscription'];
            }

            $plan = DB::table('subscription_plans')
                ->where('id', $subscription['plan_id'])
                ->first();

            if (!$plan) {
                return ['error' => 'Plan not found'];
            }

            $features = json_decode($plan->features, true);

            return [
                'vehicles' => [
                    'current' => Vehicle::count(),
                    'limit' => $features['max_vehicles'] ?? 0,
                    'percentage' => $features['max_vehicles'] > 0
                        ? round((Vehicle::count() / $features['max_vehicles']) * 100)
                        : 0,
                ],
                'users' => [
                    'current' => DB::table('users')->where('company_id', $companyId)->count(),
                    'limit' => $features['max_users'] ?? 0,
                ],
            ];
        });
    }

    /**
     * Invalidate pricing rules cache for a company
     */
    public function invalidatePricingRules(int $companyId): void
    {
        Cache::forget("pricing_rules:company:{$companyId}");
    }

    /**
     * Invalidate fleet availability cache for a company
     */
    public function invalidateFleetAvailability(int $companyId, ?string $vehicleType = null): void
    {
        if ($vehicleType) {
            Cache::forget("fleet_availability:company:{$companyId}:type:{$vehicleType}");
        } else {
            // Invalidate all vehicle types
            Cache::forget("fleet_availability:company:{$companyId}");
        }
    }

    /**
     * Invalidate dashboard KPIs cache for a company
     */
    public function invalidateDashboardKPIs(int $companyId): void
    {
        Cache::forget("dashboard_kpis:company:{$companyId}");
    }

    /**
     * Invalidate subscription cache for a company
     */
    public function invalidateSubscription(int $companyId): void
    {
        Cache::forget("subscription:company:{$companyId}");
        Cache::forget("quota_status:company:{$companyId}");
    }

    /**
     * Invalidate all caches for a company
     */
    public function invalidateAllForCompany(int $companyId): void
    {
        $this->invalidatePricingRules($companyId);
        $this->invalidateFleetAvailability($companyId);
        $this->invalidateDashboardKPIs($companyId);
        $this->invalidateSubscription($companyId);
    }

    /**
     * Warm cache for a company (pre-load all caches)
     * Useful for new companies or after cache clear
     */
    public function warmCacheForCompany(int $companyId): void
    {
        $this->cachePricingRules($companyId);
        $this->cacheFleetAvailability($companyId);
        $this->cacheDashboardKPIs($companyId);
        $this->cacheActiveSubscription($companyId);
        $this->cacheQuotaStatus($companyId);
    }

    /**
     * Get cache statistics for monitoring
     */
    public function getCacheStats(): array
    {
        return [
            'store' => config('cache.default'),
            'prefix' => config('cache.prefix'),
            'ttl_short' => self::TTL_SHORT,
            'ttl_medium' => self::TTL_MEDIUM,
            'ttl_long' => self::TTL_LONG,
        ];
    }

    /**
     * Remember value in cache with optional tags support.
     * Falls back to standard cache if driver doesn't support tags.
     */
    public static function rememberTagged(array $tags, string $key, int $ttl, \Closure $callback)
    {
        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            return Cache::tags($tags)->remember($key, $ttl, $callback);
        }

        return Cache::remember($key, $ttl, $callback);
    }
}
