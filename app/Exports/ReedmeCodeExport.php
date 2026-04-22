<?php

namespace App\Exports;

use App\Models\ReedmeCode;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReedmeCodeExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ReedmeCode::select('code', 'status')->get();
    }

    public function headings(): array
    {
        return [
            'Code',
            'Status',
        ];
    }

    public function map($code): array
    {
        return [
            $code->code,
            $code->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row (row 1)
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '28A745'], // Green
                ],
            ],
        ];
}
}
