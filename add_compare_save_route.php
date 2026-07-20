<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\routes\web.php';
$content = file_get_contents($file);

if (strpos($content, "name('compare.save')") === false) {
    $content = str_replace(
        "Route::get('/compare', [CountryComparisonController::class, 'index'])->name('compare.index');",
        "Route::get('/compare', [CountryComparisonController::class, 'index'])->name('compare.index');\n    Route::post('/compare/save', [CountryComparisonController::class, 'save'])->name('compare.save');",
        $content
    );
    file_put_contents($file, $content);
    echo "Added compare.save route\n";
}
