<?php

namespace App\Exports;

use App\Models\Driver;
use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DriverExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected Company $company;
    protected ?string $status;

    public function __construct(Company $company, ?string $status = null)
    {
        $this->company = $company;
        $this->status = $status;
    }

    public function collection()
    {
        return Driver::when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy('first_name')
            ->get()
            ->map(fn($d) => [
                'Code'            => $d->driver_code,
                'Name'            => $d->name,
                'Phone'           => $d->phone,
                'License #'       => $d->license_number,
                'License Expiry'  => $d->license_expiry_date?->format('d/m/Y') ?? 'N/A',
                'Status'          => strtoupper($d->status),
                'Hire Date'       => $d->hire_date?->format('d/m/Y') ?? 'N/A',
                'Emergency Contact' => $d->emergency_contact_phone ?? 'N/A',
            ]);
    }

    public function headings(): array
    {
        return ['Code', 'Name', 'Phone', 'License #', 'License Expiry', 'Status', 'Hire Date', 'Emergency Contact'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 25,
            'C' => 18,
            'D' => 20,
            'E' => 15,
            'F' => 12,
            'G' => 15,
            'H' => 20,
        ];
    }

    public function title(): string
    {
        return 'Drivers Report';
    }
}
