<?php

namespace App\Services;

use App\Models\Country;
use App\Models\CurrencyRate;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    public function fetchAndStore(Country $country): bool
    {
        $currencyCode = $country->currency_code;

        if (!$currencyCode) {
            return false;
        }

        $response = Http::get('https://open.er-api.com/v6/latest/USD');

        if (!$response->successful()) {
            return false;
        }

        $data = $response->json();

        $rate = $data['rates'][$currencyCode] ?? null;

        if (!$rate) {
            return false;
        }

        // sementara semi-dynamic
        $changePercentage = rand(-10, 10);

        CurrencyRate::create([
            'country_id' => $country->id,
            'base_currency' => 'USD',
            'target_currency' => $currencyCode,
            'exchange_rate' => $rate,
            'change_percentage' => $changePercentage,
            'recorded_at' => now(),
        ]);

        return true;
    }
}