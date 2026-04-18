<?php

namespace App\Notifications;

use App\Models\Rental;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    public $rental;
    public $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Rental $rental, $reason = null)
    {
        $this->rental = $rental;
        $this->reason = $reason;
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
        $message = (new MailMessage)
            ->subject('Booking Cancelled - Reference #' . $this->rental->id)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your booking has been cancelled.')
            ->line('**Cancelled Booking Details:**')
            ->line('Vehicle: ' . $this->rental->vehicle->name)
            ->line('Pickup Date: ' . $this->rental->start_datetime->format('M d, Y H:i'))
            ->line('Booking Amount: AED ' . number_format($this->rental->final_amount, 2));

        if ($this->reason) {
            $message->line('')->line('**Cancellation Reason:** ' . $this->reason);
        }

        if ($this->rental->payment_status === 'paid') {
            $message->line('')
                ->line('**Refund Information:**')
                ->line('A refund of AED ' . number_format($this->rental->final_amount, 2) . ' will be processed to your original payment method within 5-7 business days.');
        }

        $message->line('If you did not request this cancellation or have any questions, please contact us immediately.')
            ->salutation('Best regards, ' . config('app.name'));

        return $message;
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
            'cancellation_reason' => $this->reason,
            'refund_amount' => $this->rental->payment_status === 'paid' ? $this->rental->final_amount : 0,
            'message' => 'Your booking for ' . $this->rental->vehicle->name . ' has been cancelled.',
            'action_url' => route('portal.dashboard'),
        ];
    }
}
