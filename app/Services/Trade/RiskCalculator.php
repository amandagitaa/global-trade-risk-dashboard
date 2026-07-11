<?php

namespace App\Services\Trade;

use App\Models\Country;
use App\Models\RiskScore;

class RiskCalculator
{
    /**
     * Hitung seluruh risk score sebuah negara
     */
    public function calculate(Country $country): RiskScore
    {
        $weather = $this->weatherScore($country);

        $currency = $this->currencyScore($country);

        $port = $this->portScore($country);

        $news = $this->newsScore($country);

        $economy = $this->economyScore($country);

        $finalScore = $this->calculateFinalScore(

            $weather['score'],

            $currency['score'],

            $port['score'],

            $news['score'],

            $economy['score']

        );

        $riskLevel = $this->determineRiskLevel($finalScore);

        $reason = $this->buildReason([

            $weather,

            $currency,

            $port,

            $news,

            $economy

        ]);

        return RiskScore::updateOrCreate(

            [

                'country_id' => $country->id

            ],

            [

                'weather_score' => $weather['score'],

                'currency_score' => $currency['score'],

                'port_score' => $port['score'],

                'news_score' => $news['score'],

                'economic_score' => $economy['score'],

                'final_score' => round($finalScore,2),

                'risk_level' => $riskLevel,

                'reason' => $reason

            ]

        );
    }

    /**
     * ==========================================================
     * WEATHER
     * ==========================================================
     */

    private function weatherScore(Country $country): array
    {
        $weather = $country->latestWeather;

        if (!$weather) {

            return [

                'score' => 30,

                'reason' => 'Weather data unavailable.'

            ];

        }

        $status = strtolower($weather->weather_status);

        $score = config(
            "trade.weather_score.$status",
            30
        );

        return [

            'score' => $score,

            'reason' => match ($status) {

                'clear' => 'Stable weather condition.',

                'cloudy' => 'Cloudy weather detected.',

                'rain' => 'Rain may affect logistics.',

                'heavy_rain' => 'Heavy rain affects transportation.',

                'storm' => 'Storm detected.',

                default => 'Unknown weather condition.'

            }

        ];
    }

        /**
     * ==========================================================
     * CURRENCY
     * ==========================================================
     */

    private function currencyScore(Country $country): array
    {
        $currency = $country->latestCurrency;

        if (!$currency) {

            return [

                'score' => 30,

                'reason' => 'Currency data unavailable.'

            ];

        }

        $change = abs($currency->change_percentage);

        $score = 100;

        foreach (config('trade.currency_score') as $rule) {

            if ($change <= $rule['max']) {

                $score = $rule['score'];

                break;

            }

        }

        $reason = match (true) {

            $change <= 2 => 'Stable exchange rate.',

            $change <= 5 => 'Minor exchange rate fluctuation.',

            $change <= 8 => 'High exchange rate volatility.',

            default => 'Extreme currency volatility detected.'

        };

        return [

            'score' => $score,

            'reason' => $reason

        ];
    }

    /**
     * ==========================================================
     * PORT
     * ==========================================================
     */

    private function portScore(Country $country): array
    {
        $port = $country->ports()

            ->orderByDesc('congestion_score')

            ->first();

        if (!$port) {

            return [

                'score' => 30,

                'reason' => 'Port data unavailable.'

            ];

        }

        $score = min(100, max(0, $port->congestion_score));

        $reason = match (true) {

            $score <= 20 => 'Port operates normally.',

            $score <= 40 => 'Minor port congestion.',

            $score <= 60 => 'Moderate port congestion.',

            $score <= 80 => 'Heavy port congestion.',

            default => 'Critical port congestion.'

        };

        return [

            'score' => $score,

            'reason' => $reason

        ];
    }

    /**
     * ==========================================================
     * NEWS
     * ==========================================================
     */

    private function newsScore(Country $country): array
    {
        $news = $country->latestNews;

        if (!$news) {

            return [

                'score' => 30,

                'reason' => 'News data unavailable.'

            ];

        }

        $sentiment = strtolower($news->sentiment);

        $score = config(

            "trade.news_score.$sentiment",

            40

        );

        $reason = match ($sentiment) {

            'positive' => 'Positive trade sentiment.',

            'neutral' => 'Neutral market sentiment.',

            'negative' => 'Negative geopolitical news detected.',

            default => 'Unknown market sentiment.'

        };

        return [

            'score' => $score,

            'reason' => $reason

        ];
    }

    /**
     * ==========================================================
     * ECONOMY
     * ==========================================================
     */

    private function economyScore(Country $country): array
    {
        return [

            'score' => 50,

            'reason' => 'Economic indicator is not available yet.'

        ];
    }

        /**
     * ==========================================================
     * FINAL SCORE
     * ==========================================================
     */

    private function calculateFinalScore(
        float $weather,
        float $currency,
        float $port,
        float $news,
        float $economy
    ): float {

        $weight = config('trade.weights');

        return (

            ($weather * $weight['weather']) +

            ($currency * $weight['currency']) +

            ($port * $weight['port']) +

            ($news * $weight['news']) +

            ($economy * $weight['economy'])

        ) / 100;

    }

    /**
     * ==========================================================
     * RISK LEVEL
     * ==========================================================
     */

    private function determineRiskLevel(float $score): string
    {
        foreach (config('trade.risk_level') as $rule) {

            if ($score <= $rule['max']) {

                return $rule['level'];

            }

        }

        return 'critical';
    }

    /**
     * ==========================================================
     * BUILD REASON
     * ==========================================================
     */

    private function buildReason(array $items): string
    {
        $reasons = [];

        foreach ($items as $item) {

            if (!empty($item['reason'])) {

                $reasons[] = $item['reason'];

            }

        }

        return implode(' ', array_unique($reasons));
    }

    /**
     * ==========================================================
     * CALCULATE ALL COUNTRIES
     * ==========================================================
     */

    public function calculateAll(): void
    {
        Country::with([

            'latestWeather',

            'latestCurrency',

            'latestNews',

            'ports'

        ])

        ->chunk(25, function ($countries) {

            foreach ($countries as $country) {

                $this->calculate($country);

            }

        });
    }

}