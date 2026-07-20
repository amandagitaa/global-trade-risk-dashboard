<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\routes\web.php';
$content = file_get_contents($file);

// Add the use statement if missing
if (strpos($content, 'App\Http\Controllers\CountryComparisonController') === false) {
    $content = str_replace(
        "use App\Http\Controllers\CountryController;",
        "use App\Http\Controllers\CountryController;\nuse App\Http\Controllers\CountryComparisonController;",
        $content
    );
}

// Add the route using regex to ensure it matches the actual content
if (strpos($content, "compare.index") === false) {
    $content = preg_replace(
        "/(Route::get\('\/countries', \[CountryController::class,'index'\])\r?\n(        ->name\('countries.index'\);)/s",
        "$1\n$2\n\n    Route::get('/compare', [CountryComparisonController::class, 'index'])->name('compare.index');",
        $content
    );
}

file_put_contents($file, $content);
echo "web.php fixed.\n";
