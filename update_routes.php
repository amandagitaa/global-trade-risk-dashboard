<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\routes\web.php';
$content = file_get_contents($file);

if (strpos($content, "use App\Http\Controllers\CountryComparisonController;") === false) {
    $content = str_replace(
        "use App\Http\Controllers\CountryController;",
        "use App\Http\Controllers\CountryController;\nuse App\Http\Controllers\CountryComparisonController;",
        $content
    );
}

if (strpos($content, "Route::get('/country-comparison'") === false) {
    $routeCode = <<<EOD
    Route::get('/countries/search', [CountryController::class,'search'])
        ->name('countries.search');

    Route::get('/country-comparison', [CountryComparisonController::class, 'index'])
        ->name('country-comparison.index');
EOD;

    $content = str_replace(
        "Route::get('/countries/search', [CountryController::class,'search'])\n        ->name('countries.search');",
        $routeCode,
        $content
    );
}

file_put_contents($file, $content);
echo "Routes updated.\n";
