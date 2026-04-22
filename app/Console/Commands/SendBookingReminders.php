<?php

namespace App\Console\Commands;

use App\Models\Rental;
use App\Notifications\BookingReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendBookingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications to customers 24 hours before their booking';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get bookings starting in 24 hours (give or take 1 hour buffer)
        $tomorrow = Carbon::now()->addDay();
        $reminderStart = $tomorrow->copy()->subHour();
        $reminderEnd = $tomorrow->copy()->addHour();

        $upcomingRentals = Rental::with(['customer', 'vehicle'])
            ->where('status', 'confirmed')
            ->where('payment_status', 'paid')
            ->whereBetween('start_datetime', [$reminderStart, $reminderEnd])
            ->whereDoesntHave('customer.notifications', function ($query) {
                // Don't send duplicate reminders
                $query->where('type', BookingReminder::class)
                    ->where('created_at', '>=', Carbon::now()->subDay());
            })
            ->get();

        $count = 0;

        foreach ($upcomingRentals as $rental) {
            try {
                $rental->customer->notify(new BookingReminder($rental));
                $count++;
                $this->info("Reminder sent for booking #{$rental->id} to {$rental->customer->email}");
            } catch (\Exception $e) {
                $this->error("Failed to send reminder for booking #{$rental->id}: {$e->getMessage()}");
            }
        }

        $this->info("Sent {$count} booking reminders.");

        // --- Rental Expiring Alerts ---
        $expiringRentals = Rental::with(['customer', 'vehicle'])
            ->whereIn('status', ['active', 'confirmed'])
            ->whereBetween('end_date', [$reminderStart, $reminderEnd])
            ->get();
            
        $expCount = 0;
        
        $company = \App\Models\Company::where('database_name', config('database.connections.tenant.database'))->first();
        if ($company) {
            $settings = $company->companyNotificationSettings;
            if ($settings && $settings->whatsapp_enabled && $settings->notify_rental_expiring && $settings->whatsapp_number) {
                foreach ($expiringRentals as $rental) {
                    \App\Jobs\SendWhatsAppJob::dispatch(
                        $settings->whatsapp_number,
                        'rental_expiring',
                        [
                            'company_name' => $company->name,
                            'customer_name' => $rental->customer->name ?? $rental->customer->company_name,
                            'vehicle_name' => $rental->vehicle ? $rental->vehicle->vehicle_number : 'N/A',
                            'days' => 1,
                            'expiry_date' => \Carbon\Carbon::parse($rental->end_date)->format('d M Y'),
                        ]
                    );
                    $expCount++;
                }
            }
        }

        $this->info("Sent {$expCount} rental expiring alerts.");

        return 0;
    }
}
