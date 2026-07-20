<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\routes\web.php';
$content = file_get_contents($file);

if (strpos($content, "Route::get('/compare', [CountryComparisonController::class, 'index'])") === false) {
    // Add right after Route::get('/countries', ...)
    $search = "Route::get('/countries', [CountryController::class,'index'])\n        ->name('countries.index');";
    $replace = $search . "\n\n    Route::get('/compare', [CountryComparisonController::class, 'index'])\n        ->name('compare.index');";
    
    $content = str_replace($search, $replace, $content);
}

file_put_contents($file, $content);
echo "Routes updated.\n";
