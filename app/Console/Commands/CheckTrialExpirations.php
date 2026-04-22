<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckTrialExpirations extends Command
{
    protected $signature = 'subscriptions:check-trials';
    protected $description = 'Check for expiring trials and send notifications';

    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        parent::__construct();
        $this->subscriptionService = $subscriptionService;
    }

    public function handle()
    {
        $this->info('Checking trial expirations...');

        // Find trials expiring in 3 days
        $expiringIn3Days = Subscription::where('status', 'trialing')
            ->whereBetween('trial_ends_at', [
                Carbon::now()->addDays(2)->startOfDay(),
                Carbon::now()->addDays(3)->endOfDay(),
            ])
            ->with('company.users')
            ->get();

        foreach ($expiringIn3Days as $subscription) {
            $this->warn("Trial expiring in 3 days: {$subscription->company->name}");
            // TODO: Send email notification
            Log::info('Trial expiring soon', [
                'company_id' => $subscription->company_id,
                'expires_at' => $subscription->trial_ends_at,
            ]);

            $company = $subscription->company;
            $settings = $company->companyNotificationSettings;
            if ($settings && $settings->whatsapp_enabled && $settings->notify_subscription && $settings->whatsapp_number) {
                \App\Jobs\SendWhatsAppJob::dispatch(
                    $settings->whatsapp_number,
                    'subscription_expiring',
                    [
                        'company_name' => $company->name,
                        'plan_name' => $subscription->plan->name ?? 'Trial',
                        'days' => 3,
                        'expiry_date' => \Carbon\Carbon::parse($subscription->trial_ends_at)->format('d M Y'),
                        'amount' => $subscription->plan->price_yearly ?? 0,
                    ]
                );
            }
        }

        // Find trials expiring today
        $expiringToday = Subscription::where('status', 'trialing')
            ->whereDate('trial_ends_at', Carbon::today())
            ->with('company')
            ->get();

        foreach ($expiringToday as $subscription) {
            $this->error("Trial expiring TODAY: {$subscription->company->name}");
            // TODO: Send urgent notification
        }

        // Find expired trials (convert to past_due)
        $expired = Subscription::where('status', 'trialing')
            ->where('trial_ends_at', '<', Carbon::now())
            ->get();

        foreach ($expired as $subscription) {
            $subscription->update(['status' => 'past_due']);
            $this->subscriptionService->handleGracePeriod($subscription);
            $this->info("Converted trial to grace period: {$subscription->company->name}");
        }

        $this->info('Trial check complete.');
        $this->info("Expiring in 3 days: {$expiringIn3Days->count()}");
        $this->info("Expiring today: {$expiringToday->count()}");
        $this->info("Expired (now in grace): {$expired->count()}");

        return 0;
    }
}
