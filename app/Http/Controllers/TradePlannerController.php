<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Port;
use App\Models\ShippingRoute;
use App\Models\WeatherData;
use App\Models\TradeSimulation;
use Illuminate\Support\Facades\Auth;

class TradePlannerController extends Controller
{
    public function index(Request $request)
    {
        $countries = Country::with('ports')->orderBy('country_name')->get();

        $simulation = null;
        $originPort = null;
        $destinationPort = null;
        $routes = collect();

        if ($request->has('origin_port') && $request->has('destination_port')) {
            $originPort = Port::with('country')->findOrFail($request->origin_port);
            $destinationPort = Port::with([
                'country.weatherData', 
                'country.currencyRates', 
                'country.economicData', 
                'country.riskScores', 
                'country.newsCache'
            ])->findOrFail($request->destination_port);

            $routes = ShippingRoute::where('origin_port_id', $originPort->id)
                                 ->where('destination_port_id', $destinationPort->id)
                                 ->get();

            $baseEta = 0;
            $distance = 0;
            $baseCost = 0;

            if ($routes->count() > 0) {
                $bestRoute = $routes->sortBy('estimated_days')->first();
                $baseEta = $bestRoute->estimated_days;
                $distance = $bestRoute->distance_km;
            } else {
                // If no direct route in DB, simulate based on distance
                $distance = $this->haversineGreatCircleDistance(
                    $originPort->latitude, $originPort->longitude,
                    $destinationPort->latitude, $destinationPort->longitude
                );
                // Assume average speed 20 knots (approx 900 km/day)
                $baseEta = max(1, round($distance / 900));
            }

            // Estimate base cost based on distance and container size
            $multiplier = $request->input('container_size', '20') == '40' ? 1.8 : 1;
            // Base: $500 + ($0.5 * distance)
            $baseCost = 500 + ($distance * 0.5);
            $baseCost = $baseCost * $multiplier;

            $delays = [];
            
            // 1. Weather check (Destination)
            $weatherStatus = 'Clear';
            $weatherImpact = 0;
            $weatherData = $destinationPort->country->weatherData->first();
            if ($weatherData) {
                if (str_contains(strtolower($weatherData->condition), 'storm')) {
                    $weatherStatus = 'Storm';
                    $weatherImpact = 4;
                    $delays[] = ['reason' => 'Storm at Destination', 'days' => 4];
                } elseif (str_contains(strtolower($weatherData->condition), 'rain')) {
                    $weatherStatus = 'Rain';
                    $weatherImpact = 2;
                    $delays[] = ['reason' => 'Heavy Rain', 'days' => 2];
                } elseif (str_contains(strtolower($weatherData->condition), 'extreme')) {
                    $weatherStatus = 'Extreme';
                    $weatherImpact = 7;
                    $delays[] = ['reason' => 'Extreme Weather', 'days' => 7];
                }
            }

            // 2. Port Congestion check
            if ($destinationPort->traffic_level == 'High') {
                $delays[] = ['reason' => 'Port Congestion', 'days' => 3];
            }

            // 3. Political Risk check
            $politicalStatus = 'Normal';
            if ($destinationPort->political_risk > 70) {
                $politicalStatus = 'Conflict';
                $delays[] = ['reason' => 'Political Conflict', 'days' => 5];
            } elseif ($destinationPort->political_risk > 50) {
                $politicalStatus = 'Restricted';
                $delays[] = ['reason' => 'Political Restrictions', 'days' => 2];
            }

            // 4. Currency Risk Check
            $currencyData = $destinationPort->country->currencyRates->first();
            if ($currencyData && floatval($currencyData->exchange_rate) > 15000) {
                // E.g., if exchange rate is highly inflated, increase base cost slightly
                $baseCost *= 1.1; // 10% currency risk premium
            }

            // 5. Economy Impact Check
            $economyData = $destinationPort->country->economicData;
            if ($economyData && floatval($economyData->inflation_rate) > 8) {
                $baseCost *= 1.05; // 5% inflation premium
            }

            // 6. News Impact Check
            $newsData = $destinationPort->country->newsCache->first();
            if ($newsData && str_contains(strtolower($newsData->sentiment ?? ''), 'negative')) {
                $delays[] = ['reason' => 'Negative News Sentiment', 'days' => 2];
            } elseif ($destinationPort->logistic_risk > 60) {
                // Fallback to logistic risk if news unavailable or not negative
                $delays[] = ['reason' => 'Logistics Congestion', 'days' => 2];
            }

            $totalDelayDays = collect($delays)->sum('days');
            $finalEta = $baseEta + $totalDelayDays;

            $tradeRisk = 'Low';
            $totalRiskScore = $destinationPort->risk_score + $weatherImpact * 5;
            if ($totalRiskScore > 75) $tradeRisk = 'Critical';
            elseif ($totalRiskScore > 50) $tradeRisk = 'High';
            elseif ($totalRiskScore > 30) $tradeRisk = 'Medium';

            $costIndicator = 'Stable';
            if ($totalDelayDays > 5) {
                $baseCost *= 1.25; // 25% increase
                $costIndicator = 'Increase';
            }
            if ($tradeRisk == 'Critical') {
                $baseCost *= 1.5; // 50% increase
                $costIndicator = 'Critical';
            }

            $recommendation = [
                'status' => 'Recommended to Ship',
                'badge' => 'success',
                'reason' => 'Conditions are generally stable with manageable risks.',
                'action' => 'Estimated Arrival in ' . $finalEta . ' Days'
            ];

            if ($totalDelayDays > 3 || $tradeRisk == 'High' || $tradeRisk == 'Critical') {
                $recommendation = [
                    'status' => 'Delay Shipment',
                    'badge' => 'danger',
                    'reason' => 'High risk detected due to ' . (count($delays) > 0 ? $delays[0]['reason'] : 'overall risk factors') . '.',
                    'action' => 'Suggested Departure: ' . max(3, $totalDelayDays) . ' Days Later'
                ];
            }

            if (Auth::check()) {
                TradeSimulation::create([
                    'user_id' => Auth::id(),
                    'origin_country_id' => $originPort->country_id,
                    'destination_country_id' => $destinationPort->country_id,
                    'origin_port_id' => $originPort->id,
                    'destination_port_id' => $destinationPort->id,
                    'cargo_type' => $request->input('cargo_type', 'General'),
                    'container_size' => $request->input('container_size', '20 FT'),
                    'departure_date' => $request->input('departure_date', date('Y-m-d')),
                    'estimated_distance' => round($distance),
                    'estimated_duration' => $finalEta,
                    'weather_impact' => $weatherStatus,
                    'currency_impact' => $costIndicator,
                    'risk_score' => $totalRiskScore,
                    'risk_level' => $tradeRisk,
                    'ai_recommendation' => $recommendation['status'] . ' - ' . $recommendation['reason']
                ]);
            }

            $simulation = [
                'origin' => $originPort,
                'destination' => $destinationPort,
                'distance' => round($distance),
                'baseEta' => $baseEta,
                'totalDelay' => $totalDelayDays,
                'finalEta' => $finalEta,
                'cost' => round($baseCost),
                'costIndicator' => $costIndicator,
                'tradeRisk' => $tradeRisk,
                'weatherStatus' => $weatherStatus,
                'politicalStatus' => $politicalStatus,
                'delays' => $delays,
                'recommendation' => $recommendation,
                'routes' => $routes,
                'cargo_type' => $request->input('cargo_type', 'General'),
                'container_size' => $request->input('container_size', '20 FT')
            ];
        }

                $history = collect();
        if (Auth::check()) {
            $history = TradeSimulation::with(['originPort.country', 'destinationPort.country'])
                ->where('user_id', Auth::id())
                ->latest()
                ->get();
        }

        return view('trade-planner.index', compact('countries', 'simulation', 'history'));
    }

    /**
     * Calculates the great-circle distance between two points, with
     * the Haversine formula.
     */
    private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
    {
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
          cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}
