<?php

namespace App\Services\Trade;

use App\Models\Country;
use App\Models\TradeRecommendation;

class RecommendationEngine
{
    public function generate(Country $country): TradeRecommendation
    {
        $risk = $country->latestRisk;

        if (!$risk) {

            throw new \Exception('Risk score not found.');

        }

        $primaryFactor = $this->primaryFactor($risk);

        $action = $this->tradeAction(

            $risk->risk_level,

            $primaryFactor

        );

        $priority = $this->priority(

            $risk->risk_level

        );

        $recommendation = $this->recommendation(

            $risk->risk_level,

            $primaryFactor

        );

        return TradeRecommendation::updateOrCreate(

            [

                'country_id'=>$country->id

            ],

            [

                'trade_action'=>$action,

                'priority'=>$priority,

                'recommendation'=>$recommendation,

                'business_reason'=>$risk->reason,

                'generated_at'=>now()

            ]

        );

    }

    private function priority(string $risk): string
{
    return match($risk){

        'safe'=>'Low',

        'stable'=>'Medium',

        'alert'=>'High',

        'dangerous'=>'Very High',

        'critical'=>'Critical',

        default=>'Unknown'

    };
}

private function primaryFactor($risk): string
{
    $scores=[

        'weather'=>$risk->weather_score,

        'currency'=>$risk->currency_score,

        'port'=>$risk->port_score,

        'news'=>$risk->news_score,

        'economy'=>$risk->economic_score

    ];

    arsort($scores);

    return array_key_first($scores);
}