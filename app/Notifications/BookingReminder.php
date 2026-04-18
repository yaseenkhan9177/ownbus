<?php

namespace App\Notifications;

use App\Models\Rental;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingReminder extends Notification implements ShouldQueue
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
        $bookingLink = route('portal.bookings.show', $this->rental);

        return (new MailMessage)
            ->subject('Reminder: Your Booking Tomorrow - #' . $this->rental->id)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('This is a friendly reminder that your bus rental is scheduled for tomorrow.')
            ->line('**Booking Details:**')
            ->line('Vehicle: ' . $this->rental->vehicle->name)
            ->line('Pickup: ' . $this->rental->start_datetime->format('M d, Y H:i'))
            ->line('Location: ' . $this->rental->pickup_location)
            ->line('')
            ->line('**Important Reminders:**')
            ->line('- Please arrive 10 minutes before scheduled pickup time')
            ->line('- Bring a valid ID and driver\'s license (if self-driving)')
            ->line('- The vehicle will accommodate up to ' . $this->rental->vehicle->seating_capacity . ' passengers')
            ->action('View Booking', $bookingLink)
            ->line('If you have any questions or need to make changes, please contact us immediately.')
            ->salutation('Safe travels, ' . config('app.name'));
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
            'message' => 'Reminder: Your ' . $this->rental->vehicle->name . ' rental is tomorrow!',
            'action_url' => route('portal.bookings.show', $this->rental),
        ];
    }
}
