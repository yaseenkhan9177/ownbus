<?php

namespace App\Exports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TrialBalanceExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected Company $company;
    protected array $report;
    protected $start;
    protected $end;
    protected $branches;
    protected $branchId;

    public function __construct(Company $company, array $report, $start, $end, $branches, $branchId = null)
    {
        $this->company = $company;
        $this->report = $report;
        $this->start = $start;
        $this->end = $end;
        $this->branches = $branches;
        $this->branchId = $branchId;
    }

    public function collection()
    {
        $rows = collect();

        foreach ($this->report['accounts'] as $acc) {
            $rows->push([
                $acc['account_code'],
                $acc['account_name'],
                $acc['debit'] > 0 ? number_format($acc['debit'], 2) : '0.00',
                $acc['credit'] > 0 ? number_format($acc['credit'], 2) : '0.00',
            ]);
        }

        // Add Total Row
        $rows->push([
            '',
            'TOTAL',
            number_format($this->report['total_debit'], 2),
            number_format($this->report['total_credit'], 2),
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return [
            ['TRIAL BALANCE'],
            ['Company:', $this->company->name],
            ['Period:', $this->start->format('d M Y') . ' - ' . $this->end->format('d M Y')],
            ['Branch:', $this->branchId ? collect($this->branches)->firstWhere('id', $this->branchId)->name : 'Consolidated'],
            [],
            ['Code', 'Account Name', 'Debit (AED)', 'Credit (AED)']
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            6 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']],
            ],
            $sheet->getHighestRow() => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 45,
            'C' => 20,
            'D' => 20,
        ];
    }

    public function title(): string
    {
        return 'Trial Balance';
    }
}
