<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\routes\web.php';
$content = file_get_contents($file);

if (strpos($content, "compare.index") === false) {
    $content .= "\nRoute::get('/compare', [\App\Http\Controllers\CountryComparisonController::class, 'index'])->name('compare.index');\n";
    file_put_contents($file, $content);
    echo "Appended compare route.\n";
} else {
    echo "Compare route already exists.\n";
}
