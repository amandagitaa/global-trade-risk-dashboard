<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RiskScoreSeeder extends Seeder
{
    public function run(): void
    {

        $countries = DB::table('countries')->get();

        foreach ($countries as $country) {

            $weather = rand(10,70);
            $port = rand(10,60);
            $currency = rand(10,70);
            $economic = rand(10,60);
            $news = rand(10,70);


            $final = round(
                (
                    $weather +
                    $port +
                    $currency +
                    $economic +
                    $news
                ) / 5
            );


            if ($final >= 50) {

                $level = 'alert';
                $reason = 'High trade risk detected';

            } elseif ($final >= 30) {

                $level = 'stable';
                $reason = 'Moderate trade condition';

            } else {

                $level = 'safe';
                $reason = 'Low trade risk';

            }


            DB::table('risk_scores')->insert([

                'country_id'=>$country->id,

                'weather_score'=>$weather,

                'port_score'=>$port,

                'currency_score'=>$currency,

                'economic_score'=>$economic,

                'news_score'=>$news,

                'final_score'=>$final,

                'risk_level'=>$level,

                'reason'=>$reason,

                'created_at'=>now(),

                'updated_at'=>now()

            ]);

        }

    }
}