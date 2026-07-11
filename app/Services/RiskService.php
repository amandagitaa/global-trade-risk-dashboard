<?php

namespace App\Services;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\RiskHistory;
use App\Models\WeatherData;
use App\Models\CurrencyRate;
use App\Models\EconomicData;
use App\Models\NewsCache;
use App\Models\Port;

class RiskService
{
    public function calculate(Country $country)
    {
        /*
        |--------------------------------------------------------------------------
        | WEATHER
        |--------------------------------------------------------------------------
        */

        $weather = WeatherData::where('country_id', $country->id)
            ->latest()
            ->first();

        $weatherScore = $weather
            ? $weather->storm_risk
            : 10;

        /*
        |--------------------------------------------------------------------------
        | CURRENCY
        |--------------------------------------------------------------------------
        */

        $currency = CurrencyRate::where('country_id', $country->id)
            ->latest()
            ->first();

        $currencyScore = $this->calculateCurrencyScore($currency);

        /*
        |--------------------------------------------------------------------------
        | NEWS
        |--------------------------------------------------------------------------
        */

        $news = NewsCache::where('country_id', $country->id)
            ->latest()
            ->first();

        $newsScore = $this->calculateNewsScore($news);

        /*
        |--------------------------------------------------------------------------
        | ECONOMY
        |--------------------------------------------------------------------------
        */

        $economy = EconomicData::where('country_id', $country->id)
            ->latest()
            ->first();

        $economicScore = $economy
            ? $economy->economic_risk
            : 20;

        /*
        |--------------------------------------------------------------------------
        | PORT
        |--------------------------------------------------------------------------
        */

        $port = Port::where('country_id', $country->id)
            ->orderByDesc('congestion_score')
            ->first();

        $portScore = $port
            ? $port->congestion_score
            : 20;

        /*
        |--------------------------------------------------------------------------
        | FINAL SCORE
        |--------------------------------------------------------------------------
        */

        $finalScore = (

            ($weatherScore * 0.25) +

            ($portScore * 0.20) +

            ($currencyScore * 0.20) +

            ($economicScore * 0.15) +

            ($newsScore * 0.20)

        );

        $riskLevel = $this->determineRiskLevel($finalScore);

        /*
        |--------------------------------------------------------------------------
        | SAVE RISK
        |--------------------------------------------------------------------------
        */

        RiskScore::updateOrCreate(

            [
                'country_id' => $country->id
            ],

            [

                'weather_score' => $weatherScore,

                'port_score' => $portScore,

                'currency_score' => $currencyScore,

                'economic_score' => $economicScore,

                'news_score' => $newsScore,

                'final_score' => round($finalScore,2),

                'risk_level' => $riskLevel,

                'reason' => 'Calculated from Weather, Currency, Economy, News and Port'

            ]

        );

        /*
        |--------------------------------------------------------------------------
        | HISTORY
        |--------------------------------------------------------------------------
        */

        RiskHistory::create([

            'country_id' => $country->id,

            'risk_score' => round($finalScore,2),

            'record_date' => now()->toDateString()

        ]);

        /*
        |--------------------------------------------------------------------------
        | NEXT STEP
        |--------------------------------------------------------------------------
        */

        // app(RecommendationService::class)->generate($country);

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | CURRENCY SCORE
    |--------------------------------------------------------------------------
    */

    private function calculateCurrencyScore($currency)
    {
        if (!$currency) {
            return 10;
        }

        $change = abs($currency->change_percentage);

        if ($change <= 2) {
            return 10;
        }

        if ($change <= 5) {
            return 35;
        }

        if ($change <= 8) {
            return 65;
        }

        return 95;
    }

    /*
    |--------------------------------------------------------------------------
    | NEWS SCORE
    |--------------------------------------------------------------------------
    */

    private function calculateNewsScore($news)
    {
        if (!$news) {
            return 20;
        }

        return match ($news->sentiment) {

            'positive' => 10,

            'neutral' => 40,

            'negative' => 80,

            default => 20

        };
    }

    /*
    |--------------------------------------------------------------------------
    | RISK LEVEL
    |--------------------------------------------------------------------------
    */

    private function determineRiskLevel($score)
    {

        if ($score <= 20) {

            return 'safe';

        }

        if ($score <= 40) {

            return 'stable';

        }

        if ($score <= 60) {

            return 'alert';

        }

        if ($score <= 80) {

            return 'dangerous';

        }

        return 'critical';
    }
}