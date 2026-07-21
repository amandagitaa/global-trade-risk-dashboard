<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$c = \App\Models\Country::where('country_code', 'DE')->first();
if ($c) {
    try {
        app(\App\Services\CountryDetailService::class)->build($c);
        echo "Success";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Country not found";
}
