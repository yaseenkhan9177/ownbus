<?php

namespace App\Notifications;

use App\Models\Rental;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public $rental;

    /**
     * Create a new notification instance.
     */
    public function __construct(Rental $rental)
    {
        $this->rental = $rental;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['mail', 'database'];

        // Check user preferences for SMS
        $preferences = $notifiable->notification_preferences ?? ['email' => true, 'sms' => false];

        if (isset($preferences['sms']) && $preferences['sms'] && $notifiable->phone) {
            // $channels[] = 'twilio';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $invoiceLink = route('portal.rentals.invoice', $this->rental);

        return (new MailMessage)
            ->subject('Payment Confirmed - Booking #' . $this->rental->id)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('We have received your payment! Your booking is now confirmed.')
            ->line('**Booking Details:**')
            ->line('Vehicle: ' . $this->rental->vehicle->name)
            ->line('Pickup: ' . $this->rental->start_datetime->format('M d, Y H:i'))
            ->line('Location: ' . $this->rental->pickup_location)
            ->line('Amount Paid: AED ' . number_format($this->rental->final_amount, 2))
            ->action('View Invoice', $invoiceLink)
            ->line('We will send you a reminder 24 hours before your pickup time.')
            ->line('Thank you for choosing ' . config('app.name') . '!')
            ->salutation('Best regards, ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification (for database).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'rental_id' => $this->rental->id,
            'vehicle_name' => $this->rental->vehicle->name,
            'amount_paid' => $this->rental->final_amount,
            'message' => 'Payment received for ' . $this->rental->vehicle->name . ' booking.',
            'action_url' => route('portal.bookings.show', $this->rental),
        ];
    }
}
