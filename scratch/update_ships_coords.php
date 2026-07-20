<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$ships = \App\Models\Ship::with('currentPort')->get();
$count = 0;
foreach ($ships as $ship) {
    if ($ship->currentPort) {
        $ship->latitude = $ship->currentPort->latitude + (rand(-10, 10) / 1000);
        $ship->longitude = $ship->currentPort->longitude + (rand(-10, 10) / 1000);
        $ship->save();
        $count++;
    }
}
echo "$count Ships updated to be near their ports.\n";
