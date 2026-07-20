<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\routes\web.php';
$content = file_get_contents($file);

$content = str_replace(
    "Route::get('reports/compare/history', [ReportExportController::class, 'compareHistory'])->name('compare.history');",
    "Route::get('reports/compare/history', [ReportExportController::class, 'compareHistory'])->name('compare.history');\n    Route::get('reports/compare/export/pdf-all', [ReportExportController::class, 'comparisonPdfAll'])->name('compare.pdf.all');\n    Route::get('reports/compare/export/excel-all', [ReportExportController::class, 'comparisonExcelAll'])->name('compare.excel.all');",
    $content
);
file_put_contents($file, $content);
echo "Routes updated.\n";
