<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\EconomicData;

class EconomicDataSeeder extends Seeder
{
    public function run(): void
    {
        EconomicData::truncate();

        /*
        |--------------------------------------------------------------
        | GDP acuan (USD)
        | Nilai dibulatkan agar sederhana.
        |--------------------------------------------------------------
        */

        $realEconomy = [

            'United States' => 29000000000000,
            'China' => 18000000000000,
            'Japan' => 4200000000000,
            'Germany' => 4700000000000,
            'India' => 3900000000000,
            'United Kingdom' => 3600000000000,
            'France' => 3200000000000,
            'Italy' => 2400000000000,
            'Canada' => 2200000000000,
            'Brazil' => 2200000000000,
            'Russia' => 2200000000000,
            'South Korea' => 1800000000000,
            'Australia' => 1700000000000,
            'Mexico' => 1800000000000,
            'Indonesia' => 1500000000000,
            'Spain' => 1700000000000,
            'Saudi Arabia' => 1100000000000,
            'Turkey' => 1200000000000,
            'Netherlands' => 1100000000000,
            'Switzerland' => 930000000000,
            'Poland' => 850000000000,
            'Belgium' => 670000000000,
            'Argentina' => 650000000000,
            'Thailand' => 550000000000,
            'Nigeria' => 480000000000,
            'Vietnam' => 470000000000,
            'Malaysia' => 440000000000,
            'Philippines' => 470000000000,
            'Singapore' => 530000000000,
            'South Africa' => 410000000000,

        ];

        foreach (Country::all() as $country) {

            $population = $country->population ?? rand(1000000,150000000);

            /*
            |----------------------------------------------------------
            | GDP
            |----------------------------------------------------------
            */

            if (isset($realEconomy[$country->country_name])) {

                $gdp = $realEconomy[$country->country_name];

            } else {

                switch ($country->region) {

                    case 'Europe':
                        $gdpPerCapita = rand(25000,55000);
                        break;

                    case 'Americas':
                        $gdpPerCapita = rand(10000,35000);
                        break;

                    case 'Asia':
                        $gdpPerCapita = rand(4000,30000);
                        break;

                    case 'Africa':
                        $gdpPerCapita = rand(1500,9000);
                        break;

                    case 'Oceania':
                        $gdpPerCapita = rand(25000,60000);
                        break;

                    default:
                        $gdpPerCapita = rand(7000,20000);
                        break;

                }

                $gdp = $population * $gdpPerCapita;

            }

            /*
            |----------------------------------------------------------
            | Inflation
            |----------------------------------------------------------
            */

            switch ($country->region) {

                case 'Europe':
                    $inflation = rand(15,45)/10;
                    break;

                case 'Americas':
                    $inflation = rand(20,70)/10;
                    break;

                case 'Asia':
                    $inflation = rand(10,60)/10;
                    break;

                case 'Africa':
                    $inflation = rand(40,150)/10;
                    break;

                default:
                    $inflation = rand(20,70)/10;

            }

            /*
            |----------------------------------------------------------
            | Export & Import
            |----------------------------------------------------------
            */

            $exports = $gdp * (rand(20,45)/100);

            $imports = $gdp * (rand(18,42)/100);

            EconomicData::create([

                'country_id' => $country->id,

                'gdp' => round($gdp,2),

                'inflation' => round($inflation,2),

                'exports' => round($exports,2),

                'imports' => round($imports,2),

                'data_year' => 2025,

            ]);

        }

        $this->command->info('Economic Data seeded successfully.');
    }
}