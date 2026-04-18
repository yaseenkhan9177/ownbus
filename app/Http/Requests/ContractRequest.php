<?php

namespace App\Http\Requests;

use App\Models\Vehicle;
use App\Models\Driver;
use App\Services\Fleet\FleetOperationsService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class ContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contract_no' => ['required', 'string', 'unique:tenant.rentals,contract_no'],
            'branch_id' => ['required', 'exists:tenant.branches,id'],
            'rental_type' => ['required', 'in:daily,monthly,trip_based'],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'in:draft,active,completed,cancelled'],
            'customer_id' => ['required', 'exists:tenant.customers,id'],
            'vehicle_id' => ['required', 'exists:tenant.vehicles,id'],
            'driver_id' => ['nullable', 'exists:tenant.drivers,id'],
            'base_rent' => ['required', 'numeric', 'min:0'],
            'extra_km_rate' => ['nullable', 'numeric', 'min:0'],
            'fuel_included' => ['required', 'boolean'],
            'security_deposit' => ['nullable', 'numeric', 'min:0'],
            'tax_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:Cash,Bank,Partial'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->failed()) return;

            $opsService = app(FleetOperationsService::class);

            // ❌ Do not allow assigning already rented vehicle
            $vehicle = Vehicle::find($this->input('vehicle_id'));
            if ($vehicle && !$opsService->checkVehicleAvailability(
                $vehicle,
                $this->input('start_date'),
                $this->input('end_date')
            )) {
                $validator->errors()->add('vehicle_id', 'This vehicle is already assigned to another rental or maintenance in this period.');
            }

            // ❌ Do not allow assigning busy driver
            $driverId = $this->input('driver_id');
            if ($driverId) {
                $driver = Driver::find($driverId);
                if ($driver && !$opsService->checkDriverAvailability(
                    $driver,
                    $this->input('start_date'),
                    $this->input('end_date')
                )) {
                    $validator->errors()->add('driver_id', 'This driver is busy with another assignment during this period.');
                }
            }
        });
    }
}
