<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\app\Http\Controllers\DashboardController.php';
$content = file_get_contents($file);

$content = str_replace(
    "'insight_html' => view('dashboard.components.trade-insight', \$data)->render(),",
    "",
    $content
);

file_put_contents($file, $content);
echo "DashboardController updated.\n";
