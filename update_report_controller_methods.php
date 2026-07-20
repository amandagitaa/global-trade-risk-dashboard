<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\app\Http\Controllers\ReportExportController.php';
$content = file_get_contents($file);

$oldMethods = <<<'EOD'
    private function getComparisonData($isPdf = false)
    {
        $query = \App\Models\CountryComparison::with(['countryA', 'countryB'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [];
        foreach ($query as $comp) {
            $data[] = [
                'country_a' => $comp->countryA ? $comp->countryA->country_name : '-',
                'country_b' => $comp->countryB ? $comp->countryB->country_name : '-',
                'gdp_a' => $comp->comparison_result['gdp_a'] ?? '-',
                'gdp_b' => $comp->comparison_result['gdp_b'] ?? '-',
                'inflation_a' => $comp->comparison_result['inflation_a'] ?? '-',
                'inflation_b' => $comp->comparison_result['inflation_b'] ?? '-',
                'currency_a' => $comp->comparison_result['currency_a'] ?? '-',
                'currency_b' => $comp->comparison_result['currency_b'] ?? '-',
                'weather_a' => $comp->comparison_result['weather_a'] ?? '-',
                'weather_b' => $comp->comparison_result['weather_b'] ?? '-',
                'ports_a' => $comp->comparison_result['ports_a'] ?? '-',
                'ports_b' => $comp->comparison_result['ports_b'] ?? '-',
                'risk_score_a' => $comp->risk_score_a ?? '-',
                'risk_score_b' => $comp->risk_score_b ?? '-',
                'recommendation' => $comp->recommended_country,
                'summary' => $comp->recommendation,
                'comparison_date' => $comp->created_at->format('Y-m-d H:i')
            ];
        }
        return $data;
    }

    public function comparisonView() {
        $data = $this->getComparisonData(true);
        return view('reports.exports.country-comparison', compact('data'));
    }

    public function comparisonPdf() {
        $data = $this->getComparisonData(true);
        $pdf = Pdf::loadView('reports.exports.country-comparison', ['data' => $data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return $pdf->download('country-comparison-report.pdf');
    }

    public function comparisonExcel() {
        $data = $this->getComparisonData(false);
        return Excel::download(new \App\Exports\CountryComparisonExport($data), 'country-comparison-report.xlsx');
    }
EOD;

$newMethods = <<<'EOD'
    private function getComparisonData($id, $isPdf = false)
    {
        $comp = \App\Models\CountryComparison::with(['countryA', 'countryB'])
            ->where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        return [[
            'country_a' => $comp->countryA ? $comp->countryA->country_name : '-',
            'country_b' => $comp->countryB ? $comp->countryB->country_name : '-',
            'gdp_a' => $comp->comparison_result['gdp_a'] ?? '-',
            'gdp_b' => $comp->comparison_result['gdp_b'] ?? '-',
            'inflation_a' => $comp->comparison_result['inflation_a'] ?? '-',
            'inflation_b' => $comp->comparison_result['inflation_b'] ?? '-',
            'currency_a' => $comp->comparison_result['currency_a'] ?? '-',
            'currency_b' => $comp->comparison_result['currency_b'] ?? '-',
            'weather_a' => $comp->comparison_result['weather_a'] ?? '-',
            'weather_b' => $comp->comparison_result['weather_b'] ?? '-',
            'ports_a' => $comp->comparison_result['ports_a'] ?? '-',
            'ports_b' => $comp->comparison_result['ports_b'] ?? '-',
            'risk_score_a' => $comp->risk_score_a ?? '-',
            'risk_score_b' => $comp->risk_score_b ?? '-',
            'recommendation' => $comp->recommended_country,
            'summary' => $comp->recommendation,
            'comparison_date' => $comp->created_at->format('Y-m-d H:i')
        ]];
    }

    public function comparisonView($id) {
        $data = $this->getComparisonData($id, true);
        return view('reports.exports.country-comparison', compact('data'));
    }

    public function comparisonPdf($id) {
        $data = $this->getComparisonData($id, true);
        $pdf = Pdf::loadView('reports.exports.country-comparison', ['data' => $data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return $pdf->download('country-comparison-report.pdf');
    }

    public function comparisonExcel($id) {
        $data = $this->getComparisonData($id, false);
        return Excel::download(new \App\Exports\CountryComparisonExport($data), 'country-comparison-report.xlsx');
    }
EOD;

$content = str_replace($oldMethods, $newMethods, $content);
file_put_contents($file, $content);
echo "ReportExportController updated.\n";
