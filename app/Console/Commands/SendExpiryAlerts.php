<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\DriverDocument;
use App\Models\Company;
use App\Notifications\AdminDocumentExpiryNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendExpiryAlerts extends Command
{
    protected $signature   = 'fleet:expiry-alerts';
    protected $description = 'Send expiry alerts for UAE vehicle documents (Mulkiya/Insurance/Inspection/Route Permit) and driver compliance (License/RTA/Visa/Emirates ID) per company';

    public function handle(): void
    {
        $this->info('🇦🇪 UAE Fleet Expiry Alert System — Starting...');
        $this->newLine();

        $thresholds = [30, 14, 7, 1];

        // Process per company for multi-tenant notifications
        Company::chunk(50, function ($companies) use ($thresholds) {
            foreach ($companies as $company) {
                // Determine if this company has notification settings configured
                $settings = $company->notificationSetting;
                $ultraMsgSettings = $company->companyNotificationSettings;
                
                $hasOldSettings = $settings && ($settings->admin_email || $settings->admin_whatsapp);
                $hasNewSettings = $ultraMsgSettings && $ultraMsgSettings->whatsapp_enabled && $ultraMsgSettings->whatsapp_number;

                if (!$hasOldSettings && !$hasNewSettings) {
                    continue; // Skip if no notification routes configured
                }

                // Prepare a notification target
                $notifiable = Notification::route('mail', $settings->admin_email);
                if ($settings->admin_whatsapp && $settings->twilio_sid && $settings->twilio_auth_token && $settings->twilio_whatsapp_number) {
                    $notifiable->route(\App\Channels\WhatsAppChannel::class, $settings->admin_whatsapp);
                }

                foreach ($thresholds as $days) {
                    $targetDate = Carbon::now()->addDays($days)->toDateString();

                    // ── Vehicle Documents ─────────────────────────────────────
                    // Including Route Permit from recent updates
                    $vehicles = Vehicle::where('company_id', $company->id)
                        ->where(function ($q) use ($targetDate) {
                            $q->whereDate('registration_expiry', $targetDate)
                                ->orWhereDate('insurance_expiry', $targetDate)
                                ->orWhereDate('inspection_expiry_date', $targetDate)
                                ->orWhereDate('route_permit_expiry', $targetDate);
                        })->get();

                    foreach ($vehicles as $vehicle) {
                        $checks = [
                            'Mulkiya (Registration)' => $vehicle->registration_expiry,
                            'Insurance'              => $vehicle->insurance_expiry,
                            'RTA Inspection'         => $vehicle->inspection_expiry_date,
                            'Route Permit'           => $vehicle->route_permit_expiry,
                        ];

                        foreach ($checks as $label => $date) {
                            if ($date && $date->toDateString() === $targetDate) {
                                $this->warn("  ⚠ [Company {$company->name}] Vehicle {$vehicle->vehicle_number}: {$label} expires in {$days} day(s)");
                                if ($hasOldSettings) {
                                    $notifiable->notify(new AdminDocumentExpiryNotification($label, "Vehicle {$vehicle->vehicle_number}", $days));
                                }

                                if ($hasNewSettings && $ultraMsgSettings->notify_document_expiring) {
                                    \App\Jobs\SendWhatsAppJob::dispatch(
                                        $ultraMsgSettings->whatsapp_number,
                                        $days <= 1 ? 'document_expired' : 'document_expiring',
                                        [
                                            'company_name' => $company->name,
                                            'vehicle_name' => $vehicle->vehicle_number,
                                            'document_type' => $label,
                                            'days' => $days,
                                            'expiry_date' => $date->format('d M Y'),
                                        ]
                                    );
                                }
                            }
                        }
                    }

                    // ── Driver UAE Compliance ──────────────────────────────────
                    $drivers = Driver::where('company_id', $company->id)
                        ->where('status', Driver::STATUS_ACTIVE)
                        ->where(function ($q) use ($targetDate) {
                            $q->whereDate('license_expiry_date', $targetDate)
                                ->orWhereDate('rta_permit_expiry', $targetDate)
                                ->orWhereDate('visa_expiry', $targetDate)
                                ->orWhereDate('emirates_id_expiry', $targetDate);
                        })->get();

                    foreach ($drivers as $driver) {
                        $checks = [
                            'Driving License' => $driver->license_expiry_date,
                            'RTA Permit'      => $driver->rta_permit_expiry,
                            'Residence Visa'  => $driver->visa_expiry,
                            'Emirates ID'     => $driver->emirates_id_expiry,
                        ];
                        foreach ($checks as $label => $date) {
                            if ($date && $date->toDateString() === $targetDate) {
                                $this->warn("  ⚠ [Company {$company->name}] Driver {$driver->name}: {$label} expires in {$days} day(s)");
                                if ($hasOldSettings) {
                                    $notifiable->notify(new AdminDocumentExpiryNotification($label, "Driver {$driver->name}", $days));
                                }

                                if ($hasNewSettings && $ultraMsgSettings->notify_driver_license) {
                                    \App\Jobs\SendWhatsAppJob::dispatch(
                                        $ultraMsgSettings->whatsapp_number,
                                        'driver_license_expiring',
                                        [
                                            'company_name' => $company->name,
                                            'driver_name' => $driver->name,
                                            'license_number' => $driver->license_number ?? 'N/A',
                                            'days' => $days,
                                            'expiry_date' => $date->format('d M Y'),
                                        ]
                                    );
                                }
                            }
                        }
                    }
                }
            }
        });

        $this->newLine();
        $this->info('✅ UAE Expiry Alert check completed.');
    }
}
