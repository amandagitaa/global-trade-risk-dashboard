<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $controller = app()->make(\App\Http\Controllers\ReportExportController::class);
    
    // Test Excel
    $excelResponse = $controller->tradePlannerExcel();
    echo "Excel generated class: " . get_class($excelResponse) . "\n";
    
    // Test PDF
    $pdfResponse = $controller->tradePlannerPdf();
    echo "PDF generated class: " . get_class($pdfResponse) . "\n";
    
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
}
