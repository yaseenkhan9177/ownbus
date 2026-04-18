<?php

namespace App\Exports;

use App\Models\Vehicle;
use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VehicleExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected Company $company;
    protected array $filters;

    public function __construct(Company $company, array $filters = [])
    {
        $this->company = $company;
        $this->filters = $filters;
    }

    public function collection()
    {
        return Vehicle::query()
            ->when(isset($this->filters['status']), function ($q) {
                $q->where('status', $this->filters['status']);
            })
            ->get();
    }

    public function map($vehicle): array
    {
        return [
            $vehicle->vehicle_number,
            $vehicle->name,
            $vehicle->model,
            $vehicle->type,
            $vehicle->seating_capacity,
            $vehicle->current_odometer,
            strtoupper($vehicle->status),
            $vehicle->registration_expiry ? $vehicle->registration_expiry->format('Y-m-d') : 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            'Vehicle Number',
            'Name',
            'Model',
            'Type',
            'Seats',
            'Odometer',
            'Status',
            'Registration Expiry'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
