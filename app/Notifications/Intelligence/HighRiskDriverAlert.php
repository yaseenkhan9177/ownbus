<?php

namespace App\Notifications\Intelligence;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HighRiskDriverAlert extends Notification
{
    use Queueable;

    protected \App\Models\Driver $driver;
    protected int $score;

    /**
     * Create a new notification instance.
     */
    public function __construct(\App\Models\Driver $driver, int $score)
    {
        $this->driver = $driver;
        $this->score = $score;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject('🚨 HIGH RISK DRIVER ALERT: ' . $this->driver->getNameAttribute())
            ->line('A driver has been flagged as HIGH RISK by the safety monitoring system.')
            ->line('Driver: ' . $this->driver->getNameAttribute())
            ->line('Safety Score: ' . $this->score . '/100')
            ->action('View Driver Profile', url('/portal/drivers/' . $this->driver->id))
            ->line('Please review driving logs and telematics alerts immediately.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'driver_id' => $this->driver->id,
            'driver_name' => $this->driver->getNameAttribute(),
            'score' => $this->score,
            'risk_level' => 'high',
            'message' => 'Driver ' . $this->driver->getNameAttribute() . ' is now High Risk (Score: ' . $this->score . ')',
        ];
    }
}
