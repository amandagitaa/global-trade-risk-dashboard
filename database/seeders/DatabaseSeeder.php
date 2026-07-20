<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\CountryService;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        // Sync data 250 negara dari countries.dev
        app(CountryService::class)->syncCountries();


        $this->call([

            PositiveWordSeeder::class,

            NegativeWordSeeder::class,

            WeatherSeeder::class,

            CurrencySeeder::class,

            EconomicDataSeeder::class,

            RiskScoreSeeder::class,

            TradeRecommendationSeeder::class,

            NewsSeeder::class,

        ]);

        $this->call([
            PortSeeder::class,
        ]);

        $this->call([
            CountrySeeder::class,
            PortSeeder::class,
            ShipSeeder::class,
            ShippingRouteSeeder::class,
        ]);
    }
}