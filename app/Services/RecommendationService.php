<?php

namespace App\Services;

use App\Models\Country;
use App\Models\TradeRecommendation;

class RecommendationService
{
    public function generate(Country $country)
    {
        $risk=$country->latestRisk;

        $weather=$country->weather;

        $currency=$country->currency;

        $port=$country->ports()
            ->orderByDesc('congestion_score')
            ->first();

        $news=$country->latestNews;

        $tradeAction='Import Normally';

        $priority='Low';

        $recommendation=[];

        $reason=[];

        /*
        ============================================
        Risk
        ============================================
        */

        if($risk){

            switch($risk->risk_level){

                case 'safe':

                    $tradeAction='Import Now';

                    break;

                case 'stable':

                    $tradeAction='Import Normally';

                    break;

                case 'alert':

                    $tradeAction='Monitor Daily';

                    $priority='Medium';

                    break;

                case 'dangerous':

                    $tradeAction='Delay Shipment';

                    $priority='High';

                    break;

                case 'critical':

                    $tradeAction='Stop Trade';

                    $priority='Critical';

                    break;

            }

        }

        /*
        ============================================
        Weather
        ============================================
        */

        if($weather){

            if($weather->weather_status=='storm'){

                $recommendation[]='Delay shipment 1-2 days';

                $reason[]='Storm detected';

            }

            if($weather->weather_status=='extreme'){

                $recommendation[]='Avoid sea shipment';

                $reason[]='Extreme weather';

            }

        }

        /*
        ============================================
        Currency
        ============================================
        */

        if($currency){

            if($currency->status=='cost_surge'){

                $recommendation[]='Monitor exchange rate';

                $reason[]='Currency surge';

            }

            if($currency->status=='trade_critical'){

                $recommendation[]='Recalculate import cost';

                $reason[]='Currency volatility';

            }

        }

        /*
        ============================================
        Port
        ============================================
        */

        if($port){

            if($port->congestion_score>=70){

                $recommendation[]='Use alternative port';

                $reason[]='High port congestion';

            }

        }

        /*
        ============================================
        News
        ============================================
        */

        if($news){

            if($news->sentiment=='negative'){

                $recommendation[]='Monitor geopolitical news';

                $reason[]='Negative international news';

            }

        }

        /*
        ============================================
        Default Recommendation
        ============================================
        */

        if(empty($recommendation)){

            $recommendation[]='Safe to Import';

            $recommendation[]='Suggested Shipping: Next 5 Days';

            $recommendation[]='Continue Monitoring';

        }

        TradeRecommendation::updateOrCreate(

            [

                'country_id'=>$country->id

            ],

            [

                'trade_action'=>$tradeAction,

                'priority'=>$priority,

                'recommendation'=>implode("\n",$recommendation),

                'business_reason'=>implode("\n",$reason),

                'generated_at'=>now()

            ]

        );

        return true;

    }
}