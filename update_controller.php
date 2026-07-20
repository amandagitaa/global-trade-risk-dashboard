<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\app\Http\Controllers\DashboardController.php';
$content = file_get_contents($file);

$content = str_replace(
    "'recommendation_html' => view('dashboard.components.recommendation-panel', \$data)->render(),",
    "'port_risk_html' => view('dashboard.components.port-risk-panel', \$data)->render(),",
    $content
);

file_put_contents($file, $content);
echo "DashboardController updated.\n";
