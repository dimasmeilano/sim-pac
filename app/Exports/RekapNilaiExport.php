<?php

namespace App\Exports;

use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapNilaiExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    public function view(): View
    {
        return view('admin.makesta.export_rekap_excel', [
            'event' => $this->event
        ]);
    }

    // Fungsi untuk memberikan garis pinggir (border) pada semua sel Excel
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);
    }
}
