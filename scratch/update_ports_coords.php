<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$ports = \App\Models\Port::all();
$coords = [];
$count = 0;

foreach ($ports as $port) {
    $key = $port->latitude . ',' . $port->longitude;
    if (isset($coords[$key])) {
        // Duplicate coordinate found, add small offset to DB
        $port->latitude = $port->latitude + (rand(-10, 10) / 1000);
        $port->longitude = $port->longitude + (rand(-10, 10) / 1000);
        $port->save();
        $count++;
    } else {
        $coords[$key] = true;
    }
}
echo "$count Ports updated with slight offsets in the database to prevent stacking.\n";
