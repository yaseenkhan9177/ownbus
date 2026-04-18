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

        return 0;
    }
}
