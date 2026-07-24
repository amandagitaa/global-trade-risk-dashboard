<?php

namespace App\Services;

use App\Models\Country;
use App\Services\Trade\RiskAnalysisService;

class CountryDetailService
{
    protected RiskAnalysisService $riskAnalysis;

    public function __construct(RiskAnalysisService $riskAnalysis)
    {
        $this->riskAnalysis = $riskAnalysis;
    }

    public function build(Country $country): array
    {
        $country->load([

            'latestRisk',

            'recommendation',

            'latestWeather',

            'latestCurrency',

            'latestNews',

            'ports',

            'economicData'

        ]);

        /*
        |--------------------------------------------------------------------------
        | Automatic Risk Analysis
        |--------------------------------------------------------------------------
        */

        $analysis = $this->riskAnalysis->calculate($country);

        /*
        |--------------------------------------------------------------------------
        | Merge Latest Risk + Dynamic Analysis
        |--------------------------------------------------------------------------
        */

        $risk = $country->latestRisk;

        if (!$risk) {

            $risk = new \stdClass();

        }

        $risk->weather_score = $analysis['weather_score'];

        $risk->currency_score = $analysis['currency_score'];

        $risk->economic_score = $analysis['economy_score'];

        $risk->port_score = $analysis['port_score'];

        $risk->news_score = $analysis['news_score'];

        $risk->final_score = $analysis['final_score'];

        $risk->risk_level = $analysis['risk_level'];

        $risk->reason = $analysis['reason'];

        $data = [

            'country' => $country,

            'risk' => $risk,

            'recommendation' => $country->recommendation,

            'weather' => $country->latestWeather,

            'currency' => $country->latestCurrency,

            'news' => $country->latestNews,

            'ports' => $country->ports,

            'economic' => $country->economicData,

        ];

        /*
        |--------------------------------------------------------------------------
        | Port Information Metrics
        |--------------------------------------------------------------------------
        */
        $portIds = $country->ports->pluck('id')->toArray();
        $totalPorts = count($portIds);
        
        $activeShips = 0;
        $activeShipModels = collect();
        $shippingRoutes = 0;
        $connectedCountries = 0;
        $averageCapacity = $country->ports->avg('teu_capacity');
        $mainExportGateway = '-';
        $mainImportGateway = '-';
        
        if ($totalPorts > 0) {
            $activeShipModels = \App\Models\Ship::with(['currentPort', 'destinationPort'])
                ->whereIn('current_port_id', $portIds)
                ->get();
            $activeShips = $activeShipModels->count();
            
            $shippingRoutes = \App\Models\ShippingRoute::whereIn('origin_port_id', $portIds)
                                ->orWhereIn('destination_port_id', $portIds)->count();
                                
            $destPortIds = \App\Models\ShippingRoute::whereIn('origin_port_id', $portIds)
                ->pluck('destination_port_id');

            $connectedCountries = \App\Models\Port::whereIn('id', $destPortIds)
                ->where('country_iso2', '!=', $country->country_code)
                ->distinct()
                ->count('country_iso2');

            $exportCounts = \App\Models\ShippingRoute::whereIn('origin_port_id', $portIds)
                ->selectRaw('origin_port_id, count(*) as total')
                ->groupBy('origin_port_id')
                ->orderBy('total', 'desc')
                ->first();
            
            if ($exportCounts) {
                $mainExportGateway = $country->ports->firstWhere('id', $exportCounts->origin_port_id)->name ?? '-';
            }

            $importCounts = \App\Models\ShippingRoute::whereIn('destination_port_id', $portIds)
                ->selectRaw('destination_port_id, count(*) as total')
                ->groupBy('destination_port_id')
                ->orderBy('total', 'desc')
                ->first();

            if ($importCounts) {
                $mainImportGateway = $country->ports->firstWhere('id', $importCounts->destination_port_id)->name ?? '-';
            }
        }

        $avgRisk = $country->ports->avg('risk_score') ?? 0;
        
        if ($avgRisk > 80) {
            $operationalStatus = 'Closed';
        } elseif ($avgRisk >= 60) {
            $operationalStatus = 'Congested';
        } else {
            $operationalStatus = 'Normal';
        }

        // Nearest Ports for Landlocked Countries
        $nearestPorts = collect();
        if ($totalPorts == 0 && $country->latitude && $country->longitude) {
            $lat = $country->latitude;
            $lng = $country->longitude;
            $nearestPorts = \App\Models\Port::selectRaw(
                "*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance",
                [$lat, $lng, $lat]
            )
            ->with('country') // Ensure country is loaded for name display
            ->orderBy('distance')
            ->limit(3)
            ->get();
        }

        // Port Risk Level calculation as requested
        // Hitung berdasarkan: Weather Risk, Shipping Route Risk, Jumlah Active Ships, Status Port
        $portWeatherRisk = $country->ports->avg('weather_risk') ?? 0;
        $avgRouteRisk = \App\Models\ShippingRoute::whereIn('origin_port_id', $portIds)->avg('risk_level') ?? 0; // assuming risk_level is numeric, or we just use simple logic
        
        $riskScore = $portWeatherRisk + ($avgRouteRisk * 10) + ($activeShips > 50 ? 20 : 0) + ($operationalStatus == 'Closed' ? 30 : 0);
        
        if ($riskScore > 60 || $avgRisk > 80) {
            $riskLevel = 'High';
        } elseif ($riskScore > 30 || $avgRisk >= 60) {
            $riskLevel = 'Medium';
        } else {
            $riskLevel = 'Low';
        }

        if ($totalPorts == 0) {
            $riskLevel = 'N/A';
        }

        $data['activeShipModels'] = $activeShipModels;
        $data['ships'] = $activeShips;
        $data['shippingRoutes'] = $shippingRoutes;
        $data['connectedCountries'] = $connectedCountries;
        $data['averageCapacity'] = $averageCapacity ? number_format($averageCapacity, 0) . ' TEU' : '-';
        $data['mainExportGateway'] = $mainExportGateway;
        $data['mainImportGateway'] = $mainImportGateway;
        $data['operationalStatus'] = $operationalStatus;
        $data['riskLevel'] = $riskLevel;
        $data['nearestPorts'] = $nearestPorts;

        return $data;
    }
}