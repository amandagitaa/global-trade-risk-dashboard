<?php

namespace App\Services\Trade;

use App\Models\Country;
use App\Models\WeatherData;
use App\Models\CurrencyRate;
use App\Models\EconomicData;
use App\Models\Port;
use App\Models\NewsCache;
use App\Models\RiskScore;

class RiskAnalysisService
{
    /*
    |--------------------------------------------------------------------------
    | WEATHER RISK
    |--------------------------------------------------------------------------
    */

    private function calculateWeatherRisk(?WeatherData $weather): float
    {
        if (!$weather) {
            return 20;
        }

        $risk = 0;

        $condition = strtolower($weather->weather ?? '');

        if (str_contains($condition, 'storm')) {

            $risk += 40;

        } elseif (str_contains($condition, 'rain')) {

            $risk += 20;

        } elseif (str_contains($condition, 'cloud')) {

            $risk += 10;

        } elseif (str_contains($condition, 'clear')) {

            $risk += 0;

        }

        /*
        |--------------------------------------------------------------------------
        | Wind Speed
        |--------------------------------------------------------------------------
        */

        $wind = $weather->wind_speed ?? 0;

        if ($wind >= 60) {

            $risk += 30;

        } elseif ($wind >= 40) {

            $risk += 20;

        } elseif ($wind >= 20) {

            $risk += 10;

        }

        /*
        |--------------------------------------------------------------------------
        | Temperature
        |--------------------------------------------------------------------------
        */

        $temperature = $weather->temperature ?? 0;

        if ($temperature >= 40) {

            $risk += 20;

        } elseif ($temperature <= 0) {

            $risk += 10;

        }

        return max(0, min(100, $risk));
    }

    /*
    |--------------------------------------------------------------------------
    | CURRENCY RISK
    |--------------------------------------------------------------------------
    */

    private function calculateCurrencyRisk(?CurrencyRate $currency): float
    {
        if (!$currency) {

            return 20;

        }

        $risk = 0;

        /*
        |--------------------------------------------------------------------------
        | Exchange Rate Change
        |--------------------------------------------------------------------------
        */

        $change = abs($currency->change_percentage ?? 0);

        if ($change <= 2) {

            $risk += 10;

        } elseif ($change <= 5) {

            $risk += 30;

        } elseif ($change <= 8) {

            $risk += 55;

        } else {

            $risk += 80;

        }

        /*
        |--------------------------------------------------------------------------
        | Volatility
        |--------------------------------------------------------------------------
        */

        $volatility = strtolower($currency->volatility ?? '');

        switch ($volatility) {

            case 'low':

                $risk -= 5;

                break;

            case 'medium':

                $risk += 5;

                break;

            case 'high':

                $risk += 15;

                break;

        }

        return max(0, min(100, $risk));
    }

        /*
    |--------------------------------------------------------------------------
    | ECONOMY RISK
    |--------------------------------------------------------------------------
    */

    private function calculateEconomyRisk(?EconomicData $economy): float
    {
        if (!$economy) {
            return 50;
        }

        $risk = 50;

        /*
        |--------------------------------------------------------------------------
        | GDP
        |--------------------------------------------------------------------------
        */

        $gdp = $economy->gdp ?? 0;

        if ($gdp >= 10000000000000) {

            $risk -= 20;

        } elseif ($gdp >= 5000000000000) {

            $risk -= 15;

        } elseif ($gdp >= 1000000000000) {

            $risk -= 10;

        } elseif ($gdp >= 500000000000) {

            $risk -= 5;

        } else {

            $risk += 8;

        }

        /*
        |--------------------------------------------------------------------------
        | Inflation
        |--------------------------------------------------------------------------
        */

        $inflation = $economy->inflation ?? 0;

        if ($inflation >= 12) {

            $risk += 20;

        } elseif ($inflation >= 8) {

            $risk += 12;

        } elseif ($inflation >= 5) {

            $risk += 6;

        } elseif ($inflation <= 2) {

            $risk -= 5;

        }

        /*
        |--------------------------------------------------------------------------
        | Trade Balance
        |--------------------------------------------------------------------------
        */

        $exports = $economy->exports ?? 0;

        $imports = $economy->imports ?? 0;

        $balance = $exports - $imports;

        if ($balance > 0) {

            $risk -= 8;

        } elseif ($balance < 0) {

            $risk += 8;

        }

        return max(0, min(100, $risk));
    }

    /*
    |--------------------------------------------------------------------------
    | PORT RISK
    |--------------------------------------------------------------------------
    */

    private function calculatePortRisk(?Port $port): float
    {
        if (!$port) {
            return 20;
        }

        $risk = 0;

        /*
        |--------------------------------------------------------------------------
        | Congestion
        |--------------------------------------------------------------------------
        */

        $congestion = $port->congestion ?? 0;

        if ($congestion >= 90) {

            $risk += 40;

        } elseif ($congestion >= 70) {

            $risk += 30;

        } elseif ($congestion >= 50) {

            $risk += 20;

        } elseif ($congestion >= 30) {

            $risk += 10;

        }

        /*
        |--------------------------------------------------------------------------
        | Capacity
        |--------------------------------------------------------------------------
        */

        $capacity = $port->capacity ?? 100;

        if ($capacity < 30) {

            $risk += 20;

        } elseif ($capacity < 60) {

            $risk += 10;

        }

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        $status = strtolower($port->status ?? '');

        switch ($status) {

            case 'closed':

                $risk += 40;

                break;

            case 'limited':

                $risk += 20;

                break;

            case 'maintenance':

                $risk += 15;

                break;

            case 'congested':

                $risk += 10;

                break;

        }

        return max(0, min(100, $risk));
    }

        /*
    |--------------------------------------------------------------------------
    | NEWS RISK
    |--------------------------------------------------------------------------
    */

    private function calculateNewsRisk(?NewsCache $news): float
    {
        if (!$news) {
            return 20;
        }

        $sentiment = strtolower($news->sentiment ?? 'neutral');

        return match ($sentiment) {

            'positive' => 10,

            'neutral' => 40,

            'negative' => 80,

            default => 20

        };
    }

    /*
    |--------------------------------------------------------------------------
    | FINAL RISK
    |--------------------------------------------------------------------------
    */

    private function calculateFinalRisk(
        float $weather,
        float $currency,
        float $economy,
        float $port,
        float $news
    ): float {

        return round(

            ($weather * 0.25) +

            ($currency * 0.20) +

            ($economy * 0.15) +

            ($port * 0.20) +

            ($news * 0.20),

            2

        );
    }

    /*
    |--------------------------------------------------------------------------
    | RISK LEVEL
    |--------------------------------------------------------------------------
    */

    private function determineRiskLevel(float $score): string
    {
        return match (true) {

            $score <= 20 => 'safe',

            $score <= 40 => 'stable',

            $score <= 60 => 'alert',

            $score <= 80 => 'dangerous',

            default => 'critical',

        };
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE RISK SCORE
    |--------------------------------------------------------------------------
    */

    private function saveRisk(
        Country $country,
        array $risk
    ): void {

        RiskScore::updateOrCreate(

            [

                'country_id' => $country->id

            ],

            [

                'weather_score' => $risk['weather_score'],

                'currency_score' => $risk['currency_score'],

                'economic_score' => $risk['economy_score'],

                'port_score' => $risk['port_score'],

                'news_score' => $risk['news_score'],

                'final_score' => $risk['final_score'],

                'risk_level' => $risk['risk_level'],

                'calculated_at' => now(),

            ]

        );

    }

        /*
    |--------------------------------------------------------------------------
    | MAIN ANALYSIS
    |--------------------------------------------------------------------------
    */

    public function calculate(Country $country): array
    {
        /*
        |--------------------------------------------------------------------------
        | Load Relations
        |--------------------------------------------------------------------------
        */

        $country->loadMissing([

            'latestWeather',

            'latestCurrency',

            'latestNews',

            'economicData',

            'ports'

        ]);

        /*
        |--------------------------------------------------------------------------
        | Get Data
        |--------------------------------------------------------------------------
        */

        $weather = $country->latestWeather;

        $currency = $country->latestCurrency;

        $economy = $country->economicData;

        $port = $country->ports
            ->sortByDesc('congestion')
            ->first();

        $news = $country->latestNews;

        /*
        |--------------------------------------------------------------------------
        | Calculate Risk Components
        |--------------------------------------------------------------------------
        */

        $weatherRisk = $this->calculateWeatherRisk($weather);

        $currencyRisk = $this->calculateCurrencyRisk($currency);

        $economyRisk = $this->calculateEconomyRisk($economy);

        $portRisk = $this->calculatePortRisk($port);

        $newsRisk = $this->calculateNewsRisk($news);

        /*
        |--------------------------------------------------------------------------
        | Final Score
        |--------------------------------------------------------------------------
        */

        $finalRisk = $this->calculateFinalRisk(

            $weatherRisk,

            $currencyRisk,

            $economyRisk,

            $portRisk,

            $newsRisk

        );

        /*
        |--------------------------------------------------------------------------
        | Risk Level
        |--------------------------------------------------------------------------
        */

        $riskLevel = $this->determineRiskLevel($finalRisk);

        /*
        |--------------------------------------------------------------------------
        | Result
        |--------------------------------------------------------------------------
        */

        $result = [

            'weather_score' => round($weatherRisk, 2),

            'currency_score' => round($currencyRisk, 2),

            'economy_score' => round($economyRisk, 2),

            'port_score' => round($portRisk, 2),

            'news_score' => round($newsRisk, 2),

            'final_score' => round($finalRisk, 2),

            'risk_level' => $riskLevel,

            'reason' => implode("\n", [

                'Weather Risk : '.round($weatherRisk,2),

                'Currency Risk : '.round($currencyRisk,2),

                'Economy Risk : '.round($economyRisk,2),

                'Port Risk : '.round($portRisk,2),

                'News Risk : '.round($newsRisk,2),

            ])

        ];

        /*
        |--------------------------------------------------------------------------
        | Save Cache
        |--------------------------------------------------------------------------
        */

        $this->saveRisk($country, $result);

        /*
        |--------------------------------------------------------------------------
        | Return
        |--------------------------------------------------------------------------
        */

        return $result;
    }

}