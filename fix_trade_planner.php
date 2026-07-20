<?php
// 1. Fix ReportController
$reportControllerFile = 'c:\laragon\www\global-trade-risk-dashboard\app\Http\Controllers\ReportController.php';
$rcContent = file_get_contents($reportControllerFile);
$rcContent = str_replace(
    '$tradeSimulations = ShippingRoute::count(); // Using Shipping Routes as proxy for simulations',
    '$tradeSimulations = \App\Models\TradeSimulation::where(\'user_id\', auth()->id())->count();',
    $rcContent
);
file_put_contents($reportControllerFile, $rcContent);

// 2. Add Routes to web.php
$webPhpFile = 'c:\laragon\www\global-trade-risk-dashboard\routes\web.php';
$webContent = file_get_contents($webPhpFile);
if (strpos($webContent, "name('trade-planner.view')") === false) {
    $routesToAdd = <<<EOD
    Route::get('reports/trade-planner/view', [ReportExportController::class, 'tradePlannerView'])->name('trade-planner.view');
    Route::get('reports/trade-planner/export/pdf', [ReportExportController::class, 'tradePlannerPdf'])->name('trade-planner.pdf');
    Route::get('reports/trade-planner/export/excel', [ReportExportController::class, 'tradePlannerExcel'])->name('trade-planner.excel');
EOD;
    $webContent = str_replace(
        "// Export Routes",
        "// Export Routes\n" . $routesToAdd,
        $webContent
    );
    file_put_contents($webPhpFile, $webContent);
}

// 3. Restore buttons in index.blade.php
$indexFile = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\reports\index.blade.php';
$indexContent = file_get_contents($indexFile);
$comingSoonBtn = '<a href="#" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn disabled"><i class="bi bi-hourglass-split me-1"></i> Coming Soon</a>';
$threeBtns = <<<EOD
<a href="{{ route('trade-planner.view') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn"><i class="bi bi-eye me-1"></i> View</a>
                            <a href="{{ route('trade-planner.pdf') }}" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn"><i class="bi bi-file-earmark-pdf me-1"></i> Export PDF</a>
                            <a href="{{ route('trade-planner.excel') }}" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn"><i class="bi bi-file-earmark-excel me-1"></i> Export Excel</a>
EOD;
$indexContent = str_replace($comingSoonBtn, $threeBtns, $indexContent);
file_put_contents($indexFile, $indexContent);

// 4. Fix routes in export view
$exportViewFile = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\reports\exports\trade-planner.blade.php';
$exportContent = file_get_contents($exportViewFile);
$exportContent = str_replace("route('reports.trade-planner.pdf')", "route('trade-planner.pdf')", $exportContent);
$exportContent = str_replace("route('reports.trade-planner.excel')", "route('trade-planner.excel')", $exportContent);
file_put_contents($exportViewFile, $exportContent);

echo "All tasks completed.\n";
