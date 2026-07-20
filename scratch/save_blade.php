<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$country = \App\Models\Country::with('ports')->where('country_name', 'Korea (Republic of)')->first();
$service = app(\App\Services\CountryDetailService::class);
$data = $service->build($country);

$view = view('countries.components.map', $data)->render();

file_put_contents('scratch/rendered_map.html', $view);
echo "Rendered view saved to scratch/rendered_map.html\n";
