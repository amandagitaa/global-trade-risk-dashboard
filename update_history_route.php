<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\routes\web.php';
$content = file_get_contents($file);

$content = str_replace(
    "Route::get('reports/compare/view/{id}', [ReportExportController::class, 'comparisonDetail'])->name('compare.view');",
    "Route::get('reports/compare/history', [ReportExportController::class, 'compareHistory'])->name('compare.history');\n    Route::get('reports/compare/view/{id}', [ReportExportController::class, 'comparisonDetail'])->name('compare.view');",
    $content
);
file_put_contents($file, $content);
echo "Routes updated.\n";
