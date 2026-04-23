<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
use App\Jobs\SendWhatsAppJob;

class CheckTrials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trials:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expiring trials and send WhatsApp reminders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expiring trials...');

        // 3 days left
        $expiringIn3Days = Company::where('subscription_status', 'trial')
            ->whereDate('trial_ends_at', now()->addDays(3)->toDateString())
            ->get();

        foreach ($expiringIn3Days as $company) {
            $ownerEmail = env('OWNER_EMAIL', 'ykcaptain2223@gmail.com');
            $ownerPhone = env('OWNER_WHATSAPP', '+923409172223');
            $date = $company->trial_ends_at->format('d M Y');
            
            $message = "⚠️ *Trial Expiring Soon!*\nHi {$company->name},\nYour OwnBus 7-day FREE TRIAL expires in *3 days* on {$date}.\n\nDon't lose your data! Contact us to upgrade:\n📧 {$ownerEmail}\n📱 {$ownerPhone}\nownbus.software";
            
            if ($company->phone) {
                SendWhatsAppJob::dispatch($company->phone, null, [], $message);
                $this->info("Sent 3-day reminder to {$company->name}");
            }
        }

        // 1 day left
        $expiringIn1Day = Company::where('subscription_status', 'trial')
            ->whereDate('trial_ends_at', now()->addDays(1)->toDateString())
            ->get();

        foreach ($expiringIn1Day as $company) {
            $ownerEmail = env('OWNER_EMAIL', 'ykcaptain2223@gmail.com');
            $ownerPhone = env('OWNER_WHATSAPP', '+923409172223');
            
            $message = "🚨 *Final Reminder!*\nHi {$company->name},\nYour OwnBus trial ends *TOMORROW*.\n\nTo keep managing your fleet without interruption, please contact us today to activate your subscription:\n📧 {$ownerEmail}\n📱 {$ownerPhone}\nownbus.software";
            
            if ($company->phone) {
                SendWhatsAppJob::dispatch($company->phone, null, [], $message);
                $this->info("Sent 1-day reminder to {$company->name}");
            }
        }

        // Expired today
        $expiredToday = Company::where('subscription_status', 'trial')
            ->whereDate('trial_ends_at', '<', now()->toDateString())
            ->get();

        foreach ($expiredToday as $company) {
            $company->update(['subscription_status' => 'expired']);
            if ($company->subscription) {
                $company->subscription->update(['status' => 'expired']);
            }
            
            $ownerEmail = env('OWNER_EMAIL', 'ykcaptain2223@gmail.com');
            $ownerPhone = env('OWNER_WHATSAPP', '+923409172223');
            
            $message = "❌ *Trial Expired*\nHi {$company->name},\nYour OwnBus free trial has ended. Access to your account is now restricted.\n\nWe'd love to have you back! Please contact us to subscribe:\n📧 {$ownerEmail}\n📱 {$ownerPhone}\nownbus.software";
            
            if ($company->phone) {
                SendWhatsAppJob::dispatch($company->phone, null, [], $message);
                $this->info("Sent expiration notice to {$company->name} and marked as expired");
            }
        }

        $this->info('Trial check completed.');
    }
}
