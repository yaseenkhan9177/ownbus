<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoiceExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $company;
    protected $invoices;

    public function __construct($company, $invoices)
    {
        $this->company = $company;
        $this->invoices = $invoices;
    }

    public function view(): View
    {
        return view('exports.invoices', [
            'company' => $this->company,
            'invoices' => $this->invoices
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 14]],
            2    => ['font' => ['bold' => true, 'size' => 12]],
            // The table headers will be styled in the blade view via HTML, but we can also add some default formatting if we want
        ];
    }
}
