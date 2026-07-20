<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Port;
use Illuminate\Database\Seeder;

class PortSeeder extends Seeder
{
    public function run(): void
    {
        $file = database_path('data/ports.csv');

        if (!file_exists($file)) {
            $this->command->error('ports.csv not found.');
            return;
        }

        $handle = fopen($file, 'r');

        $header = array_map(function ($value) {
            return preg_replace('/^\xEF\xBB\xBF/', '', trim($value));
        }, fgetcsv($handle));

        while (($row = fgetcsv($handle)) !== false) {

            if (count(array_filter($row)) === 0) {
                continue;
            }

            if ($row[0] === 'country_iso2') {
                continue;
            }

            if (count($row) !== count($header)) {
                $this->command->warn('Skipping invalid row: ' . implode(',', $row));
                continue;
            }

    $port = array_combine($header, $row);
        if (!isset($port['description'])) {
        $this->command->warn('Description column not found.');
        continue;
    }

    $country = Country::where(
            'country_code',
            $port['country_iso2']
        )->first();

        if (!$country) {
            continue;
}
            Port::updateOrCreate(
                [
                    'code'=>$port['code']
                ],
                [
                    'country_iso2' => $country->iso2,
                    'country_name' => $country->name,

                    'name'=>$port['name'],
                    'city'=>$port['city'],
                    'region'=>$port['region'],

                    'latitude'=>$port['latitude'],
                    'longitude'=>$port['longitude'],

                    'port_type'=>$port['port_type'],
                    'status'=>$port['status'],

                    'annual_capacity'=>$port['annual_capacity'],
                    'teu_capacity'=>$port['teu_capacity'],
                    'trade_volume'=>$port['trade_volume'],

                    'importance_score'=>$port['importance_score'],

                    'risk_score'=>$port['risk_score'],
                    'risk_level'=>$port['risk_level'],

                    'weather_risk'=>$port['weather_risk'],
                    'political_risk'=>$port['political_risk'],
                    'logistic_risk'=>$port['logistic_risk'],

                    'main_industries'=>$port['main_industries'],

                    'description'=>$port['description'],

                    'traffic_level'=>$port['traffic_level'],
                    'shipping_routes'=>$port['shipping_routes'],
                    'ai_recommendation'=>$port['ai_recommendation'],
                ]
                );
        }
        fclose($handle);

        $this->command->info('Ports imported successfully.');
    }
}