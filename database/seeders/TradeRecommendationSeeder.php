<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TradeRecommendationSeeder extends Seeder
{
    public function run(): void
    {
        $countries = DB::table('countries')->get();

        foreach ($countries as $country) {

            DB::table('trade_recommendations')->updateOrInsert(
                [
                    'country_id' => $country->id
                ],
                [
                    'trade_action' => 'Monitor',
                    'priority' => 'medium',
                    'recommendation' => 'Maintain trade activity with '.$country->country_name,
                    'business_reason' => 'Trade condition analysis based on current risk factors',
                    'generated_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

        }
    }
}