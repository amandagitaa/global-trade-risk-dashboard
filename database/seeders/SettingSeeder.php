<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'system_name', 'value' => 'Global Trade Risk Intelligence Platform'],
            ['key' => 'company_name', 'value' => 'Global Trade'],
            ['key' => 'timezone', 'value' => 'Asia/Jakarta'],
            ['key' => 'risk_notification', 'value' => '1'],
            ['key' => 'weather_notification', 'value' => '1'],
            ['key' => 'currency_notification', 'value' => '1'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
