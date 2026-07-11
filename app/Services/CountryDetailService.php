<?php

namespace App\Services;

use App\Models\Country;

class CountryDetailService
{
    public function build(Country $country): array
    {
        $country->load([

            'latestRisk',

            'recommendation',

            'latestWeather',

            'latestCurrency',

            'latestNews',

            'ports'

        ]);

        return [

            'country'=>$country,

            'risk'=>$country->latestRisk,

            'recommendation'=>$country->recommendation,

            'weather'=>$country->latestWeather,

            'currency'=>$country->latestCurrency,

            'news'=>$country->latestNews,

            'ports'=>$country->ports

        ];
    }
}