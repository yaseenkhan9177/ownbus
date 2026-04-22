<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller/policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $vehicle = $this->route('vehicle') ?? $this->route('fleet');
        $vehicleId = is_object($vehicle) ? $vehicle->id : $vehicle;
        $companyId = $this->user()->company_id;

        return [
            'vehicle_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tenant.vehicles', 'vehicle_number')->ignore($vehicleId),
            ],
            'name'                   => 'required|string|max:255',
            'make'                   => 'nullable|string|max:100',
            'model'                  => 'nullable|string|max:100',
            'color'                  => 'nullable|string|max:50',
            'registration_emirate'   => 'nullable|string|max:100',
            'registration_code'      => 'nullable|string|max:50',
            'plate_number'           => 'nullable|string|max:50',
            'plate_category'         => 'nullable|string|max:100',
            'plate_source'           => 'nullable|string|max:100',
            'plate_number_dp'        => 'nullable|string|max:20',
            'plate_code_dp'          => 'nullable|string|max:10',
            'vehicle_code'           => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('tenant.vehicles', 'vehicle_code')->ignore($vehicleId),
            ],
            'type'                   => 'nullable|string|in:bus,minibus,luxury,shuttle,van,car',
            'category'               => 'nullable|string|max:100',
            'seating_capacity'        => 'nullable|integer|min:1',
            'fuel_type'              => 'nullable|string|in:diesel,petrol,electric,hybrid',
            'transmission'           => 'nullable|string|in:manual,automatic',
            'year'                   => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'purchase_date'          => 'nullable|date',
            'purchase_price'         => 'nullable|numeric|min:0',
            'current_odometer'       => 'required|integer|min:0',
            'next_service_odometer'  => 'required|integer|gte:current_odometer',
            'insurance_expiry'       => 'nullable|date',
            'registration_expiry'    => 'nullable|date',
            'status'                 => 'nullable|string|in:available,rented,maintenance,inactive',
            'branch_id'              => 'nullable|exists:tenant.branches,id',
            'daily_rate'             => 'required|numeric|min:0',
            'image'                  => 'nullable|image|max:2048',
            'notes'                  => 'nullable|string',
        ];
    }
}
