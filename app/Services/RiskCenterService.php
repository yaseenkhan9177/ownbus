<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Contract;
use App\Models\ContractInvoice;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\VehicleFine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Unified Risk Center Service
 * Aggregates operational, compliance, and financial risks into a single engine.
 */
class RiskCenterService
{
    public function getCompanyRiskSummary(Company $company): array
    {
        return [
            'critical' => $this->getCriticalRisks($company),
            'warning'  => $this->getWarningRisks($company),
            'info'     => $this->getInfoRisks($company),
        ];
    }

    private function getCriticalRisks(Company $company): Collection
    {
        $risks = collect();

        // 1. Expired Driver Licenses
        Driver::where('status', 'active')
            ->where('license_expiry_date', '<', now()->toDateString())
            ->get()
            ->each(fn($d) => $risks->push($this->formatRisk(
                'driver_license_expired',
                'Critical: Driver License Expired',
                $d->id,
                $d->name,
                'critical'
            )));

        // 2. Expired Vehicle Mulkiya/Registration
        Vehicle::where('status', '!=', 'inactive')
            ->where('registration_expiry', '<', now()->toDateString())
            ->get()
            ->each(fn($v) => $risks->push($this->formatRisk(
                'vehicle_mulkiya_expired',
                'Critical: Vehicle Registration Expired',
                $v->id,
                $v->vehicle_number,
                'critical'
            )));

        // 3. Unpaid Fines > 30 Days
        VehicleFine::where('status', 'pending')
            ->where('due_date', '<', now()->subDays(30)->toDateString())
            ->get()
            ->each(fn($f) => $risks->push($this->formatRisk(
                'fine_overdue_30',
                'Critical: Fine Unpaid > 30 Days',
                $f->id,
                $f->fine_number . ' (' . $f->amount . ')',
                'critical'
            )));

        // 4. AR Overdue > 60 Days
        ContractInvoice::where('status', 'draft') // draft often means unpaid/pending in this system
            ->where('due_date', '<', now()->subDays(60)->toDateString())
            ->with('customer')
            ->get()
            ->each(fn($i) => $risks->push($this->formatRisk(
                'ar_overdue_60',
                'Critical: AR Overdue > 60 Days',
                $i->id,
                $i->invoice_number . ' - ' . ($i->customer?->name ?? 'Unknown'),
                'critical'
            )));

        // 5. High Breakdown Risk (Score > 80)
        DB::connection('tenant')->table('vehicle_risk_predictions')
            ->join('vehicles', 'vehicles.id', '=', 'vehicle_risk_predictions.vehicle_id')
            ->where('vehicle_risk_predictions.risk_score', '>', 80)
            ->select('vehicles.id', 'vehicles.vehicle_number', 'vehicle_risk_predictions.risk_score')
            ->get()
            ->each(fn($p) => $risks->push($this->formatRisk(
                'high_breakdown_risk',
                'Critical: High Breakdown Risk (' . $p->risk_score . '%)',
                $p->id,
                $p->vehicle_number,
                'critical'
            )));

        // 6. High Accident Risk (Score > 80)
        DB::connection('tenant')->table('driver_risk_predictions')
            ->join('drivers', 'drivers.id', '=', 'driver_risk_predictions.driver_id')
            ->where('driver_risk_predictions.risk_score', '>', 80)
            ->select('drivers.id', 'drivers.first_name', 'drivers.last_name', 'driver_risk_predictions.risk_score')
            ->get()
            ->each(fn($p) => $risks->push($this->formatRisk(
                'high_accident_risk',
                'Critical: High Accident Risk (' . $p->risk_score . '%)',
                $p->id,
                $p->first_name . ' ' . $p->last_name,
                'critical'
            )));

        // 7. GPS Offline > 24 Hours
        Vehicle::where('status', '!=', 'inactive')
            ->where('last_gps_ping_at', '<', now()->subHours(24))
            ->get()
            ->each(fn($v) => $risks->push($this->formatRisk(
                'gps_offline_24',
                'Critical: GPS Offline > 24 Hours',
                $v->id,
                $v->vehicle_number,
                'critical'
            )));

        return $risks;
    }

    private function getWarningRisks(Company $company): Collection
    {
        $risks = collect();

        // 1. License expiring < 7 days
        Driver::where('status', 'active')
            ->whereBetween('license_expiry_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->get()
            ->each(fn($d) => $risks->push($this->formatRisk(
                'driver_license_expiring_soon',
                'Warning: License Expiring < 7 Days',
                $d->id,
                $d->name,
                'warning'
            )));

        // 2. Mulkiya expiring < 7 days
        Vehicle::where('status', '!=', 'inactive')
            ->whereBetween('registration_expiry', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->get()
            ->each(fn($v) => $risks->push($this->formatRisk(
                'vehicle_mulkiya_expiring_soon',
                'Warning: Mulkiya Expiring < 7 Days',
                $v->id,
                $v->vehicle_number,
                'warning'
            )));

        // 3. Fine unpaid > 7 days
        VehicleFine::where('status', 'pending')
            ->whereBetween('due_date', [now()->subDays(30)->toDateString(), now()->subDays(7)->toDateString()])
            ->get()
            ->each(fn($f) => $risks->push($this->formatRisk(
                'fine_overdue_7',
                'Warning: Fine Unpaid > 7 Days',
                $f->id,
                $f->fine_number,
                'warning'
            )));

        // 4. AR Overdue > 30 days
        ContractInvoice::where('status', 'draft')
            ->whereBetween('due_date', [now()->subDays(60)->toDateString(), now()->subDays(30)->toDateString()])
            ->with('customer')
            ->get()
            ->each(fn($i) => $risks->push($this->formatRisk(
                'ar_overdue_30',
                'Warning: AR Overdue > 30 Days',
                $i->id,
                $i->invoice_number . ' - ' . ($i->customer?->name ?? 'Unknown'),
                'warning'
            )));

        // 5. Risk scores 60-79
        // Included both vehicle and driver for brevity in warning
        DB::connection('tenant')->table('vehicle_risk_predictions')
            ->join('vehicles', 'vehicles.id', '=', 'vehicle_risk_predictions.vehicle_id')
            ->whereBetween('vehicle_risk_predictions.risk_score', [60, 79])
            ->select('vehicles.id', 'vehicles.vehicle_number', 'vehicle_risk_predictions.risk_score')
            ->get()
            ->each(fn($p) => $risks->push($this->formatRisk(
                'medium_breakdown_risk',
                'Warning: Medium Breakdown Risk (' . $p->risk_score . '%)',
                $p->id,
                $p->vehicle_number,
                'warning'
            )));

        // 7. Maintenance overdue (simple check based on next_service_odometer)
        Vehicle::where('status', '!=', 'inactive')
            ->whereColumn('current_odometer', '>=', 'next_service_odometer')
            ->whereNotNull('next_service_odometer')
            ->get()
            ->each(fn($v) => $risks->push($this->formatRisk(
                'maintenance_overdue',
                'Warning: Maintenance Overdue (' . ($v->current_odometer - $v->next_service_odometer) . ' KM)',
                $v->id,
                $v->vehicle_number,
                'warning'
            )));

        return $risks;
    }

    private function getInfoRisks(Company $company): Collection
    {
        $risks = collect();

        // 1. Contracts expiring soon (< 30 days)
        Contract::where('status', 'active')
            ->whereBetween('end_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->get()
            ->each(fn($c) => $risks->push($this->formatRisk(
                'contract_expiring_soon',
                'Info: Contract Expiring Soon',
                $c->id,
                $c->contract_number,
                'info'
            )));

        // 2. Insurance expiring < 15 days
        Vehicle::where('status', '!=', 'inactive')
            ->whereBetween('insurance_expiry', [now()->toDateString(), now()->addDays(15)->toDateString()])
            ->get()
            ->each(fn($v) => $risks->push($this->formatRisk(
                'vehicle_insurance_expiring',
                'Info: Insurance Expiring < 15 Days',
                $v->id,
                $v->vehicle_number,
                'info'
            )));

        // 3. Driver visa expiring < 30 days
        Driver::query()
            ->whereBetween('visa_expiry', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->get()
            ->each(fn($d) => $risks->push($this->formatRisk(
                'driver_visa_expiring',
                'Info: Visa Expiring < 30 Days',
                $d->id,
                $d->name,
                'info'
            )));

        return $risks;
    }

    private function formatRisk(string $type, string $title, int $entityId, string $entityName, string $priority): array
    {
        // Guessing route pattern based on standard Laravel/App conventions
        $route = match ($type) {
            'driver_license_expired', 'driver_license_expiring_soon', 'driver_visa_expiring' => route('company.drivers.show', $entityId),
            'vehicle_mulkiya_expired', 'vehicle_mulkiya_expiring_soon', 'high_breakdown_risk', 'medium_breakdown_risk', 'maintenance_overdue', 'vehicle_insurance_expiring', 'gps_offline_24' => route('company.fleet.show', $entityId),
            'fine_overdue_30', 'fine_overdue_7' => route('company.fines.index', ['search' => $entityId]), // Search by ID or similar
            'ar_overdue_60', 'ar_overdue_30' => route('company.contracts.show', ContractInvoice::find($entityId)->contract_id), // Link to contract
            'contract_expiring_soon' => route('company.contracts.show', $entityId),
            'high_accident_risk' => route('company.drivers.show', $entityId),
            default => '#',
        };

        return [
            'type'        => $type,
            'title'       => $title,
            'entity_id'   => $entityId,
            'entity_name' => $entityName,
            'action_url'  => $route,
            'priority'    => $priority,
        ];
    }
}
