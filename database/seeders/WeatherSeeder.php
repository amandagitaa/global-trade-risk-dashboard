<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Country;

class WeatherSeeder extends Seeder
{
    public function run(): void
    {

        $countries = Country::all();

        foreach ($countries as $country) {

            DB::table('weather_data')->insert([

                'country_id' => $country->id,

                'temperature' => rand(15,35),

                'rainfall' => rand(50,250),

                'wind_speed' => rand(5,40),

                'storm_risk' => rand(5,80),

                'weather_status' => $this->getWeatherStatus(),

                'recorded_at' => now(),

                'created_at' => now(),

                'updated_at' => now(),

            ]);

        }

    }


    private function getWeatherStatus()
{
    $status = [
        'clear',
        'rain'
    ];

    return $status[array_rand($status)];
}
}