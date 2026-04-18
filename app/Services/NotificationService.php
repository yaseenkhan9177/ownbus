<?php

namespace App\Services;

use App\Models\Fine;
use App\Models\Rental;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Unified Notification Handler.
     * Channels: Email, SMS, WhatsApp (Mocked).
     */
    public function sendFineNotification(Fine $fine)
    {
        $message = "New Fine Detected: {$fine->reference_number} for {$fine->amount} AED on Vehicle {$fine->bus->vehicle_number}.";

        // 1. Notify Branch/Admin
        $this->sendToChannel('email', 'admin@company.com', $message);

        // 2. Notify Customer if linked to Rental
        if ($fine->rental && $fine->rental->customer) {
            $customerMsg = "Dear Customer, a fine has been recorded on your rental {$fine->rental->contract_number}. Amount: {$fine->amount}.";
            $this->sendToChannel('email', $fine->rental->customer->email, $customerMsg);
            $this->sendToChannel('whatsapp', $fine->rental->customer->phone, $customerMsg);
        }
    }

    public function sendExpiryNotification($record, string $type, int $daysLeft, $users = null)
    {
        $entityName = $record->name ?? ($record->vehicle_number ?? 'Entity');
        $message = "Alert: {$type} for {$entityName} expires in {$daysLeft} days.";
        
        $this->createInAppNotification($users, 'expiry', "Document Expiry Alerts", $message, $record, $daysLeft <= 0 ? 'critical' : ($daysLeft <= 7 ? 'warning' : 'info'));
        
        // Keep external channels for critical
        if ($daysLeft <= 7) {
            $this->sendToChannel('whatsapp', 'admin_number', $message);
        }
    }

    /**
     * Create an in-app database notification for specified users.
     */
    public function createInAppNotification($users, string $type, string $title, string $message, $notifiable = null, string $urgency = 'info')
    {
        if (!$users) {
            // Default to all company admins in current tenant context
            $users = User::where('role', 'company_admin')->get();
        } elseif (!is_iterable($users)) {
            $users = [$users];
        }

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'notifiable_type' => $notifiable ? get_class($notifiable) : null,
                'notifiable_id' => $notifiable ? $notifiable->id : null,
                'urgency' => $urgency,
            ]);
        }
    }

    protected function sendToChannel(string $channel, ?string $recipient, string $message)
    {
        if (!$recipient) return;

        // Mock Implementation
        Log::info("Notification [{$channel}] to [{$recipient}]: {$message}");

        // Real implementation would use:
        // if ($channel == 'whatsapp') Twilio::send(...)
        // if ($channel == 'email') Mail::to(...)->send(...)
    }
}
