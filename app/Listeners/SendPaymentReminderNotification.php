<?php

namespace App\Listeners;

use App\Events\PaymentReminderDue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPaymentReminderNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentReminderDue $event): void
    {
        $rental = \App\Models\Rental::with('customer')->find($event->rentalId);
        if ($rental && $rental->customer) {
            $email = $rental->customer->email;
            $phone = $rental->customer->phone ?? $rental->customer->phone_number;

            \Illuminate\Support\Facades\Notification::route('mail', $email)
                ->route('whatsapp', $phone)
                ->notify(new \App\Notifications\CustomerInvoiceNotification($event->rentalId, $event->companyId));
        }
    }
}
