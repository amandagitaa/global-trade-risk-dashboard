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
                ($weather * 0.25) +
                ($currency * 0.20) +
                ($economic * 0.15) +
                ($port * 0.20) +
                ($news * 0.20), 2
            );

            if ($final <= 20) {
                $level = 'safe';
                $reason = 'Low trade risk';
            } elseif ($final <= 40) {
                $level = 'stable';
                $reason = 'Moderate trade condition';
            } elseif ($final <= 60) {
                $level = 'alert';
                $reason = 'Elevated trade risk detected';
            } elseif ($final <= 80) {
                $level = 'dangerous';
                $reason = 'High trade risk detected';
            } else {
                $level = 'critical';
                $reason = 'Critical trade risk detected';
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