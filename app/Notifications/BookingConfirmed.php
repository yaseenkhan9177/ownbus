<?php

namespace App\Notifications;

use App\Models\Rental;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmed extends Notification implements ShouldQueue
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
            // SMS will be added when Twilio is configured
            // $channels[] = 'twilio';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $paymentLink = route('portal.payments.show', $this->rental);

        return (new MailMessage)
            ->subject('Booking Confirmed - Reference #' . $this->rental->id)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your booking has been created successfully.')
            ->line('**Booking Details:**')
            ->line('Vehicle: ' . $this->rental->vehicle->name)
            ->line('Pickup: ' . $this->rental->start_datetime->format('M d, Y H:i'))
            ->line('Return: ' . $this->rental->end_datetime->format('M d, Y H:i'))
            ->line('Total Amount: AED ' . number_format($this->rental->final_amount, 2))
            ->action('Complete Payment', $paymentLink)
            ->line('Please complete your payment to confirm your booking.')
            ->line('If you have any questions, feel free to contact us.')
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
            'pickup_date' => $this->rental->start_datetime->format('M d, Y H:i'),
            'total_amount' => $this->rental->final_amount,
            'message' => 'Your booking for ' . $this->rental->vehicle->name . ' has been confirmed.',
            'action_url' => route('portal.bookings.show', $this->rental),
        ];
    }
}
