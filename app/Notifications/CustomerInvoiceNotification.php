<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Notifications\Messages\WhatsAppMessage;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerInvoiceNotification extends Notification implements ShouldQueue
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

        $pdf = Pdf::loadView('exports.invoices', ['invoices' => collect([$rental]), 'company' => $rental->company]);

        return (new MailMessage)
            ->from(system_setting('support_email', 'no-reply@aetheired.com'), system_setting('app_name', $rental->company->name))
            ->subject(system_setting('app_name', $rental->company->name) . ' - Invoice Attached')
            ->greeting('Hello ' . $rental->customer->name . ',')
            ->line('Thank you for renting with ' . $rental->company->name . '.')
            ->line('Your invoice for Contract #' . $rental->contract_number . ' is attached to this email.')
            ->line('Total Amount: AED ' . number_format($rental->final_amount, 2))
            ->attachData($pdf->output(), 'Invoice_' . $rental->contract_number . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }

    public function toWhatsApp(object $notifiable)
    {
        $rental = \App\Models\Rental::with(['customer', 'vehicle', 'company'])->find($this->rentalId);

        $text = "Hello {$rental->customer->name},\n\n";
        $text .= "This is a message from *{$rental->company->name}*.\n";
        $text .= "Your invoice for Contract #{$rental->contract_number} has been generated.\n";
        $text .= "Total Amount: AED " . number_format($rental->final_amount, 2) . "\n\n";
        $text .= "Please check your email for the attached PDF invoice. Thank you.";

        return (new WhatsAppMessage)->content($text);
    }
}
