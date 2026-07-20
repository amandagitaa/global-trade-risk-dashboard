<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\routes\web.php';
$content = file_get_contents($file);

if (strpos($content, 'App\Http\Controllers\CountryComparisonController') === false) {
    $content = preg_replace(
        '/use App\\\\Http\\\\Controllers\\\\CountryController;/',
        "use App\\Http\\Controllers\\CountryController;\r\nuse App\\Http\\Controllers\\CountryComparisonController;",
        $content
    );
}

if (strpos($content, 'compare.index') === false) {
    $content = preg_replace(
        "/(Route::get\('\/countries', \[CountryController::class,'index'\])\r?\n(        ->name\('countries.index'\);)/",
        "$1\r\n$2\r\n\r\n    Route::get('/compare', [CountryComparisonController::class, 'index'])\r\n        ->name('compare.index');",
        $content
    );
}

file_put_contents($file, $content);
echo "web.php patched correctly using regex.\n";
