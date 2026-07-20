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
            'flag_a' => strtolower($countryA->country_code),
            'flag_b' => strtolower($countryB->country_code),
            'capital_a' => $countryA->capital ?? 'N/A',
            'capital_b' => $countryB->capital ?? 'N/A',
            'region_a' => $countryA->region ?? 'N/A',
            'region_b' => $countryB->region ?? 'N/A',
            'population_a' => $countryA->population ?? 0,
            'population_b' => $countryB->population ?? 0,
            'currency_code_a' => $countryA->currency_code ?? 'N/A',
            'currency_code_b' => $countryB->currency_code ?? 'N/A',
            'currency_name_a' => $countryA->currency_name ?? 'N/A',
            'currency_name_b' => $countryB->currency_name ?? 'N/A',
            'language_a' => $countryA->language ?? 'N/A',
            'language_b' => $countryB->language ?? 'N/A',

            // Economy Comparison
            'gdp_a' => $countryA->economicData ? $countryA->economicData->gdp : 0,
            'gdp_b' => $countryB->economicData ? $countryB->economicData->gdp : 0,
            'inflation_a' => $countryA->economicData ? $countryA->economicData->inflation : 0,
            'inflation_b' => $countryB->economicData ? $countryB->economicData->inflation : 0,
            'export_a' => $countryA->economicData ? $countryA->economicData->exports : 0,
            'export_b' => $countryB->economicData ? $countryB->economicData->exports : 0,
            'import_a' => $countryA->economicData ? $countryA->economicData->imports : 0,
            'import_b' => $countryB->economicData ? $countryB->economicData->imports : 0,
            'gdp_per_capita_a' => ($countryA->economicData && $countryA->population > 0) ? ($countryA->economicData->gdp / $countryA->population) : 0,
            'gdp_per_capita_b' => ($countryB->economicData && $countryB->population > 0) ? ($countryB->economicData->gdp / $countryB->population) : 0,

            // Weather Comparison
            'temp_a' => $countryA->latestWeather ? $countryA->latestWeather->temperature : 0,
            'temp_b' => $countryB->latestWeather ? $countryB->latestWeather->temperature : 0,
            'rain_a' => $countryA->latestWeather ? $countryA->latestWeather->precipitation : 0,
            'rain_b' => $countryB->latestWeather ? $countryB->latestWeather->precipitation : 0,
            'wind_a' => $countryA->latestWeather ? $countryA->latestWeather->wind_speed : 0,
            'wind_b' => $countryB->latestWeather ? $countryB->latestWeather->wind_speed : 0,
            'storm_a' => $countryA->latestWeather ? $countryA->latestWeather->extreme_weather_alerts : 'None',
            'storm_b' => $countryB->latestWeather ? $countryB->latestWeather->extreme_weather_alerts : 'None',
            'weather_status_a' => $countryA->latestWeather ? $countryA->latestWeather->weather_status : 'Clear',
            'weather_status_b' => $countryB->latestWeather ? $countryB->latestWeather->weather_status : 'Clear',
            'weather_a' => $countryA->latestRisk ? $countryA->latestRisk->weather_score : 100,
            'weather_b' => $countryB->latestRisk ? $countryB->latestRisk->weather_score : 100,

            // Currency Comparison
            'exchange_a' => $countryA->latestCurrency ? $countryA->latestCurrency->exchange_rate : 0,
            'exchange_b' => $countryB->latestCurrency ? $countryB->latestCurrency->exchange_rate : 0,
            'currency_status_a' => $countryA->latestCurrency ? $countryA->latestCurrency->status : 'Unknown',
            'currency_status_b' => $countryB->latestCurrency ? $countryB->latestCurrency->status : 'Unknown',
            'currency_a' => $countryA->latestCurrency ? $countryA->latestCurrency->status : 'Unknown',
            'currency_b' => $countryB->latestCurrency ? $countryB->latestCurrency->status : 'Unknown',

            // Port Comparison
            'ports_a' => $countryA->ports->count(),
            'ports_b' => $countryB->ports->count(),
            'main_port_a' => $countryA->ports->first() ? $countryA->ports->first()->port_name : 'N/A',
            'main_port_b' => $countryB->ports->first() ? $countryB->ports->first()->port_name : 'N/A',
            'shipping_status_a' => 'Normal',
            'shipping_status_b' => 'Normal',

            // News Comparison
            'news_title_a' => $countryA->latestNews->first() ? $countryA->latestNews->first()->title : 'No recent news',
            'news_title_b' => $countryB->latestNews->first() ? $countryB->latestNews->first()->title : 'No recent news',
            'news_sentiment_a' => count($countryA->latestNews) > 0 ? $countryA->latestNews->avg('sentiment_score') : 0,
            'news_sentiment_b' => count($countryB->latestNews) > 0 ? $countryB->latestNews->avg('sentiment_score') : 0,

            // Risk Comparison
            'risk_weather_a' => $countryA->latestRisk ? $countryA->latestRisk->weather_score : 100,
            'risk_weather_b' => $countryB->latestRisk ? $countryB->latestRisk->weather_score : 100,
            'risk_currency_a' => $countryA->latestRisk ? $countryA->latestRisk->currency_score : 100,
            'risk_currency_b' => $countryB->latestRisk ? $countryB->latestRisk->currency_score : 100,
            'risk_inflation_a' => $countryA->latestRisk ? $countryA->latestRisk->inflation_score : 100,
            'risk_inflation_b' => $countryB->latestRisk ? $countryB->latestRisk->inflation_score : 100,
            'risk_political_a' => $countryA->latestRisk ? $countryA->latestRisk->political_score : 100,
            'risk_political_b' => $countryB->latestRisk ? $countryB->latestRisk->political_score : 100,
            
            // Recommendation
            'reasons' => $recommendation['reasons']
        ];
EOD;

$content = preg_replace($pattern, $newComparisonResult, $content);
file_put_contents($file, $content);
echo "CountryComparisonController updated with FULL snapshot JSON payload.\n";
