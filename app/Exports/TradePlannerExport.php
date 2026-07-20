<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TradePlannerExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Origin Country',
            'Destination Country',
            'Origin Port',
            'Destination Port',
            'Cargo Type',
            'Container Size',
            'Distance',
            'ETA',
            'Weather Impact',
            'Currency Impact',
            'Trade Risk',
            'AI Recommendation',
            'Simulation Date'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Styling the header row: Bold + Borders
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);
        
        $sheet->setAutoFilter('A1:' . $sheet->getHighestColumn() . '1');
        
        $sheet->freezePane('A2');

        return [];
    }
}