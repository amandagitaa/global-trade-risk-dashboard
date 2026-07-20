<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CompareLiveExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $res;

    public function __construct(array $res)
    {
        $this->res = $res;
    }

    public function array(): array
    {
        $rows = [];
        $res = $this->res;

        // 1. Country Information
        $rows[] = ['Country Information', 'Country', $res['country_name_a'], $res['country_name_b']];
        $rows[] = ['', 'Capital', $res['capital_a'], $res['capital_b']];
        $rows[] = ['', 'Region', $res['region_a'], $res['region_b']];
        $rows[] = ['', 'Population', number_format((float)$res['population_a']), number_format((float)$res['population_b'])];
        $rows[] = ['', 'Currency', $res['currency_name_a'] . ' (' . $res['currency_code_a'] . ')', $res['currency_name_b'] . ' (' . $res['currency_code_b'] . ')'];
        $rows[] = ['', 'Language', $res['language_a'], $res['language_b']];

        // 2. Economy
        $rows[] = ['Economy Comparison', 'GDP', $res['formatted_gdp_a'], $res['formatted_gdp_b']];
        $rows[] = ['', 'Inflation', number_format((float)$res['inflation_a'], 1) . '%', number_format((float)$res['inflation_b'], 1) . '%'];
        $rows[] = ['', 'Export', $res['formatted_exports_a'], $res['formatted_exports_b']];
        $rows[] = ['', 'Import', $res['formatted_imports_a'], $res['formatted_imports_b']];

        // 3. Weather
        $rows[] = ['Weather Comparison', 'Temperature', $res['temp_a'] . '°C', $res['temp_b'] . '°C'];
        $rows[] = ['', 'Wind Speed', $res['wind_a'] . ' km/h', $res['wind_b'] . ' km/h'];
        $rows[] = ['', 'Rainfall', $res['rain_a'] . '%', $res['rain_b'] . '%'];
        $rows[] = ['', 'Weather Status', $res['weather_status_a'], $res['weather_status_b']];

        // 4. Currency
        $rows[] = ['Currency Comparison', 'Currency Code', $res['currency_code_a'], $res['currency_code_b']];
        $rows[] = ['', 'Exchange Rate', $res['exchange_a'], $res['exchange_b']];
        $rows[] = ['', 'Currency Status', $res['currency_status_a'], $res['currency_status_b']];

        // 5. Ports
        $rows[] = ['Port Comparison', 'Main Port', $res['main_port_a'], $res['main_port_b']];
        $rows[] = ['', 'Major Ports Count', $res['ports_count_a'], $res['ports_count_b']];
        $rows[] = ['', 'Congestion/Avg Risk', $res['risk_port_a'], $res['risk_port_b']];
        $rows[] = ['', 'Shipping Status', 'Normal', 'Normal'];

        // 6. News
        $rows[] = ['News Sentiment', 'Average Sentiment Score', is_numeric($res['news_sentiment_a']) ? number_format((float)$res['news_sentiment_a'], 2) : $res['news_sentiment_a'], is_numeric($res['news_sentiment_b']) ? number_format((float)$res['news_sentiment_b'], 2) : $res['news_sentiment_b']];

        // 7. Risk Analysis
        $rows[] = ['Risk Analysis', 'Weather Risk', $res['risk_weather_a'], $res['risk_weather_b']];
        $rows[] = ['', 'Inflation Risk', $res['risk_inflation_a'], $res['risk_inflation_b']];
        $rows[] = ['', 'Currency Risk', $res['risk_currency_a'], $res['risk_currency_b']];
        $rows[] = ['', 'Political Risk', $res['risk_political_a'], $res['risk_political_b']];
        $rows[] = ['', 'Overall Risk Score', $res['risk_final_a'], $res['risk_final_b']];

        // 8. Recommendation
        $reasonsStr = implode(", ", $res['reasons']);
        $rows[] = ['Trade Recommendation', 'Recommended Country', $res['winner_name'], $res['winner_name']];
        $rows[] = ['', 'Key Reasons', $reasonsStr, $reasonsStr];
        $rows[] = ['', 'Conclusion', $res['conclusion'], $res['conclusion']];

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Section',
            'Parameter',
            $this->res['country_name_a'] ?? 'Country A',
            $this->res['country_name_b'] ?? 'Country B'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'color' => ['argb' => 'FF0D6EFD']]],
        ];
    }
}
