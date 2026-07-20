<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\dashboard\index.blade.php';
$content = file_get_contents($file);

// Remove the trade-insight div and make news-panel full width
$content = preg_replace(
    '/<div class="row mt-4">\s*<div class="col-lg-6" id="dashboard-trade-insight">\s*@include\(\'dashboard\.components\.trade-insight\'\)\s*<\/div>\s*<div class="col-lg-6 mt-4 mt-lg-0" id="dashboard-news-panel">\s*@include\(\'dashboard\.components\.news-panel\'\)\s*<\/div>\s*<\/div>/',
    '<div class="row mt-4">
    <div class="col-12" id="dashboard-news-panel">
        @include(\'dashboard.components.news-panel\')
    </div>
</div>',
    $content
);

// Remove trade-insight JS update
$content = str_replace(
    "document.getElementById('dashboard-trade-insight').innerHTML = data.insight_html;",
    "",
    $content
);

file_put_contents($file, $content);
echo "index.blade.php updated.\n";
