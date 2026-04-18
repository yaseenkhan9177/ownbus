<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'version' => 1,
                'price_monthly' => 99.00,
                'price_yearly' => 990.00, // 2 months free
                'features' => [
                    'max_vehicles' => 10,
                    'max_users' => 5,
                    'max_branches' => 1,
                    'has_bi' => false,
                    'has_api' => false,
                ],
                'is_active' => true,
                'trial_days' => 14,
                'grace_period_days' => 7,
            ],
            [
                'name' => 'Growth',
                'slug' => 'growth',
                'version' => 1,
                'price_monthly' => 299.00,
                'price_yearly' => 2990.00,
                'features' => [
                    'max_vehicles' => 50,
                    'max_users' => 20,
                    'max_branches' => 5,
                    'has_bi' => true,
                    'has_api' => true,
                ],
                'is_active' => true,
                'trial_days' => 14,
                'grace_period_days' => 7,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'version' => 1,
                'price_monthly' => 999.00,
                'price_yearly' => 9990.00,
                'features' => [
                    'max_vehicles' => 999,
                    'max_users' => 100,
                    'max_branches' => 999,
                    'has_bi' => true,
                    'has_api' => true,
                ],
                'is_active' => true,
                'trial_days' => 14,
                'grace_period_days' => 7,
            ],
            [
                'name' => 'Legacy (Complimentary)',
                'slug' => 'legacy',
                'version' => 1,
                'price_monthly' => 0.00,
                'price_yearly' => 0.00,
                'features' => [
                    'max_vehicles' => 999,
                    'max_users' => 100,
                    'max_branches' => 999,
                    'has_bi' => true,
                    'has_api' => true,
                ],
                'is_active' => true,
                'trial_days' => 9999, // Effectively unlimited
                'grace_period_days' => 9999,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug'], 'version' => $plan['version']],
                $plan
            );
        }
    }
}
