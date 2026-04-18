<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Notifications\Messages\WhatsAppMessage;

class AdminDocumentExpiryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param string $docType   e.g. "Mulkiya (Registration)"
     * @param string $subject   e.g. "Vehicle BUS-001" or "Driver Ahmed Ali"
     * @param int    $daysLeft  How many days until expiry (or 0 = already expired)
     */
    public function __construct(
        public string $docType,
        public string $subject,
        public int $daysLeft
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['mail'];

        // Only add WhatsApp if channel is available on notifiable
        if (
            method_exists($notifiable, 'routeNotificationFor') &&
            $notifiable->routeNotificationFor(WhatsAppChannel::class)
        ) {
            $channels[] = WhatsAppChannel::class;
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status = $this->daysLeft === 0
            ? '🔴 EXPIRED'
            : "⚠️ Expires in {$this->daysLeft} day(s)";

        return (new MailMessage)
            ->subject("📋 Document Expiry Alert: {$this->docType}")
            ->line("This is an automated alert from your Fleet Management System.")
            ->line("**{$this->subject}** — {$this->docType}")
            ->line("Status: **{$status}**")
            ->action('View Fleet Dashboard', url('/company/dashboard'))
            ->line('Please renew the document promptly to stay UAE compliant.');
    }

    public function toWhatsApp(object $notifiable): WhatsAppMessage
    {
        $status = $this->daysLeft === 0
            ? 'EXPIRED ❌'
            : "expires in {$this->daysLeft} day(s) ⚠️";

        $body = "📋 *Document Alert*\n\n"
            . "*{$this->subject}*\n"
            . "Document: {$this->docType}\n"
            . "Status: {$status}\n\n"
            . "Please renew immediately to remain UAE compliant.";

        return (new WhatsAppMessage)->content($body);
    }
}
