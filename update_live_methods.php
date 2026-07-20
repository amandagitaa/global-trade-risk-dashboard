<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\app\Http\Controllers\ReportExportController.php';
$content = file_get_contents($file);

$newMethods = <<<'EOD'
    private function generateRecommendationLive($a, $b)
    {
        $scoreA = 0; $scoreB = 0; $reasonsA = []; $reasonsB = [];

        $riskA = $a->latestRisk ? $a->latestRisk->final_score : 100;
        $riskB = $b->latestRisk ? $b->latestRisk->final_score : 100;
        if ($riskA < $riskB) { $scoreA++; $reasonsA[] = 'Lower Overall Risk'; }
        elseif ($riskB < $riskA) { $scoreB++; $reasonsB[] = 'Lower Overall Risk'; }

        $gdpA = $a->economicData ? (float) $a->economicData->gdp : 0;
        $gdpB = $b->economicData ? (float) $b->economicData->gdp : 0;
        if ($gdpA > $gdpB) { $scoreA++; $reasonsA[] = 'Stronger Economy (Higher GDP)'; }
        elseif ($gdpB > $gdpA) { $scoreB++; $reasonsB[] = 'Stronger Economy (Higher GDP)'; }

        $infA = $a->economicData ? (float) $a->economicData->inflation : 100;
        $infB = $b->economicData ? (float) $b->economicData->inflation : 100;
        if ($infA < $infB) { $scoreA++; $reasonsA[] = 'Lower Inflation Rate'; }
        elseif ($infB < $infA) { $scoreB++; $reasonsB[] = 'Lower Inflation Rate'; }

        $currA = $a->latestCurrency ? strtolower($a->latestCurrency->status) : '';
        $currB = $b->latestCurrency ? strtolower($b->latestCurrency->status) : '';
        if (in_array($currA, ['stable', 'strong']) && !in_array($currB, ['stable', 'strong'])) { $scoreA++; $reasonsA[] = 'More Stable Currency'; }
        elseif (in_array($currB, ['stable', 'strong']) && !in_array($currA, ['stable', 'strong'])) { $scoreB++; $reasonsB[] = 'More Stable Currency'; }

        $weathA = $a->latestRisk ? $a->latestRisk->weather_score : 100;
        $weathB = $b->latestRisk ? $b->latestRisk->weather_score : 100;
        if ($weathA < $weathB) { $scoreA++; $reasonsA[] = 'Better Weather Conditions'; }
        elseif ($weathB < $weathA) { $scoreB++; $reasonsB[] = 'Better Weather Conditions'; }

        $winner = null; $reasons = [];
        if ($scoreA >= $scoreB) {
            $winner = $a; $reasons = $reasonsA;
            if (empty($reasons)) $reasons[] = 'Better Trade Environment';
        } else {
            $winner = $b; $reasons = $reasonsB;
            if (empty($reasons)) $reasons[] = 'Better Trade Environment';
        }

        return [
            'winner' => $winner,
            'reasons' => $reasons,
            'conclusion' => "{$winner->country_name} is currently the recommended trading partner based on integrated supply chain risk analysis."
        ];
    }

    private function getLiveComparisonData(Request $request)
    {
        $countryAId = $request->get('country_a');
        $countryBId = $request->get('country_b');

        if (!$countryAId || !$countryBId) abort(404);

        $countryA = Country::with(['latestRisk', 'latestWeather', 'latestCurrency', 'economicData', 'ports', 'newsCache' => function($q) { $q->latest('published_at')->take(5); }])->find($countryAId);
        $countryB = Country::with(['latestRisk', 'latestWeather', 'latestCurrency', 'economicData', 'ports', 'newsCache' => function($q) { $q->latest('published_at')->take(5); }])->find($countryBId);

        if (!$countryA || !$countryB) abort(404);

        $recommendation = $this->generateRecommendationLive($countryA, $countryB);
        
        $newsListA = $countryA->newsCache;
        $newsListB = $countryB->newsCache;

        return [
            'country_name_a' => $countryA->country_name,
            'country_name_b' => $countryB->country_name,
            'country_code_a' => $countryA->country_code,
            'country_code_b' => $countryB->country_code,
            'flag_a' => $countryA->flag ?? 'https://flagcdn.com/w80/'.strtolower($countryA->country_code).'.png',
            'flag_b' => $countryB->flag ?? 'https://flagcdn.com/w80/'.strtolower($countryB->country_code).'.png',
            'capital_a' => $countryA->capital ?? '-',
            'capital_b' => $countryB->capital ?? '-',
            'region_a' => $countryA->region ?? '-',
            'region_b' => $countryB->region ?? '-',
            'population_a' => $countryA->population ?? 0,
            'population_b' => $countryB->population ?? 0,
            'currency_code_a' => $countryA->currency_code ?? '-',
            'currency_code_b' => $countryB->currency_code ?? '-',
            'currency_name_a' => $countryA->currency_name ?? '-',
            'currency_name_b' => $countryB->currency_name ?? '-',
            'language_a' => $countryA->language ?? '-',
            'language_b' => $countryB->language ?? '-',
            'gdp_a' => $countryA->economicData ? $countryA->economicData->gdp : 0,
            'gdp_b' => $countryB->economicData ? $countryB->economicData->gdp : 0,
            'formatted_gdp_a' => $countryA->economicData ? $countryA->economicData->formatted_gdp : '-',
            'formatted_gdp_b' => $countryB->economicData ? $countryB->economicData->formatted_gdp : '-',
            'formatted_exports_a' => $countryA->economicData ? $countryA->economicData->formatted_exports : '-',
            'formatted_exports_b' => $countryB->economicData ? $countryB->economicData->formatted_exports : '-',
            'formatted_imports_a' => $countryA->economicData ? $countryA->economicData->formatted_imports : '-',
            'formatted_imports_b' => $countryB->economicData ? $countryB->economicData->formatted_imports : '-',
            'inflation_a' => $countryA->economicData ? $countryA->economicData->inflation : 0,
            'inflation_b' => $countryB->economicData ? $countryB->economicData->inflation : 0,
            'temp_a' => $countryA->latestWeather ? $countryA->latestWeather->temperature : '-',
            'temp_b' => $countryB->latestWeather ? $countryB->latestWeather->temperature : '-',
            'rain_a' => $countryA->latestWeather ? $countryA->latestWeather->precipitation : '-',
            'rain_b' => $countryB->latestWeather ? $countryB->latestWeather->precipitation : '-',
            'wind_a' => $countryA->latestWeather ? $countryA->latestWeather->wind_speed : '-',
            'wind_b' => $countryB->latestWeather ? $countryB->latestWeather->wind_speed : '-',
            'weather_status_a' => $countryA->latestWeather ? $countryA->latestWeather->weather_status : '-',
            'weather_status_b' => $countryB->latestWeather ? $countryB->latestWeather->weather_status : '-',
            'exchange_a' => $countryA->latestCurrency ? $countryA->latestCurrency->exchange_rate : '-',
            'exchange_b' => $countryB->latestCurrency ? $countryB->latestCurrency->exchange_rate : '-',
            'currency_status_a' => $countryA->latestCurrency ? $countryA->latestCurrency->status : '-',
            'currency_status_b' => $countryB->latestCurrency ? $countryB->latestCurrency->status : '-',
            'ports_count_a' => $countryA->ports->count(),
            'ports_count_b' => $countryB->ports->count(),
            'main_port_a' => $countryA->ports->first() ? $countryA->ports->first()->port_name : '-',
            'main_port_b' => $countryB->ports->first() ? $countryB->ports->first()->port_name : '-',
            'news_sentiment_a' => $newsListA->count() > 0 ? $newsListA->avg('sentiment_score') : 0,
            'news_sentiment_b' => $newsListB->count() > 0 ? $newsListB->avg('sentiment_score') : 0,
            'risk_weather_a' => $countryA->latestRisk ? $countryA->latestRisk->weather_score : 100,
            'risk_weather_b' => $countryB->latestRisk ? $countryB->latestRisk->weather_score : 100,
            'risk_currency_a' => $countryA->latestRisk ? $countryA->latestRisk->currency_score : 100,
            'risk_currency_b' => $countryB->latestRisk ? $countryB->latestRisk->currency_score : 100,
            'risk_inflation_a' => $countryA->latestRisk ? $countryA->latestRisk->inflation_score : 100,
            'risk_inflation_b' => $countryB->latestRisk ? $countryB->latestRisk->inflation_score : 100,
            'risk_political_a' => $countryA->latestRisk ? $countryA->latestRisk->political_score : 100,
            'risk_political_b' => $countryB->latestRisk ? $countryB->latestRisk->political_score : 100,
            'risk_economic_a' => $countryA->latestRisk ? $countryA->latestRisk->economic_score : 100,
            'risk_economic_b' => $countryB->latestRisk ? $countryB->latestRisk->economic_score : 100,
            'risk_news_a' => $countryA->latestRisk ? $countryA->latestRisk->news_score : 100,
            'risk_news_b' => $countryB->latestRisk ? $countryB->latestRisk->news_score : 100,
            'risk_port_a' => $countryA->latestRisk ? $countryA->latestRisk->port_score : 100,
            'risk_port_b' => $countryB->latestRisk ? $countryB->latestRisk->port_score : 100,
            'risk_final_a' => $countryA->latestRisk ? $countryA->latestRisk->final_score : 100,
            'risk_final_b' => $countryB->latestRisk ? $countryB->latestRisk->final_score : 100,
            'winner_name' => $recommendation['winner'] ? $recommendation['winner']->country_name : '',
            'winner_flag' => $recommendation['winner'] ? $recommendation['winner']->flag : '',
            'reasons' => $recommendation['reasons'],
            'conclusion' => $recommendation['conclusion'] ?? ''
        ];
    }

    public function compareLivePdf(Request $request)
    {
        $res = $this->getLiveComparisonData($request);
        $pdf = Pdf::loadView('reports.exports.compare-live', compact('res'));
        $fileName = 'country-comparison-' . strtolower(str_replace(' ', '-', $res['country_name_a'])) . '-' . strtolower(str_replace(' ', '-', $res['country_name_b'])) . '.pdf';
        return $pdf->download($fileName);
    }

    public function compareLiveExcel(Request $request)
    {
        $res = $this->getLiveComparisonData($request);
        $fileName = 'country-comparison-' . strtolower(str_replace(' ', '-', $res['country_name_a'])) . '-' . strtolower(str_replace(' ', '-', $res['country_name_b'])) . '.xlsx';
        return Excel::download(new \App\Exports\CompareLiveExport($res), $fileName);
    }
EOD;

$content = preg_replace('/class ReportExportController extends Controller\s*\{/', "class ReportExportController extends Controller\n{\n" . $newMethods, $content);
file_put_contents($file, $content);
echo "Added compareLivePdf and compareLiveExcel to ReportExportController.\n";
