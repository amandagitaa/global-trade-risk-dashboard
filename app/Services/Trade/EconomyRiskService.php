<?php

namespace App\Services\Trade;

use App\Models\EconomicData;

class EconomyRiskService
{
    public function calculate(int $countryId): float
    {
        $economic = EconomicData::where('country_id', $countryId)->first();

        if (!$economic) {
            return 50;
        }

        $score = 50;

        /*
        |--------------------------------------------------------------------------
        | GDP
        |--------------------------------------------------------------------------
        */

        if ($economic->gdp >= 10000000000000) {

            $score -= 20;

        } elseif ($economic->gdp >= 5000000000000) {

            $score -= 15;

        } elseif ($economic->gdp >= 1000000000000) {

            $score -= 10;

        } elseif ($economic->gdp >= 500000000000) {

            $score -= 5;

        } else {

            $score += 8;

        }

        /*
        |--------------------------------------------------------------------------
        | Inflation
        |--------------------------------------------------------------------------
        */

        if ($economic->inflation > 15) {

            $score += 25;

        } elseif ($economic->inflation > 10) {

            $score += 15;

        } elseif ($economic->inflation > 5) {

            $score += 8;

        } elseif ($economic->inflation < 2) {

            $score -= 5;

        }

        /*
        |--------------------------------------------------------------------------
        | Trade Balance
        |--------------------------------------------------------------------------
        */

        $balance = $economic->exports - $economic->imports;

        if ($balance > 100000000000) {

            $score -= 8;

        } elseif ($balance > 0) {

            $score -= 5;

        } elseif ($balance < -100000000000) {

            $score += 10;

        } else {

            $score += 5;

        }

        return max(0, min(100, round($score,2)));
    }
}