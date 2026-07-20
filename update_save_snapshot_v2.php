<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\app\Http\Controllers\CountryComparisonController.php';
$content = file_get_contents($file);

$pattern = "/\\\$comparisonResult = \\\[(.*?)\\\];/s";

$newComparisonResult = <<<'EOD'
$comparisonResult = [
            // Country Info
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

            // Economy Comparison
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
            'export_a' => $countryA->economicData ? $countryA->economicData->exports : 0,
            'export_b' => $countryB->economicData ? $countryB->economicData->exports : 0,
            'import_a' => $countryA->economicData ? $countryA->economicData->imports : 0,
            'import_b' => $countryB->economicData ? $countryB->economicData->imports : 0,
            'gdp_per_capita_a' => ($countryA->economicData && $countryA->population > 0) ? ($countryA->economicData->gdp / $countryA->population) : 0,
            'gdp_per_capita_b' => ($countryB->economicData && $countryB->population > 0) ? ($countryB->economicData->gdp / $countryB->population) : 0,

            // Weather Comparison
            'temp_a' => $countryA->latestWeather ? $countryA->latestWeather->temperature : '-',
            'temp_b' => $countryB->latestWeather ? $countryB->latestWeather->temperature : '-',
            'rain_a' => $countryA->latestWeather ? $countryA->latestWeather->precipitation : '-',
            'rain_b' => $countryB->latestWeather ? $countryB->latestWeather->precipitation : '-',
            'wind_a' => $countryA->latestWeather ? $countryA->latestWeather->wind_speed : '-',
            'wind_b' => $countryB->latestWeather ? $countryB->latestWeather->wind_speed : '-',
            'storm_a' => $countryA->latestWeather ? $countryA->latestWeather->extreme_weather_alerts : 'None',
            'storm_b' => $countryB->latestWeather ? $countryB->latestWeather->extreme_weather_alerts : 'None',
            'weather_status_a' => $countryA->latestWeather ? $countryA->latestWeather->weather_status : '-',
            'weather_status_b' => $countryB->latestWeather ? $countryB->latestWeather->weather_status : '-',
            'weather_a' => $countryA->latestRisk ? $countryA->latestRisk->weather_score : 100,
            'weather_b' => $countryB->latestRisk ? $countryB->latestRisk->weather_score : 100,

            // Currency Comparison
            'exchange_a' => $countryA->latestCurrency ? $countryA->latestCurrency->exchange_rate : '-',
            'exchange_b' => $countryB->latestCurrency ? $countryB->latestCurrency->exchange_rate : '-',
            'currency_status_a' => $countryA->latestCurrency ? $countryA->latestCurrency->status : '-',
            'currency_status_b' => $countryB->latestCurrency ? $countryB->latestCurrency->status : '-',
            'currency_a' => $countryA->latestCurrency ? $countryA->latestCurrency->status : '-',
            'currency_b' => $countryB->latestCurrency ? $countryB->latestCurrency->status : '-',

            // Port Comparison
            'ports_count_a' => $countryA->ports->count(),
            'ports_count_b' => $countryB->ports->count(),
            'ports_avg_risk_a' => $countryA->ports->count() ? number_format($countryA->ports->avg('risk_score'), 0) : '-',
            'ports_avg_risk_b' => $countryB->ports->count() ? number_format($countryB->ports->avg('risk_score'), 0) : '-',
            'main_port_a' => $countryA->ports->first() ? $countryA->ports->first()->name : '-',
            'main_port_b' => $countryB->ports->first() ? $countryB->ports->first()->name : '-',
            'shipping_status_a' => 'Normal',
            'shipping_status_b' => 'Normal',

            // News Comparison
            'news_list_a' => $countryA->latestNews->take(5)->map(fn($n) => ['title' => $n->title, 'published_at' => $n->published_at, 'sentiment' => $n->sentiment])->toArray(),
            'news_list_b' => $countryB->latestNews->take(5)->map(fn($n) => ['title' => $n->title, 'published_at' => $n->published_at, 'sentiment' => $n->sentiment])->toArray(),
            'news_title_a' => $countryA->latestNews->first() ? $countryA->latestNews->first()->title : '-',
            'news_title_b' => $countryB->latestNews->first() ? $countryB->latestNews->first()->title : '-',
            'news_sentiment_a' => count($countryA->latestNews) > 0 ? $countryA->latestNews->avg('sentiment_score') : 0,
            'news_sentiment_b' => count($countryB->latestNews) > 0 ? $countryB->latestNews->avg('sentiment_score') : 0,

            // Risk Comparison
            'risk_level_a' => $countryA->latestRisk ? $countryA->latestRisk->risk_level : 'Unknown',
            'risk_level_b' => $countryB->latestRisk ? $countryB->latestRisk->risk_level : 'Unknown',
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
            
            // Recommendation
            'winner_name' => $recommendation['winner'] ? $recommendation['winner']->country_name : '',
            'winner_flag' => $recommendation['winner'] ? $recommendation['winner']->flag : '',
            'reasons' => $recommendation['reasons'],
            'conclusion' => $recommendation['conclusion'] ?? ''
        ];
EOD;

$content = preg_replace($pattern, $newComparisonResult, $content);
file_put_contents($file, $content);
echo "CountryComparisonController updated with EXACT snapshot JSON payload.\n";
