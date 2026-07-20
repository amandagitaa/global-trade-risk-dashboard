<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$country = \App\Models\Country::with('ports')->where('country_name', 'Korea (Republic of)')->first();
$service = app(\App\Services\CountryDetailService::class);
$data = $service->build($country);

$view = view('countries.components.map', $data)->render();
$shipsInJS = substr_count($view, 'var shipLatlng');
$portsInJS = substr_count($view, 'var portLatlng');

echo "Ships in JS: " . $shipsInJS . "\n";
echo "Ports in JS: " . $portsInJS . "\n";
