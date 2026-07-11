<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Port;

class PortService
{
    public function fetchAndStore(Country $country)
    {
        $score = rand(10, 95);

        $status = $this->determineStatus($score);

        Port::updateOrCreate(
            ['country_id' => $country->id],
            [
                'port_name' => $country->country_name . ' Main Port',
                'port_code' => strtoupper(substr($country->country_code, 0, 3)) . 'P',
                'city' => $country->capital,
                'country_name_cache' => $country->country_name,
                'latitude' => $country->latitude,
                'longitude' => $country->longitude,
                'congestion_score' => $score,
                'status' => $status
            ]
        );

        return true;
    }

    private function determineStatus($score)
    {
        if ($score <= 30) return 'normal';
        if ($score <= 70) return 'busy';
        return 'congested';
    }
}