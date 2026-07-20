<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\routes\web.php';
$content = file_get_contents($file);

$content = str_replace(
    "Route::get('/compare', [CountryComparisonController::class, 'index'])->name('compare.index');\n    Route::post('/compare/save', [CountryComparisonController::class, 'save'])->name('compare.save');",
    "Route::get('/compare', [CountryComparisonController::class, 'index'])->name('compare.index');\n    Route::post('/compare/save', [CountryComparisonController::class, 'save'])->name('compare.save');\n    Route::get('/compare/export/pdf', [ReportExportController::class, 'compareLivePdf'])->name('compare.export.live.pdf');\n    Route::get('/compare/export/excel', [ReportExportController::class, 'compareLiveExcel'])->name('compare.export.live.excel');",
    $content
);
file_put_contents($file, $content);
echo "Live export routes added.\n";
