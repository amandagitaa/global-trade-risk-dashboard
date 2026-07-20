<?php

namespace App\Services;

use App\Models\Country;
use App\Models\TradeRecommendation;
use App\Services\Trade\RiskAnalysisService;

class RecommendationService
{
    protected RiskAnalysisService $riskAnalysis;

    public function __construct(
        RiskAnalysisService $riskAnalysis
    ) {
        $this->riskAnalysis = $riskAnalysis;
    }

    public function generate(Country $country): bool
    {
        /*
        |--------------------------------------------------------------------------
        | Load Relations
        |--------------------------------------------------------------------------
        */

        $country->load([

            'latestWeather',

            'latestCurrency',

            'latestNews',

            'ports',

            'economicData'

        ]);

        /*
        |--------------------------------------------------------------------------
        | Risk Analysis
        |--------------------------------------------------------------------------
        */

        $risk = $this->riskAnalysis->calculate($country);

        $finalScore = $risk['final_score'];

        $riskLevel = $risk['risk_level'];

        /*
        |--------------------------------------------------------------------------
        | Prepare Variables
        |--------------------------------------------------------------------------
        */

        $tradeAction = 'Maintain Trade';

        $priority = 'Medium';

        $recommendations = [];

        $reasons = [];

        /*
        |--------------------------------------------------------------------------
        | Main Recommendation
        |--------------------------------------------------------------------------
        */

        if ($finalScore <= 20) {

            $tradeAction = 'Expand Trade';

            $priority = 'Low';

        } elseif ($finalScore <= 40) {

            $tradeAction = 'Maintain Trade';

            $priority = 'Medium';

        } elseif ($finalScore <= 60) {

            $tradeAction = 'Monitor Trade';

            $priority = 'Medium';

        } elseif ($finalScore <= 80) {

            $tradeAction = 'Reduce Exposure';

            $priority = 'High';

        } else {

            $tradeAction = 'Suspend Trade';

            $priority = 'Critical';

        }

                /*
        |--------------------------------------------------------------------------
        | WEATHER RECOMMENDATION
        |--------------------------------------------------------------------------
        */

        $weather = $country->latestWeather;

        if ($weather) {

            $condition = strtolower($weather->weather ?? '');

            if (str_contains($condition, 'storm')) {

                $recommendations[] = 'Delay shipment due to storm risk.';
                $reasons[] = 'Severe storm conditions detected.';

            } elseif (str_contains($condition, 'rain')) {

                $recommendations[] = 'Prepare for possible shipping delay.';
                $reasons[] = 'Heavy rain may affect logistics.';

            } elseif (str_contains($condition, 'cloud')) {

                $recommendations[] = 'Normal shipment with weather monitoring.';
                $reasons[] = 'Cloudy weather has low impact.';

            } else {

                $recommendations[] = 'Weather conditions are favorable.';
                $reasons[] = 'No significant weather disruption detected.';

            }

        }

        /*
        |--------------------------------------------------------------------------
        | CURRENCY RECOMMENDATION
        |--------------------------------------------------------------------------
        */

        $currency = $country->latestCurrency;

        if ($currency) {

            $change = abs($currency->change_percentage ?? 0);

            if ($change >= 8) {

                $recommendations[] = 'Review import/export pricing.';
                $reasons[] = 'Exchange rate volatility is very high.';

            } elseif ($change >= 5) {

                $recommendations[] = 'Monitor exchange rate daily.';
                $reasons[] = 'Currency movement is increasing.';

            } else {

                $recommendations[] = 'Currency is relatively stable.';
                $reasons[] = 'Exchange rate fluctuation is low.';

            }

        }

        /*
        |--------------------------------------------------------------------------
        | ECONOMY RECOMMENDATION
        |--------------------------------------------------------------------------
        */

        $economy = $country->economicData;

        if ($economy) {

            if (($economy->inflation ?? 0) >= 10) {

                $recommendations[] = 'Increase price monitoring.';
                $reasons[] = 'High inflation may increase trade costs.';

            }

            if (($economy->gdp ?? 0) >= 5000000000000) {

                $recommendations[] = 'Large market opportunity.';
                $reasons[] = 'Country has a very strong economy.';

            }

            if (($economy->exports ?? 0) > ($economy->imports ?? 0)) {

                $recommendations[] = 'Trade environment is favorable.';
                $reasons[] = 'Country has a positive trade balance.';

            }

        }

        /*
        |--------------------------------------------------------------------------
        | PORT RECOMMENDATION
        |--------------------------------------------------------------------------
        */

        $port = $country->ports
            ->sortByDesc('congestion')
            ->first();

        if ($port) {

            if (($port->congestion ?? 0) >= 80) {

                $recommendations[] = 'Use an alternative port.';
                $reasons[] = 'Port congestion is extremely high.';

            } elseif (($port->congestion ?? 0) >= 50) {

                $recommendations[] = 'Expect possible loading delays.';
                $reasons[] = 'Moderate port congestion detected.';

            }

            if (strtolower($port->status ?? '') === 'closed') {

                $recommendations[] = 'Avoid this port temporarily.';
                $reasons[] = 'Port is currently closed.';

            }

        }

        /*
        |--------------------------------------------------------------------------
        | NEWS RECOMMENDATION
        |--------------------------------------------------------------------------
        */

        $news = $country->latestNews;

        if ($news) {

            switch (strtolower($news->sentiment ?? 'neutral')) {

                case 'negative':

                    $recommendations[] = 'Monitor geopolitical developments.';
                    $reasons[] = 'Negative international news detected.';
                    break;

                case 'positive':

                    $recommendations[] = 'Trade outlook is improving.';
                    $reasons[] = 'Recent news sentiment is positive.';
                    break;

                default:

                    $recommendations[] = 'Continue monitoring news.';
                    $reasons[] = 'No major news impact detected.';
                    break;

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Default Recommendation
        |--------------------------------------------------------------------------
        */

        if (empty($recommendations)) {

            $recommendations[] = 'Continue normal trade operations.';
            $recommendations[] = 'Maintain routine monitoring.';
            $reasons[] = 'No significant risk factors detected.';

        }

                /*
        |--------------------------------------------------------------------------
        | Remove Duplicate Recommendation
        |--------------------------------------------------------------------------
        */

        $recommendations = array_values(array_unique($recommendations));

        $reasons = array_values(array_unique($reasons));

        /*
        |--------------------------------------------------------------------------
        | Save Recommendation
        |--------------------------------------------------------------------------
        */

        TradeRecommendation::updateOrCreate(

            [

                'country_id' => $country->id,

            ],

            [

                'trade_action' => $tradeAction,

                'priority' => $priority,

                'recommendation' => implode("\n", $recommendations),

                'business_reason' => implode("\n", $reasons),

                'risk_level' => $riskLevel,

                'risk_score' => $finalScore,

                'generated_at' => now(),

            ]

        );

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Preview Recommendation (Optional)
    |--------------------------------------------------------------------------
    */

    public function preview(Country $country): array
    {
        $country->load([
            'latestWeather',
            'latestCurrency',
            'latestNews',
            'ports',
            'economicData'
        ]);

        $risk = $this->riskAnalysis->calculate($country);

        return [

            'risk_score' => $risk['final_score'],

            'risk_level' => $risk['risk_level'],

            'trade_action' => TradeRecommendation::where(
                'country_id',
                $country->id
            )->value('trade_action'),

            'priority' => TradeRecommendation::where(
                'country_id',
                $country->id
            )->value('priority'),

        ];
    }
}