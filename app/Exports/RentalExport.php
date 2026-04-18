<?php

namespace App\Exports;

use App\Models\Rental;
use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RentalExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected Company $company;
    protected ?string $status;

    public function __construct(Company $company, ?string $status = null)
    {
        $this->company = $company;
        $this->status  = $status;
    }

    public function collection()
    {
        return Rental::when($this->status, fn($q) => $q->where('status', $this->status))
            ->with(['vehicle', 'customer'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($r) => [
                'ID'           => '#' . substr($r->uuid ?? '', 0, 8),
                'Vehicle'      => $r->vehicle->vehicle_number ?? 'N/A',
                'Vehicle Name' => $r->vehicle->name ?? 'N/A',
                'Customer'     => $r->customer->name ?? 'N/A',
                'Phone'        => $r->customer->phone ?? 'N/A',
                'Start Date'   => $r->start_date?->format('d/m/Y') ?? '',
                'End Date'     => $r->end_date?->format('d/m/Y') ?? '',
                'Days'         => $r->start_date && $r->end_date ? $r->start_date->diffInDays($r->end_date) : '',
                'Amount (AED)' => number_format($r->final_amount ?? 0, 2),
                'Status'       => strtoupper($r->status ?? ''),
            ]);
    }

    public function headings(): array
    {
        return ['ID', 'Vehicle #', 'Vehicle Name', 'Customer', 'Phone', 'Start Date', 'End Date', 'Days', 'Amount (AED)', 'Status'];
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
            'B' => 15,
            'C' => 22,
            'D' => 22,
            'E' => 16,
            'F' => 13,
            'G' => 13,
            'H' => 7,
            'I' => 16,
            'J' => 14,
        ];
    }

    public function title(): string
    {
        return 'Rentals Report';
    }
}
