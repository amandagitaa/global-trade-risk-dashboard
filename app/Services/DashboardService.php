<?php

namespace App\Services;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\TradeRecommendation;
use App\Models\WeatherData;
use App\Models\CurrencyRate;
use App\Models\NewsCache;

class DashboardService
{
    /**
     * ==========================================================
     * BUILD DASHBOARD
     * ==========================================================
     */

    public function buildDashboard(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Summary
            |--------------------------------------------------------------------------
            */

            'summary' => $this->getSummary(),

            /*
            |--------------------------------------------------------------------------
            | World Map
            |--------------------------------------------------------------------------
            */

            'mapCountries' => $this->getMapCountries(),

            /*
            |--------------------------------------------------------------------------
            | Risk Distribution
            |--------------------------------------------------------------------------
            */

            'riskDistribution' => $this->getRiskDistribution(),

            /*
            |--------------------------------------------------------------------------
            | Highest Risk
            |--------------------------------------------------------------------------
            */

            'highestRiskCountries' => $this->getHighestRiskCountries(),

/*
|--------------------------------------------------------------------------
| Recommendation
|--------------------------------------------------------------------------
*/

'recommendations' => $this->getRecommendations(),

            /*
            |--------------------------------------------------------------------------
            | Weather
            |--------------------------------------------------------------------------
            */

            'weatherPanel' => $this->getWeatherPanel(),

            /*
            |--------------------------------------------------------------------------
            | Currency
            |--------------------------------------------------------------------------
            */

            'currencyPanel' => $this->getCurrencyPanel(),

            /*
            |--------------------------------------------------------------------------
            | News
            |--------------------------------------------------------------------------
            */

            'newsPanel' => $this->getNewsPanel()

        ];
    }

    /**
     * ==========================================================
     * SUMMARY
     * ==========================================================
     */

    private function getSummary(): array
    {
        $totalCountries = Country::count();

        $averageRisk = RiskScore::avg('final_score') ?? 0;

        $critical = RiskScore::where('risk_level', 'critical')->count();

        $dangerous = RiskScore::where('risk_level', 'dangerous')->count();

        $alert = RiskScore::where('risk_level', 'alert')->count();

        $stable = RiskScore::where('risk_level', 'stable')->count();

        $safe = RiskScore::where('risk_level', 'safe')->count();

        return [

            'totalCountries' => $totalCountries,

            'averageRisk' => round($averageRisk, 2),

            'critical' => $critical,

            'dangerous' => $dangerous,

            'alert' => $alert,

            'stable' => $stable,

            'safe' => $safe

        ];
    }

    /**
     * ==========================================================
     * RISK DISTRIBUTION
     * ==========================================================
     */

    private function getRiskDistribution()
    {
        return RiskScore::selectRaw('

                risk_level,

                COUNT(*) as total

            ')

            ->groupBy('risk_level')

            ->orderBy('risk_level')

            ->get();
    }

    /**
     * ==========================================================
     * HIGHEST RISK COUNTRIES
     * ==========================================================
     */

    private function getHighestRiskCountries()
    {
        return Country::with([

                'latestRisk'

            ])

            ->whereHas('latestRisk')

            ->get()

            ->sortByDesc(function ($country) {

                return $country->latestRisk->final_score;

            })

            ->take(10)

            ->values();
    }

        /**
     * ==========================================================
     * TRADE RECOMMENDATIONS
     * ==========================================================
     */

    private function getRecommendations()
    {
       return TradeRecommendation::with('country')

        ->latest()

        ->take(10)

        ->get();
    }

    /**
     * ==========================================================
     * WORLD MAP
     * ==========================================================
     */

    private function getMapCountries()
    {
        return Country::select(

                'id',
                'country_name',
                'country_code',
                'flag',
                'currency_code',
                'latitude',
                'longitude',
                'region'

            )

            ->with([

                'latestRisk',

                'recommendation',

                'latestWeather',

                'latestCurrency'

            ])

            ->orderBy('country_name')

            ->get();
    }

    /**
     * ==========================================================
     * WEATHER PANEL
     * ==========================================================
     */

    private function getWeatherPanel()
    {
        return WeatherData::with('country')

            ->latest('recorded_at')

            ->take(10)

            ->get();
    }

    /**
     * ==========================================================
     * CURRENCY PANEL
     * ==========================================================
     */

    private function getCurrencyPanel()
    {
        return CurrencyRate::with('country')

            ->latest('recorded_at')

            ->take(10)

            ->get();
    }

    /**
     * ==========================================================
     * NEWS PANEL
     * ==========================================================
     */

    private function getNewsPanel()
    {
        return NewsCache::with('country')

            ->latest('published_at')

            ->take(10)

            ->get();
    }
}
