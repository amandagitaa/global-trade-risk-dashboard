<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\routes\web.php';
$content = file_get_contents($file);

$content = str_replace(
    "[ReportExportController::class, 'comparisonView']",
    "[ReportExportController::class, 'comparisonDetail']",
    $content
);
file_put_contents($file, $content);
echo "Routes updated.\n";
