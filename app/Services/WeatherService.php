<?php

namespace App\Services;

use App\Models\Country;
use App\Models\WeatherData;
use Illuminate\Support\Facades\Http;

class WeatherService
{
    public function sync()
    {
        $countries = Country::all();

        foreach ($countries as $country) {

            if (!$country->latitude || !$country->longitude) {
                continue;
            }

            $url = "https://api.open-meteo.com/v1/forecast";

            $response = Http::timeout(20)->get($url, [
                'latitude' => $country->latitude,
                'longitude' => $country->longitude,
                'current' => 'temperature_2m,wind_speed_10m,rain'
            ]);

            if (!$response->successful()) {
                continue;
            }

            $current = $response->json()['current'];

            $temperature = $current['temperature_2m'] ?? 0;
            $wind = $current['wind_speed_10m'] ?? 0;
            $rain = $current['rain'] ?? 0;

            $status = $this->determineStatus($temperature,$rain,$wind);
            $stormRisk = $this->stormRisk($status);

            WeatherData::updateOrCreate(
                [
                    'country_id'=>$country->id
                ],
                [
                    'temperature'=>$temperature,
                    'rainfall'=>$rain,
                    'wind_speed'=>$wind,
                    'storm_risk'=>$stormRisk,
                    'weather_status'=>$status,
                    'recorded_at'=>now()
                ]
            );

        }

        return true;
    }

    private function determineStatus($temp,$rain,$wind)
    {

        if($wind>60 || $rain>50)
            return 'extreme';

        if($wind>40 || $rain>20)
            return 'storm';

        if($rain>5)
            return 'rain';

        return 'clear';
    }

    private function stormRisk($status)
    {
        return match($status){

            'clear'=>10,

            'rain'=>30,

            'storm'=>60,

            'extreme'=>90,

            default=>10
        };
    }

}