<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\dashboard\index.blade.php';
$content = file_get_contents($file);

$content = str_replace(
    '<div class="col-12" id="dashboard-recommendation-panel">
        @include(\'dashboard.components.recommendation-panel\')
    </div>',
    '<div class="col-12" id="dashboard-port-risk-panel">
        @include(\'dashboard.components.port-risk-panel\')
    </div>',
    $content
);

$content = str_replace(
    "document.getElementById('dashboard-recommendation-panel').innerHTML = data.recommendation_html;",
    "document.getElementById('dashboard-port-risk-panel').innerHTML = data.port_risk_html;",
    $content
);

file_put_contents($file, $content);
echo "dashboard index updated.\n";
