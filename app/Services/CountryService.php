<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Support\Facades\Http;

class CountryService
{
    public function syncCountries()
    {
        $response = Http::timeout(60)
            ->get('https://countries.dev/countries');

        if (!$response->successful()) {
            return false;
        }

        $countries = $response->json();

        foreach ($countries as $item) {

            Country::updateOrCreate(
                [
                    'country_code' => $item['alpha2Code'] ?? null,
                ],
                [
                    'country_name'  => $item['name'] ?? null,

                    'capital'       => is_array($item['capital'] ?? null)
                        ? ($item['capital'][0] ?? null)
                        : ($item['capital'] ?? null),

                    'region'        => $item['region'] ?? null,

                    'subregion'     => $item['subregion'] ?? null,

                    'currency_name' => $item['currencies'][0]['name'] ?? null,

                    'currency_code' => $item['currencies'][0]['code'] ?? null,

                    'language'      => $item['languages'][0]['name'] ?? null,

                    'flag'          => isset($item['alpha2Code']) 
                        ? 'https://flagcdn.com/w320/' . strtolower($item['alpha2Code']) . '.png' 
                        : ($item['flags']['png'] ?? null),

                    'population'    => $item['population'] ?? 0,

                    'latitude'      => $item['latlng'][0] ?? 0,

                    'longitude'     => $item['latlng'][1] ?? 0,
                ]
            );

        }

        return true;
    }
}