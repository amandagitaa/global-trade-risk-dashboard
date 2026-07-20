<?php

$exports = [
    'TradePlannerExport' => [
        'Origin Country', 'Destination Country', 'Origin Port', 'Destination Port', 'Cargo Type', 'Container Size', 'Distance', 'ETA', 'Weather Impact', 'Currency Impact', 'Trade Risk', 'AI Recommendation', 'Simulation Date'
    ],
    'RiskAnalysisExport' => [
        'Country', 'Risk Score', 'Risk Level', 'Weather Risk', 'Currency Risk', 'Economy Risk', 'Political Risk', 'Overall Recommendation'
    ],
    'CountriesExport' => [
        'Country', 'Capital', 'Region', 'Currency', 'Population', 'GDP', 'Inflation', 'Export Value', 'Import Value'
    ],
    'WeatherExport' => [
        'Country', 'Temperature', 'Humidity', 'Wind Speed', 'Weather Status', 'Storm Risk', 'Updated At'
    ],
    'CurrencyExport' => [
        'Country', 'Currency', 'Exchange Rate', 'Change %', 'Status', 'Updated At'
    ],
    'EconomyExport' => [
        'Country', 'GDP', 'Inflation', 'Unemployment', 'Export', 'Import', 'Economic Status'
    ],
    'PortsExport' => [
        'Port Name', 'Country', 'Capacity', 'Active Ships', 'Congestion', 'Operational Status', 'Risk'
    ],
    'WatchListExport' => [
        'Watch Type', 'Country / Port / Route', 'Current Risk', 'Weather', 'Currency', 'Monitoring Status', 'Added Date'
    ]
];

foreach ($exports as $className => $headings) {
    $headingsStr = implode("',\n            '", $headings);
    $template = <<<PHP
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class $className implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected \$data;

    public function __construct(array \$data)
    {
        \$this->data = \$data;
    }

    public function array(): array
    {
        return \$this->data;
    }

    public function headings(): array
    {
        return [
            '$headingsStr'
        ];
    }

    public function styles(Worksheet \$sheet)
    {
        // Styling the header row: Bold + Borders
        \$sheet->getStyle('A1:' . \$sheet->getHighestColumn() . '1')->applyFromArray([
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
        
        // Auto filter
        \$sheet->setAutoFilter('A1:' . \$sheet->getHighestColumn() . '1');
        
        // Freeze first row
        \$sheet->freezePane('A2');

        return [];
    }
}
PHP;

    file_put_contents(__DIR__ . '/app/Exports/' . $className . '.php', $template);
    echo "Created: $className\n";
}
