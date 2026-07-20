<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\app\Http\Controllers\ReportExportController.php';
$content = file_get_contents($file);

$oldExcelMethod = <<<'EOD'
    public function comparisonExcelAll() {
        $data = $this->getComparisonDataAll(false);
        return Excel::download(new \App\Exports\CountryComparisonExport($data), 'country-comparison-history.xlsx');
    }
EOD;

$newExcelMethod = <<<'EOD'
    public function comparisonExcelAll() {
        $data = $this->getComparisonDataAll(false);
        
        $excelData = [];
        $no = 1;
        foreach ($data as $row) {
            $excelData[] = [
                'No' => $no++,
                'Country A' => $row['country_a'],
                'Country B' => $row['country_b'],
                'GDP A' => $row['gdp_a'],
                'GDP B' => $row['gdp_b'],
                'Inflation A' => $row['inflation_a'] . '%',
                'Inflation B' => $row['inflation_b'] . '%',
                'Risk Score A' => $row['risk_score_a'],
                'Risk Score B' => $row['risk_score_b'],
                'Recommended Country' => $row['recommendation'],
                'Recommendation' => $row['summary'],
                'Comparison Date' => $row['comparison_date'],
                'Trade Analyst' => $row['created_by']
            ];
        }

        return Excel::download(new \App\Exports\CountryComparisonExport($excelData), 'country-comparison-history.xlsx');
    }
EOD;

$content = str_replace($oldExcelMethod, $newExcelMethod, $content);
file_put_contents($file, $content);
echo "ReportExportController Excel Export updated.\n";
