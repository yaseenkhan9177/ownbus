<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CashFlowExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $company;
    protected $report;
    protected $start;
    protected $end;
    protected $branches;
    protected $branchId;

    public function __construct($company, $report, $start, $end, $branches, $branchId)
    {
        $this->company = $company;
        $this->report = $report;
        $this->start = $start;
        $this->end = $end;
        $this->branches = $branches;
        $this->branchId = $branchId;
    }

    public function view(): View
    {
        return view('exports.cash-flow', [
            'company' => $this->company,
            'report' => $this->report,
            'start' => $this->start,
            'end' => $this->end,
            'branches' => $this->branches,
            'branchId' => $this->branchId,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
