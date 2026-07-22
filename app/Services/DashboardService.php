<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Port;
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

    public function buildDashboard($countryId = null): array
    {
        return [
            'summary' => $this->getSummary($countryId),
            'mapCountries' => $this->getMapCountries($countryId),
            'riskDistribution' => $this->getRiskDistribution($countryId),
            'highestRiskCountries' => $this->getHighestRiskCountries($countryId),
            'portRisks' => $this->getPortRisks($countryId),
            'weatherPanel' => $this->getWeatherPanel($countryId),
            'currencyPanel' => $this->getCurrencyPanel($countryId),
            'newsPanel' => $this->getNewsPanel($countryId)
        ];
    }

    /**
     * ==========================================================
     * SUMMARY
     * ==========================================================
     */

    private function getSummary($countryId): array
    {
        $countryQuery = Country::query();
        $riskQuery = RiskScore::query();
        $weatherQuery = WeatherData::query();
        $currencyQuery = CurrencyRate::query();
        $recQuery = TradeRecommendation::query();

        if ($countryId) {
            $countryQuery->where('id', $countryId);
            $riskQuery->where('country_id', $countryId);
            $weatherQuery->where('country_id', $countryId);
            $currencyQuery->where('country_id', $countryId);
            $recQuery->where('country_id', $countryId);
        }

        $totalCountries = $countryQuery->count();
        $averageRisk = clone $riskQuery;
        $averageRisk = $averageRisk->avg('final_score') ?? 0;

        $critical = (clone $riskQuery)->where('risk_level', 'critical')->count();
        $dangerous = (clone $riskQuery)->where('risk_level', 'dangerous')->count();
        $alert = (clone $riskQuery)->where('risk_level', 'alert')->count();

        $riskCountries = $critical + $dangerous + $alert;

        return [
            'totalCountries' => $totalCountries,
            'weatherMonitoring' => $weatherQuery->count(),
            'currencyMonitoring' => $currencyQuery->count(),
            'recommendationCount' => $recQuery->count(),
            'averageRisk' => round($averageRisk, 2),
            'critical' => $critical,
            'dangerous' => $dangerous,
            'alert' => $alert,
            'riskCountries' => $riskCountries
        ];
    }

    /**
     * ==========================================================
     * RISK DISTRIBUTION
     * ==========================================================
     */

    private function getRiskDistribution($countryId)
    {
        $query = RiskScore::selectRaw('risk_level, COUNT(*) as total');
        
        if ($countryId) {
            $query->where('country_id', $countryId);
        }
        
        return $query->groupBy('risk_level')->orderBy('risk_level')->get();
    }

    /**
     * ==========================================================
     * HIGHEST RISK COUNTRIES
     * ==========================================================
     */

    private function getHighestRiskCountries($countryId)
    {
        $query = Country::with(['latestRisk'])->whereHas('latestRisk');
        
        if ($countryId) {
            $query->where('id', $countryId);
        }

        return $query->get()
            ->sortByDesc(function ($country) {
                return $country->latestRisk->final_score;
            })
            ->take(10)
            ->values();
    }

        /**
     * ==========================================================
     * PORT RISK MONITORING
     * ==========================================================
     */

    private function getPortRisks($countryId)
    {
        $query = Port::query();

        if ($countryId) {

            $country = Country::find($countryId);

            if ($country) {
                $query->where('country_iso2', $country->country_code);
            }
        }

        return $query
            ->orderByDesc('risk_score')
            ->take(10)
            ->get();
    }

    /**
     * ==========================================================
     * WORLD MAP
     * ==========================================================
     */

    private function getMapCountries($countryId)
    {
        $query = Country::select('id', 'country_name', 'country_code', 'flag', 'currency_code', 'latitude', 'longitude', 'region')
            ->with(['latestRisk', 'recommendation', 'latestWeather', 'latestCurrency']);
            
        // intentionally NOT filtering by countryId to keep all markers on map

        return $query->orderBy('country_name')->get();
    }

    /**
     * ==========================================================
     * WEATHER PANEL
     * ==========================================================
     */

    private function getWeatherPanel($countryId)
    {
        $query = WeatherData::with('country')->latest('recorded_at');
        
        if ($countryId) {
            $query->where('country_id', $countryId);
        }
        
        return $query->take(10)->get();
    }

    /**
     * ==========================================================
     * CURRENCY PANEL
     * ==========================================================
     */

    private function getCurrencyPanel($countryId)
    {
        $query = CurrencyRate::with('country')->latest('recorded_at');
        
        if ($countryId) {
            $query->where('country_id', $countryId);
        }
        
        return $query->take(10)->get();
    }

    /**
     * ==========================================================
     * NEWS PANEL
     * ==========================================================
     */

    private function getNewsPanel($countryId)
    {
        $query = NewsCache::with('country')->latest('published_at');
        
        if ($countryId) {
            $query->where('country_id', $countryId);
        }
        
        return $query->take(10)->get();
    }
}