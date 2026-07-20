<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\routes\web.php';
$content = file_get_contents($file);

if (strpos($content, 'App\Http\Controllers\CountryComparisonController') === false) {
    $content = str_replace(
        "use App\Http\Controllers\CountryController;",
        "use App\Http\Controllers\CountryController;\nuse App\Http\Controllers\CountryComparisonController;",
        $content
    );
}

if (strpos($content, 'compare.index') === false) {
    $content = str_replace(
        "Route::get('/countries', [CountryController::class,'index'])\n        ->name('countries.index');",
        "Route::get('/countries', [CountryController::class,'index'])\n        ->name('countries.index');\n\n    Route::get('/compare', [CountryComparisonController::class, 'index'])\n        ->name('compare.index');",
        $content
    );
}

file_put_contents($file, $content);
echo "web.php patched correctly.\n";
