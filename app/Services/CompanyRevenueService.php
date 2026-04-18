<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Rental;
use App\Services\CacheService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CompanyRevenueService
{
    /**
     * Get revenue stats for a specific company (Daily, Weekly, Monthly etc).
     */
    public function getRevenueStats(Company $company): array
    {
        return CacheService::rememberTagged(["financials"], "revenue", CacheService::TTL_MEDIUM, function () use ($company) {
            $now = Carbon::now();

            $today = Rental::whereDate('created_at', $now->today())
                ->sum('final_amount');

            $week = Rental::whereBetween('created_at', [$now->startOfWeek()->format('Y-m-d H:i:s'), $now->endOfWeek()->format('Y-m-d H:i:s')])
                ->sum('final_amount');

            $month = Rental::whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->sum('final_amount');

            return [
                'today' => (float)$today,
                'this_week' => (float)$week,
                'this_month' => (float)$month,
            ];
        });
    }

    /**
     * Get monthly revenue trend for charts.
     */
    public function getMonthlyRevenueTrend(Company $company, int $months = 6): array
    {
        return CacheService::rememberTagged(["financials"], "rev_trend_monthly:{$months}", CacheService::TTL_LONG, function () use ($company, $months) {
            $months_list = [];
            $revenue = [];
            $expenses = [];

            for ($i = $months - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $months_list[] = $date->format('M');

                $rev = Rental::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('final_amount');
                $revenue[] = (float)$rev;

                $exp = \App\Models\Expense::whereYear('expense_date', $date->year)
                    ->whereMonth('expense_date', $date->month)
                    ->sum('total_amount');
                $expenses[] = (float)$exp;
            }

            return [
                'labels'   => $months_list,
                'revenue'  => $revenue,
                'expenses' => $expenses,
            ];
        });
    }

    /**
     * Get daily revenue trend for the current month.
     */
    public function getDailyRevenueTrend(Company $company): array
    {
        return CacheService::rememberTagged(["financials"], "rev_trend_daily", CacheService::TTL_MEDIUM, function () use ($company) {
            $days = [];
            $revenue = [];
            $expenses = [];

            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now();

            $period = \Carbon\CarbonPeriod::create($start, $end);

            foreach ($period as $date) {
                $days[] = $date->format('d M');

                $rev = Rental::whereDate('created_at', $date->toDateString())
                    ->sum('final_amount');
                $revenue[] = (float)$rev;

                $exp = \App\Models\Expense::whereDate('expense_date', $date->toDateString())
                    ->sum('total_amount');
                $expenses[] = (float)$exp;
            }

            return [
                'labels'   => $days,
                'revenue'  => $revenue,
                'expenses' => $expenses,
            ];
        });
    }
}
