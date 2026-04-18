<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BalanceSheetExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $company;
    protected $report;
    protected $asOfDate;
    protected $branchId;
    protected $branches;

    public function __construct($company, $report, $asOfDate, $branches, $branchId)
    {
        $this->company = $company;
        $this->report = $report;
        $this->asOfDate = $asOfDate;
        $this->branches = $branches;
        $this->branchId = $branchId;
    }

    public function view(): View
    {
        return view('exports.balance-sheet', [
            'company' => $this->company,
            'report' => $this->report,
            'asOfDate' => $this->asOfDate,
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
