<?php

namespace App\Http\Requests;

use App\Models\Rental;
use App\Models\Vehicle;
use App\Services\Fleet\FleetOperationsService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class RentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:tenant.customers,id'],
            'vehicle_id' => ['sometimes', 'nullable', 'exists:tenant.vehicles,id'],
            'driver_id' => ['sometimes', 'nullable', 'exists:tenant.drivers,id'],
            'rental_type' => ['required', 'in:daily,hourly,monthly,distance'],
            'rate_type' => ['required', 'in:daily,weekly,monthly'],
            'rate_amount' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
            'pickup_location' => ['required', 'string', 'max:255'],
            'dropoff_location' => ['nullable', 'string', 'max:255'],
            'security_deposit' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->failed()) return;

            $vehicleId = $this->input('vehicle_id');
            if (!$vehicleId) return;

            $vehicle = Vehicle::where('id', $vehicleId)
                ->first();

            if (!$vehicle) {
                $validator->errors()->add('vehicle_id', 'Invalid vehicle selected.');
                return;
            }

            // Conflict check only if we are confirming or activating
            // For drafts, we might allow it (soft check), but user said "Before confirming rental: Check"
            // However, usually it's better to prevent overlap at creation too if not draft.

            $opsService = app(FleetOperationsService::class);
            $excludeRentalId = $this->route('rental') ? $this->route('rental')->id : null;

            if (!$opsService->checkVehicleAvailability(
                $vehicle,
                $this->input('start_date'),
                $this->input('end_date'),
                $excludeRentalId
            )) {
                $validator->errors()->add('vehicle_id', 'This vehicle has an overlapping rental or maintenance record.');
            }

            $driverId = $this->input('driver_id');
            if ($driverId) {
                $driver = \App\Models\Driver::where('id', $driverId)
                    ->first();

                if (!$driver) {
                    $validator->errors()->add('driver_id', 'Invalid driver selected.');
                    return;
                }

                if (!$opsService->checkDriverAvailability(
                    $driver,
                    $this->input('start_date'),
                    $this->input('end_date'),
                    $excludeRentalId
                )) {
                    $validator->errors()->add('driver_id', 'This driver is unavailable (Suspended, Expired License, or Overlapping Rental).');
                }
            }
        });
    }
}
