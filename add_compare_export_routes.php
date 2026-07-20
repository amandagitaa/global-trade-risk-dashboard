<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\routes\web.php';
$content = file_get_contents($file);

if (strpos($content, "name('compare.view')") === false) {
    $routesToAdd = <<<EOD
    Route::get('reports/compare/view', [ReportExportController::class, 'comparisonView'])->name('compare.view');
    Route::get('reports/compare/export/pdf', [ReportExportController::class, 'comparisonPdf'])->name('compare.pdf');
    Route::get('reports/compare/export/excel', [ReportExportController::class, 'comparisonExcel'])->name('compare.excel');
EOD;
    $content = str_replace(
        "// Export Routes",
        "// Export Routes\n" . $routesToAdd,
        $content
    );
    file_put_contents($file, $content);
    echo "Added compare export routes\n";
}
