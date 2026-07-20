<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\routes\web.php';
$content = file_get_contents($file);

$content = str_replace(
    "Route::get('reports/compare/view', [ReportExportController::class, 'comparisonView'])->name('compare.view');",
    "Route::get('reports/compare/view/{id}', [ReportExportController::class, 'comparisonView'])->name('compare.view');",
    $content
);
$content = str_replace(
    "Route::get('reports/compare/export/pdf', [ReportExportController::class, 'comparisonPdf'])->name('compare.pdf');",
    "Route::get('reports/compare/export/pdf/{id}', [ReportExportController::class, 'comparisonPdf'])->name('compare.pdf');",
    $content
);
$content = str_replace(
    "Route::get('reports/compare/export/excel', [ReportExportController::class, 'comparisonExcel'])->name('compare.excel');",
    "Route::get('reports/compare/export/excel/{id}', [ReportExportController::class, 'comparisonExcel'])->name('compare.excel');",
    $content
);

file_put_contents($file, $content);
echo "Routes updated.\n";
