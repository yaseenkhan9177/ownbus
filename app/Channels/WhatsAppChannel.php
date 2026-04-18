<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Twilio\Rest\Client;
use App\Models\NotificationSetting;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $message = $notification->toWhatsApp($notifiable);

        if (!$message) {
            return;
        }

        // We assume $notifiable belongs to a specific company 
        // Or the notification itself knows the company
        $companyId = $notification->companyId ?? (isset($notifiable->company_id) ? $notifiable->company_id : null);

        if (!$companyId) {
            Log::warning('WhatsAppChannel: Missing company context for notification.');
            return;
        }

        $settings = NotificationSetting::where('company_id', $companyId)->first();

        if (!$settings || !$settings->is_active || empty($settings->twilio_sid) || empty($settings->twilio_token) || empty($settings->twilio_whatsapp_from)) {
            Log::info("WhatsAppChannel: Twilio credentials not configured or inactive for Company {$companyId}.");
            return; // Config not set
        }

        // Get the recipient's phone number
        $to = $notifiable->routeNotificationFor('whatsapp');

        if (!$to) {
            Log::warning("WhatsAppChannel: No WhatsApp number found for notifiable.");
            return;
        }

        try {
            $twilio = new Client($settings->twilio_sid, $settings->twilio_token);

            // Twilio requires numbers in e.164 format and prefixed with whatsapp:
            $fromNumber = "whatsapp:" . $settings->twilio_whatsapp_from;

            // Format to number
            if (!str_starts_with($to, 'whatsapp:')) {
                // Remove spaces, dashes, etc
                $cleanTo = preg_replace('/[^0-9+]/', '', $to);
                // Ensure starts with +, if it's 00 change to +
                if (str_starts_with($cleanTo, '00')) {
                    $cleanTo = '+' . substr($cleanTo, 2);
                }
                $toNumber = "whatsapp:" . $cleanTo;
            } else {
                $toNumber = $to;
            }

            $messageData = [
                'from' => $fromNumber,
                'body' => $message->content
            ];

            if ($message->mediaUrl) {
                $messageData['mediaUrl'] = [$message->mediaUrl];
            }

            $twilio->messages->create($toNumber, $messageData);

            Log::info("WhatsApp message sent successfully to {$toNumber}.");
        } catch (\Exception $e) {
            Log::error("WhatsAppChannel: Error sending message via Twilio.", [
                'error' => $e->getMessage(),
                'company' => $companyId,
                'to' => $to
            ]);
        }
    }
}
