<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Country::insert([
            [
                'country_code' => 'CN',
                'country_name' => 'China',
                'capital' => 'Beijing',
                'region' => 'Asia',
                'subregion' => 'East Asia',
                'currency_name' => 'Yuan',
                'currency_code' => 'CNY',
                'language' => 'Chinese',
                'flag' => '🇨🇳',
                'population' => 1412000000,
                'latitude' => 35.8617,
                'longitude' => 104.1954,
            ],
            [
                'country_code' => 'DE',
                'country_name' => 'Germany',
                'capital' => 'Berlin',
                'region' => 'Europe',
                'subregion' => 'Western Europe',
                'currency_name' => 'Euro',
                'currency_code' => 'EUR',
                'language' => 'German',
                'flag' => '🇩🇪',
                'population' => 83200000,
                'latitude' => 51.1657,
                'longitude' => 10.4515,
            ],
            [
                'country_code' => 'AU',
                'country_name' => 'Australia',
                'capital' => 'Canberra',
                'region' => 'Oceania',
                'subregion' => 'Australia and New Zealand',
                'currency_name' => 'Australian Dollar',
                'currency_code' => 'AUD',
                'language' => 'English',
                'flag' => '🇦🇺',
                'population' => 26700000,
                'latitude' => -25.2744,
                'longitude' => 133.7751,
            ]
        ]);
    }
}
