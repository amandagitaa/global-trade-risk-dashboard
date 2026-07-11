<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Risk Weights
    |--------------------------------------------------------------------------
    */

    'weights' => [

        'weather'  => 20,

        'currency' => 25,

        'port'     => 25,

        'news'     => 20,

        'economy'  => 10,

    ],

    /*
    |--------------------------------------------------------------------------
    | Weather Score
    |--------------------------------------------------------------------------
    */

    'weather_score' => [

        'clear'       => 10,

        'cloudy'      => 20,

        'rain'        => 45,

        'heavy_rain'  => 70,

        'storm'       => 100,

    ],

    /*
    |--------------------------------------------------------------------------
    | News Score
    |--------------------------------------------------------------------------
    */

    'news_score' => [

        'positive' => 10,

        'neutral'  => 40,

        'negative' => 90,

    ],

    /*
    |--------------------------------------------------------------------------
    | Currency Score
    |--------------------------------------------------------------------------
    */

    'currency_score' => [

        [
            'max'   => 2,
            'score' => 10,
        ],

        [
            'max'   => 5,
            'score' => 30,
        ],

        [
            'max'   => 8,
            'score' => 60,
        ],

        [
            'max'   => 999,
            'score' => 100,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Risk Level
    |--------------------------------------------------------------------------
    */

    'risk_level' => [

        [
            'max'   => 20,
            'level' => 'safe',
        ],

        [
            'max'   => 40,
            'level' => 'stable',
        ],

        [
            'max'   => 60,
            'level' => 'alert',
        ],

        [
            'max'   => 80,
            'level' => 'dangerous',
        ],

        [
            'max'   => 100,
            'level' => 'critical',
        ],

    ],

    /*
|--------------------------------------------------------------------------
| Trade Recommendation Rules
|--------------------------------------------------------------------------
*/

'recommendation_rules' => [

    'safe' => [

        'weather' => [
            'action' => 'Proceed Normally',
            'recommendation' => 'Trade can continue normally.',
        ],

        'currency' => [
            'action' => 'Monitor Exchange Rate',
            'recommendation' => 'Monitor exchange rate periodically.',
        ],

        'port' => [
            'action' => 'Proceed Normally',
            'recommendation' => 'Port operation is stable.',
        ],

        'news' => [
            'action' => 'Proceed Normally',
            'recommendation' => 'No significant geopolitical issue.',
        ],

        'economy' => [
            'action' => 'Proceed Normally',
            'recommendation' => 'Economic condition is stable.',
        ],

    ],

    'stable' => [

        'weather' => [
            'action' => 'Prepare Shipment',
            'recommendation' => 'Continue shipment with weather monitoring.',
        ],

        'currency' => [
            'action' => 'Monitor Exchange Rate',
            'recommendation' => 'Currency movement should be monitored.',
        ],

        'port' => [
            'action' => 'Monitor Port',
            'recommendation' => 'Monitor port traffic.',
        ],

        'news' => [
            'action' => 'Monitor Situation',
            'recommendation' => 'Monitor international news.',
        ],

        'economy' => [
            'action' => 'Proceed Carefully',
            'recommendation' => 'Monitor economic indicators.',
        ],

    ],

    'alert' => [

        'weather' => [
            'action' => 'Delay Shipment',
            'recommendation' => 'Consider delaying shipment.',
        ],

        'currency' => [
            'action' => 'Hedge Currency',
            'recommendation' => 'Protect against currency fluctuation.',
        ],

        'port' => [
            'action' => 'Find Alternative Port',
            'recommendation' => 'Evaluate nearby ports.',
        ],

        'news' => [
            'action' => 'Review Trade Policy',
            'recommendation' => 'Review current geopolitical situation.',
        ],

        'economy' => [
            'action' => 'Review Market',
            'recommendation' => 'Evaluate economic outlook.',
        ],

    ],

    'dangerous' => [

        'weather' => [
            'action' => 'Delay Shipment',
            'recommendation' => 'Weather condition is unsafe.',
        ],

        'currency' => [
            'action' => 'Delay Transaction',
            'recommendation' => 'Currency volatility is too high.',
        ],

        'port' => [
            'action' => 'Use Alternative Port',
            'recommendation' => 'Avoid congested port.',
        ],

        'news' => [
            'action' => 'Avoid New Contract',
            'recommendation' => 'Political risk is increasing.',
        ],

        'economy' => [
            'action' => 'Reduce Exposure',
            'recommendation' => 'Economic uncertainty detected.',
        ],

    ],

    'critical' => [

        'weather' => [
            'action' => 'Stop Shipment',
            'recommendation' => 'Extreme weather detected.',
        ],

        'currency' => [
            'action' => 'Suspend Transaction',
            'recommendation' => 'Currency instability is critical.',
        ],

        'port' => [
            'action' => 'Use Alternative Port',
            'recommendation' => 'Port congestion is critical.',
        ],

        'news' => [
            'action' => 'Suspend Trade',
            'recommendation' => 'Geopolitical conflict detected.',
        ],

        'economy' => [
            'action' => 'Hold Investment',
            'recommendation' => 'Economic condition is highly uncertain.',
        ],

    ],

],

];