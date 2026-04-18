<?php

namespace App\Exports;

use App\Models\Rental;
use App\Models\Company;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RevenueExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected Company $company;
    protected int $months;

    public function __construct(Company $company, int $months = 6)
    {
        $this->company = $company;
        $this->months  = $months;
    }

    public function collection()
    {
        $rows = collect();
        for ($i = $this->months - 1; $i >= 0; $i--) {
            $date  = Carbon::now()->subMonths($i);
            $total = Rental::where('company_id', $this->company->id)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('final_amount');
            $count = Rental::where('company_id', $this->company->id)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $rows->push([
                'Month'          => $date->format('F Y'),
                'Total Rentals'  => $count,
                'Revenue (AED)'  => number_format($total, 2),
                'Avg/Rental'     => $count > 0 ? number_format($total / $count, 2) : '0.00',
            ]);
        }
        return $rows;
    }

    public function headings(): array
    {
        return ['Month', 'Total Rentals', 'Revenue (AED)', 'Avg Per Rental (AED)'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 18, 'B' => 16, 'C' => 18, 'D' => 22];
    }

    public function title(): string
    {
        return 'Revenue Report';
    }
}
