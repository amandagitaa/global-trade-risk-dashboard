<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Port;
use App\Models\ShippingRoute;

class ShippingRouteSeeder extends Seeder
{
    public function run(): void
    {
        $regions = include database_path('data/routes_world.php');

        foreach ($regions as $regionName => $routes) {
            foreach ($routes as $route) {
                $originKeyword = $route[0];
                $destKeyword = $route[1];

                $origins = Port::where('name', 'like', "%{$originKeyword}%")
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->get();

                $destinations = Port::where('name', 'like', "%{$destKeyword}%")
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->get();

                foreach ($origins as $origin) {
                    foreach ($destinations as $dest) {
                        if ($origin->id == $dest->id) {
                            continue;
                        }

                        $distance = $this->calculateHaversineDistance(
                            $origin->latitude,
                            $origin->longitude,
                            $dest->latitude,
                            $dest->longitude
                        );

                        // estimated days based on average speed (e.g., 20 knots = ~37 km/h)
                        $speedKmh = 37;
                        $estimatedDays = max(1, (int) round($distance / ($speedKmh * 24)));

                        // shipping cost based on distance (e.g. $2 per km)
                        $shippingCost = round($distance * 2, 2);

                        $weatherRisk = $this->calculateWeatherRisk($origin, $dest);
                        $piracyRisk = $this->calculatePiracyRisk($regionName, $origin, $dest);

                        $avgRisk = ($weatherRisk + $piracyRisk) / 2;
                        if ($avgRisk >= 60) {
                            $riskLevel = 'High';
                        } elseif ($avgRisk >= 40) {
                            $riskLevel = 'Medium';
                        } else {
                            $riskLevel = 'Low';
                        }

                        if ($riskLevel === 'High') {
                            $status = 'Warning';
                            $aiRecommendation = 'Consider alternative shipping corridor. Monitor weather and security updates closely.';
                        } elseif ($riskLevel === 'Medium') {
                            $status = 'Monitor';
                            $aiRecommendation = 'Monitor weather before departure. Stay alert and follow standard security protocols.';
                        } else {
                            $status = 'Open';
                            $aiRecommendation = 'Recommended for all cargo. Low risk route with stable conditions.';
                        }

                        ShippingRoute::updateOrCreate(
                            [
                                'origin_port_id' => $origin->id,
                                'destination_port_id' => $dest->id,
                            ],
                            [
                                'distance_km' => (int) round($distance),
                                'estimated_days' => $estimatedDays,
                                'shipping_cost' => $shippingCost,
                                'weather_risk' => $weatherRisk,
                                'piracy_risk' => $piracyRisk,
                                'risk_level' => $riskLevel,
                                'status' => $status,
                                'ai_recommendation' => $aiRecommendation
                            ]
                        );
                    }
                }
            }
        }
    }

    private function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        
        return $angle * $earthRadius;
    }

    private function calculateWeatherRisk($origin, $dest)
    {
        $latAvg = (abs($origin->latitude) + abs($dest->latitude)) / 2;
        
        if ($latAvg > 50) return 70; // High lat
        if ($latAvg > 30) return 40; // Mid lat
        if ($latAvg < 15) return 55; // Tropical storms
        
        return 20; // Mild
    }

    private function calculatePiracyRisk($region, $origin, $dest)
    {
        $highRiskKeywords = ['Singapore', 'Malacca', 'Aden', 'Somalia', 'Suez'];
        
        foreach ($highRiskKeywords as $kw) {
            if (stripos($origin->name, $kw) !== false || stripos($dest->name, $kw) !== false) {
                return 80;
            }
        }

        if ($region === 'africa' || $region === 'middle_east') {
            return 50;
        }

        if ($region === 'asia') {
            return 30;
        }

        return 10;
    }
}