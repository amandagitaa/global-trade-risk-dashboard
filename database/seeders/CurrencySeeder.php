<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $countries = DB::table('countries')->get();

        foreach ($countries as $country) {

            DB::table('currency_rates')->insert([

                'country_id' => $country->id,

                'base_currency' => 'USD',

                'target_currency' => $country->currency_code ?? 'USD',

                'exchange_rate' => rand(50,1500) / 100,

                'change_percentage' => rand(-300,300) / 100,

                'recorded_at' => now(),

                'created_at' => now(),

                'updated_at' => now()

            ]);

        }
    }
}