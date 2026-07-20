<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "--- Backend Audit ---\n";

$countries = \App\Models\Country::with('ports')->get();

foreach ($countries as $country) {
    $portIds = $country->ports->pluck('id')->toArray();
    if (count($portIds) == 0) continue;
    
    $ships = \App\Models\Ship::whereIn('current_port_id', $portIds)->get();
    
    if ($ships->count() > 1) {
        echo "\nCountry: " . $country->country_name . "\n";
        echo "Total Ports: " . count($portIds) . "\n";
        echo "Active Ships: " . $ships->count() . "\n";
        
        $coords = [];
        foreach ($ships as $ship) {
            $lat = $ship->latitude;
            $lng = $ship->longitude;
            $coordKey = "$lat,$lng";
            
            if (!isset($coords[$coordKey])) {
                $coords[$coordKey] = 0;
            }
            $coords[$coordKey]++;
            
            echo "- Ship: " . $ship->ship_name . " (Lat: " . $lat . ", Lng: " . $lng . ")\n";
        }
        
        echo "Unique Coordinates: " . count($coords) . "\n";
        foreach ($coords as $k => $v) {
            if ($v > 1) {
                echo "  WARNING: $v ships have exactly same coordinate ($k)!\n";
            }
        }
    }
}
