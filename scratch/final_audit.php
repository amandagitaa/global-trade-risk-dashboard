<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "--- FINAL AUDIT REPORT ---\n";

$country = \App\Models\Country::with('ports')->where('country_name', 'Korea (Republic of)')->first();

echo "1. Country Marker: 1 (Lat: {$country->latitude}, Lng: {$country->longitude})\n";

$ports = $country->ports;
echo "2. Port Markers: " . $ports->count() . "\n";
foreach ($ports as $port) {
    echo "   - {$port->name}: {$port->latitude}, {$port->longitude}\n";
}

$portIds = $ports->pluck('id')->toArray();
$ships = \App\Models\Ship::with('currentPort')->whereIn('current_port_id', $portIds)->get();

echo "3. Ship Markers: " . $ships->count() . "\n";
foreach ($ships as $ship) {
    echo "   - {$ship->ship_name}: {$ship->latitude}, {$ship->longitude} (Near {$ship->currentPort->name})\n";
}

$invalidCoords = 0;
foreach ($ships as $ship) {
    if (!$ship->latitude || !$ship->longitude || $ship->latitude == 0 || $ship->longitude == 0) {
        $invalidCoords++;
    }
}
echo "4. Invalid/NULL/0,0 Coords: " . $invalidCoords . "\n";
