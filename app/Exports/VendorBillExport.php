<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VendorBillExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $company;
    protected $vendorBills;

    public function __construct($company, $vendorBills)
    {
        $this->company = $company;
        $this->vendorBills = $vendorBills;
    }

    public function view(): View
    {
        return view('exports.vendor-bills', [
            'company' => $this->company,
            'vendorBills' => $this->vendorBills,
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
