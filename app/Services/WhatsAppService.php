<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $instanceId;
    private string $token;
    private string $baseUrl;

    public function __construct()
    {
        $this->instanceId = config('services.ultramsg.instance_id') ?? env('ULTRAMSG_INSTANCE_ID', '');
        $this->token = config('services.ultramsg.token') ?? env('ULTRAMSG_TOKEN', '');
        $this->baseUrl = "https://api.ultramsg.com/{$this->instanceId}";
    }

    public function send(string $phone, string $message): bool
    {
        if (!env('WHATSAPP_ENABLED', true)) {
            Log::info("WhatsApp disabled. Would have sent to {$phone}: {$message}");
            return true;
        }

        try {
            // Format UAE phone number
            $phone = $this->formatPhone($phone);
            
            $response = Http::post("{$this->baseUrl}/messages/chat", [
                'token' => $this->token,
                'to' => $phone,
                'body' => $message,
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp sent to {$phone}");
                return true;
            }

            Log::error("WhatsApp failed: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("WhatsApp error: " . $e->getMessage());
            return false;
        }
    }

    private function formatPhone(string $phone): string
    {
        // Remove spaces, dashes, brackets
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Add UAE country code if missing
        if (str_starts_with($phone, '05')) {
            $phone = '+971' . substr($phone, 1);
        } elseif (str_starts_with($phone, '5') && strlen($phone) == 9) {
            $phone = '+971' . $phone;
        } elseif (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }
        
        return $phone;
    }

    // Send with template
    public function sendTemplate(string $phone, string $template, array $vars = []): bool
    {
        $message = $this->buildTemplate($template, $vars);
        return $this->send($phone, $message);
    }

    private function buildTemplate(string $template, array $vars): string
    {
        $templates = [
            
            'new_fine' => "🚨 *NEW FINE ALERT*\n\n" .
                "Dear {company_name},\n\n" .
                "A new traffic fine has been recorded:\n\n" .
                "🚌 *Vehicle:* {vehicle_name}\n" .
                "🔢 *Plate:* {plate_number}\n" .
                "📋 *Fine #:* {fine_number}\n" .
                "💰 *Amount:* AED {amount}\n" .
                "⚠️ *Violation:* {violation}\n" .
                "🏛️ *Authority:* {authority}\n" .
                "📅 *Date:* {date}\n\n" .
                "Login to manage: ownbus.software\n\n" .
                "_OwnBus Fleet Management_",

            'rental_created' => "✅ *NEW RENTAL CREATED*\n\n" .
                "Dear {company_name},\n\n" .
                "A new rental has been created:\n\n" .
                "👤 *Customer:* {customer_name}\n" .
                "🚌 *Vehicle:* {vehicle_name}\n" .
                "📅 *Start:* {start_date}\n" .
                "📅 *End:* {end_date}\n" .
                "💰 *Amount:* AED {amount}\n\n" .
                "Login to view: ownbus.software\n\n" .
                "_OwnBus Fleet Management_",

            'rental_expiring' => "⏰ *RENTAL EXPIRING SOON*\n\n" .
                "Dear {company_name},\n\n" .
                "This rental expires in {days} days:\n\n" .
                "👤 *Customer:* {customer_name}\n" .
                "🚌 *Vehicle:* {vehicle_name}\n" .
                "📅 *Expiry:* {end_date}\n" .
                "💰 *Amount Due:* AED {amount}\n\n" .
                "Login to renew: ownbus.software\n\n" .
                "_OwnBus Fleet Management_",

            'payment_received' => "💰 *PAYMENT RECEIVED*\n\n" .
                "Dear {company_name},\n\n" .
                "Payment has been recorded:\n\n" .
                "👤 *Customer:* {customer_name}\n" .
                "💵 *Amount:* AED {amount}\n" .
                "📋 *Reference:* {reference}\n" .
                "📅 *Date:* {date}\n\n" .
                "Login to view: ownbus.software\n\n" .
                "_OwnBus Fleet Management_",

            'document_expiring' => "📄 *DOCUMENT EXPIRING SOON*\n\n" .
                "Dear {company_name},\n\n" .
                "This document expires in {days} days:\n\n" .
                "🚌 *Vehicle:* {vehicle_name}\n" .
                "📋 *Document:* {document_type}\n" .
                "📅 *Expiry:* {expiry_date}\n\n" .
                "Please renew immediately!\n\n" .
                "Login: ownbus.software\n\n" .
                "_OwnBus Fleet Management_",

            'document_expired' => "🚨 *DOCUMENT EXPIRED*\n\n" .
                "Dear {company_name},\n\n" .
                "⛔ This document has EXPIRED:\n\n" .
                "🚌 *Vehicle:* {vehicle_name}\n" .
                "📋 *Document:* {document_type}\n" .
                "📅 *Expired:* {expiry_date}\n\n" .
                "⚠️ Vehicle may not be legally operated!\n\n" .
                "Login: ownbus.software\n\n" .
                "_OwnBus Fleet Management_",

            'maintenance_due' => "🔧 *MAINTENANCE DUE*\n\n" .
                "Dear {company_name},\n\n" .
                "Vehicle maintenance is due:\n\n" .
                "🚌 *Vehicle:* {vehicle_name}\n" .
                "🔧 *Service:* {service_type}\n" .
                "📅 *Due Date:* {due_date}\n" .
                "📍 *Garage:* {garage_name}\n\n" .
                "Login to schedule: ownbus.software\n\n" .
                "_OwnBus Fleet Management_",

            'driver_license_expiring' => "🪪 *DRIVER LICENSE EXPIRING*\n\n" .
                "Dear {company_name},\n\n" .
                "Driver license expires in {days} days:\n\n" .
                "👨‍✈️ *Driver:* {driver_name}\n" .
                "🪪 *License #:* {license_number}\n" .
                "📅 *Expiry:* {expiry_date}\n\n" .
                "Please renew immediately!\n\n" .
                "Login: ownbus.software\n\n" .
                "_OwnBus Fleet Management_",

            'new_registration' => "🎉 *WELCOME TO OWNBUS!*\n\n" .
                "Dear {company_name},\n\n" .
                "Your account has been approved!\n\n" .
                "🌐 *Portal:* ownbus.software\n" .
                "📧 *Email:* {email}\n" .
                "📋 *Plan:* {plan_name}\n\n" .
                "Start managing your fleet today!\n\n" .
                "_OwnBus Fleet Management_",

            'subscription_expiring' => "⚠️ *SUBSCRIPTION EXPIRING*\n\n" .
                "Dear {company_name},\n\n" .
                "Your subscription expires in {days} days:\n\n" .
                "📋 *Plan:* {plan_name}\n" .
                "📅 *Expiry:* {expiry_date}\n" .
                "💰 *Renewal:* AED {amount}/year\n\n" .
                "Renew now: ownbus.software/subscription\n\n" .
                "_OwnBus Fleet Management_",
        ];

        $message = $templates[$template] ?? $template;
        
        foreach ($vars as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }
        
        return $message;
    }
}
