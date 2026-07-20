<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\reports\index.blade.php';
$content = file_get_contents($file);

preg_match_all('/route\(\'(.*?)\'\)/', $content, $matches);
$routes = array_unique($matches[1]);

$missing = [];
foreach ($routes as $route) {
    if (!\Illuminate\Support\Facades\Route::has($route)) {
        $missing[] = $route;
    }
}

if (empty($missing)) {
    echo "All routes in Reports are valid and registered!\n";
} else {
    echo "Missing routes:\n" . implode("\n", $missing) . "\n";
}
