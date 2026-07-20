<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$country = \App\Models\Country::with('ports')->where('country_name', 'South Korea')->first();
if(!$country) {
    echo "South Korea not found\n";
    exit;
}
$portIds = $country->ports->pluck('id')->toArray();
$ships = \App\Models\Ship::whereIn('current_port_id', $portIds)->get();

echo "Total Ports: " . count($portIds) . "\n";
echo "Active Ships: " . $ships->count() . "\n";

foreach($ships as $ship) {
    echo "Ship: " . $ship->ship_name . " (Lat: " . $ship->latitude . ", Lng: " . $ship->longitude . ")\n";
}
