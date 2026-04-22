<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\CompanyNotificationSettings;
use Illuminate\Http\Request;
use App\Services\WhatsAppService;

class WhatsAppSettingsController extends Controller
{
    public function index()
    {
        $company = auth()->user()->company;
        $settings = $company->companyNotificationSettings ?? new CompanyNotificationSettings();

        return view('portal.settings.whatsapp', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'whatsapp_number' => 'nullable|string|max:20',
        ]);

        $company = auth()->user()->company;

        CompanyNotificationSettings::updateOrCreate(
            ['company_id' => $company->id],
            [
                'whatsapp_number' => $request->whatsapp_number,
                'whatsapp_enabled' => $request->has('whatsapp_enabled'),
                'notify_new_rental' => $request->has('notify_new_rental'),
                'notify_rental_expiring' => $request->has('notify_rental_expiring'),
                'notify_payment' => $request->has('notify_payment'),
                'notify_new_fine' => $request->has('notify_new_fine'),
                'notify_document_expiring' => $request->has('notify_document_expiring'),
                'notify_maintenance' => $request->has('notify_maintenance'),
                'notify_driver_license' => $request->has('notify_driver_license'),
                'notify_subscription' => $request->has('notify_subscription'),
            ]
        );

        return redirect()->back()->with('success', 'WhatsApp settings updated successfully.');
    }

    public function test(Request $request, WhatsAppService $whatsAppService)
    {
        $request->validate([
            'whatsapp_number' => 'required|string|max:20',
        ]);

        $phone = $request->whatsapp_number;
        $message = "✅ *Test Message*\n\nThis is a test message from OwnBus to verify your WhatsApp setup.";
        
        $success = $whatsAppService->send($phone, $message);

        if ($success) {
            return redirect()->back()->with('success', 'Test message sent successfully! Please check your WhatsApp.');
        }

        return redirect()->back()->with('error', 'Failed to send test message. Check your API credentials and ensure the number is correct.');
    }
}
