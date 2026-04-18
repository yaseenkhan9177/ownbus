<?php

namespace App\Services\Portal;

use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Rental;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Get all active notifications for the company.
     */
    public function getNotifications(Company $company): Collection
    {
        $notifications = collect();

        // 1. Vehicle Document Expiries
        $this->getVehicleExpiries($company)->each(fn($n) => $notifications->push($n));

        // 2. Driver License Expiries
        $this->getDriverExpiries($company)->each(fn($n) => $notifications->push($n));

        // 3. Maintenance Due
        $this->getMaintenanceAlerts($company)->each(fn($n) => $notifications->push($n));

        // 4. Overdue Payments
        $this->getOverduePayments($company)->each(fn($n) => $notifications->push($n));

        return $notifications->sortByDesc('created_at');
    }

    protected function getVehicleExpiries(Company $company): Collection
    {
        $threshold = now()->addDays(30);
        $vehicles = Vehicle::where(function ($q) use ($threshold) {
            $q->whereDate('registration_expiry', '<=', $threshold)
                ->orWhereDate('insurance_expiry', '<=', $threshold)
                ->orWhereDate('inspection_expiry_date', '<=', $threshold)
                ->orWhereDate('route_permit_expiry', '<=', $threshold);
        })->get();

        return $vehicles->map(function ($vehicle) {
            return [
                'type' => 'alert',
                'category' => 'Vehicle',
                'title' => "Document Expiry: {$vehicle->vehicle_number}",
                'message' => "One or more documents for {$vehicle->name} are expiring soon or have expired.",
                'link' => route('company.fleet.edit', $vehicle->id),
                'severity' => 'warning',
                'created_at' => now(), // Using now() as a placeholder for sorting if no specific event time
            ];
        });
    }

    protected function getDriverExpiries(Company $company): Collection
    {
        return Driver::where('status', Driver::STATUS_ACTIVE)
            ->get()
            ->filter(fn($driver) => $driver->hasComplianceRisk(30))
            ->map(function ($driver) {
                return [
                    'type' => 'alert',
                    'category' => 'Driver',
                    'title' => "License/Visa Expiry: {$driver->full_name}",
                    'message' => "Compliance documents for {$driver->full_name} require attention.",
                    'link' => route('company.drivers.show', $driver->id),
                    'severity' => 'warning',
                    'created_at' => now(),
                ];
            });
    }

    protected function getMaintenanceAlerts(Company $company): Collection
    {
        $vehicles = Vehicle::where(function ($q) {
            $q->whereRaw('next_service_odometer - current_odometer <= ?', [500])
                ->orWhere('status', 'maintenance');
        })->get();

        return $vehicles->map(function ($vehicle) {
            return [
                'type' => 'maintenance',
                'category' => 'Maintenance',
                'title' => "Maintenance Due: {$vehicle->vehicle_number}",
                'message' => "{$vehicle->name} is due for service soon.",
                'link' => route('company.maintenance.index'),
                'severity' => 'info',
                'created_at' => now(),
            ];
        });
    }

    protected function getOverduePayments(Company $company): Collection
    {
        $overdueRentals = Rental::where('status', 'overdue')->get();

        return $overdueRentals->map(function ($rental) {
            return [
                'type' => 'payment',
                'category' => 'Finance',
                'title' => "Overdue Payment: #{$rental->rental_number}",
                'message' => "Payment for rental to {$rental->customer->name} is overdue.",
                'link' => route('company.rentals.show', $rental->id),
                'severity' => 'error',
                'created_at' => now(),
            ];
        });
    }
}
