<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GeneralLedgerExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $company;
    protected $report;
    protected $account;
    protected $start;
    protected $end;
    protected $branches;
    protected $branchId;

    public function __construct($company, $report, $account, $start, $end, $branches, $branchId)
    {
        $this->company = $company;
        $this->report = $report;
        $this->account = $account;
        $this->start = $start;
        $this->end = $end;
        $this->branches = $branches;
        $this->branchId = $branchId;
    }

    public function view(): View
    {
        return view('exports.general-ledger', [
            'company' => $this->company,
            'report' => $this->report,
            'account' => $this->account,
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
