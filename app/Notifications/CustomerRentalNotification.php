<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Notifications\Messages\WhatsAppMessage;

class CustomerRentalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $rentalId;
    public $companyId;

    public function __construct($rentalId, $companyId)
    {
        $this->rentalId = $rentalId;
        $this->companyId = $companyId;
    }

    public function via(object $notifiable): array
    {
        return ['mail', WhatsAppChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $rental = \App\Models\Rental::with(['customer', 'vehicle', 'company'])->find($this->rentalId);

        return (new MailMessage)
            ->subject('Booking Confirmation - ' . $rental->company->name)
            ->greeting('Hello ' . $rental->customer->name . ',')
            ->line('Your bus rental has been officially confirmed.')
            ->line('Contract Number: ' . $rental->contract_number)
            ->line('Vehicle: ' . $rental->vehicle->name . ' (' . $rental->vehicle->vehicle_number . ')')
            ->line('Start Date: ' . $rental->start_date->format('d M Y'))
            ->line('Thank you for choosing us!');
    }

    public function toWhatsApp(object $notifiable)
    {
        $rental = \App\Models\Rental::with(['customer', 'vehicle', 'company'])->find($this->rentalId);

        $text = "🚗 *Booking Confirmation*\n\n";
        $text .= "Hello {$rental->customer->name},\n";
        $text .= "Your booking with *{$rental->company->name}* is confirmed.\n\n";
        $text .= "Contract No: {$rental->contract_number}\n";
        $text .= "Vehicle: {$rental->vehicle->name} ({$rental->vehicle->vehicle_number})\n";
        $text .= "Start Date: {$rental->start_date->format('d M Y')}\n\n";
        $text .= "Thank you!";

        return (new WhatsAppMessage)->content($text);
    }
}
