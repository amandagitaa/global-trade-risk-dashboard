<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\app\Http\Controllers\ReportExportController.php';
$content = file_get_contents($file);

$newMethods = <<<'EOD'
    private function getComparisonDataAll($isPdf = false)
    {
        $query = \App\Models\CountryComparison::with(['countryA', 'countryB', 'user'])
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
                'news_sentiment_a' => $comp->comparison_result['news_sentiment_a'] ?? '-',
                'news_sentiment_b' => $comp->comparison_result['news_sentiment_b'] ?? '-',
                'risk_score_a' => $comp->risk_score_a ?? '-',
                'risk_score_b' => $comp->risk_score_b ?? '-',
                'recommendation' => $comp->recommended_country,
                'summary' => $comp->recommendation,
                'comparison_date' => $comp->created_at->format('Y-m-d H:i'),
                'created_by' => $comp->user->name ?? 'User'
            ];
        }
        return $data;
    }

    public function comparisonPdfAll() {
        $data = $this->getComparisonDataAll(true);
        $pdf = Pdf::loadView('reports.exports.country-comparison', ['data' => $data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return $pdf->download('country-comparison-history.pdf');
    }

    public function comparisonExcelAll() {
        $data = $this->getComparisonDataAll(false);
        return Excel::download(new \App\Exports\CountryComparisonExport($data), 'country-comparison-history.xlsx');
    }
EOD;

$content = str_replace(
    "public function comparisonDetail(\$id) {",
    $newMethods . "\n\n    public function comparisonDetail(\$id) {",
    $content
);
file_put_contents($file, $content);
echo "ReportExportController updated with PDF/Excel All.\n";
