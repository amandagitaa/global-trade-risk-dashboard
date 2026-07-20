<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\app\Http\Controllers\CountryComparisonController.php';
$content = file_get_contents($file);

$newMethod = <<<'EOD'
    public function save(Request $request)
    {
        $request->validate([
            'country_a_id' => 'required|exists:countries,id',
            'country_b_id' => 'required|exists:countries,id|different:country_a_id',
        ]);

        $countryA = Country::with(['latestRisk', 'latestWeather', 'latestCurrency', 'economicData', 'ports', 'latestNews'])->find($request->country_a_id);
        $countryB = Country::with(['latestRisk', 'latestWeather', 'latestCurrency', 'economicData', 'ports', 'latestNews'])->find($request->country_b_id);

        $recommendation = $this->generateRecommendation($countryA, $countryB);

        $comparisonResult = [
            'gdp_a' => $countryA->economicData ? $countryA->economicData->gdp : 0,
            'gdp_b' => $countryB->economicData ? $countryB->economicData->gdp : 0,
            'inflation_a' => $countryA->economicData ? $countryA->economicData->inflation : 0,
            'inflation_b' => $countryB->economicData ? $countryB->economicData->inflation : 0,
            'currency_a' => $countryA->latestCurrency ? $countryA->latestCurrency->status : 'Unknown',
            'currency_b' => $countryB->latestCurrency ? $countryB->latestCurrency->status : 'Unknown',
            'weather_a' => $countryA->latestRisk ? $countryA->latestRisk->weather_score : 100,
            'weather_b' => $countryB->latestRisk ? $countryB->latestRisk->weather_score : 100,
            'ports_a' => $countryA->ports->count(),
            'ports_b' => $countryB->ports->count(),
            'reasons' => $recommendation['reasons']
        ];

        \App\Models\CountryComparison::create([
            'user_id' => auth()->id(),
            'country_a_id' => $countryA->id,
            'country_b_id' => $countryB->id,
            'risk_score_a' => $countryA->latestRisk ? $countryA->latestRisk->final_score : 100,
            'risk_score_b' => $countryB->latestRisk ? $countryB->latestRisk->final_score : 100,
            'recommended_country' => $recommendation['winner']->country_name,
            'recommendation' => $recommendation['conclusion'],
            'comparison_result' => $comparisonResult
        ]);

        return redirect()->route('reports.index')->with('success', 'Country Comparison saved successfully to your Reports.');
    }
}
EOD;

$content = preg_replace('/\}\s*$/', "\n$newMethod", $content);
file_put_contents($file, $content);
echo "Method added to CountryComparisonController\n";
