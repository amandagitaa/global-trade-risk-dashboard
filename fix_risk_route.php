<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\routes\web.php';
$content = file_get_contents($file);

if (strpos($content, "name('risk-analysis.view')") === false) {
    $content = str_replace(
        "Route::get('risk-analysis/pdf'",
        "Route::get('risk-analysis/view', [ReportExportController::class, 'riskAnalysisView'])->name('risk-analysis.view');\n    Route::get('risk-analysis/pdf'",
        $content
    );
    file_put_contents($file, $content);
    echo "Added risk-analysis.view to web.php\n";
} else {
    echo "Route already exists.\n";
}
