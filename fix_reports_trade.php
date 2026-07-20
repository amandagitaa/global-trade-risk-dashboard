<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\reports\index.blade.php';
$content = file_get_contents($file);

$content = preg_replace(
    "/<a href=\"\{\{ route\('reports\.trade-planner\.view'\) \}\}\".*?<\/a>\s*<a href=\"\{\{ route\('reports\.trade-planner\.pdf'\) \}\}\".*?<\/a>\s*<a href=\"\{\{ route\('reports\.trade-planner\.excel'\) \}\}\".*?<\/a>/is",
    "<a href=\"#\" class=\"btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn disabled\"><i class=\"bi bi-hourglass-split me-1\"></i> Coming Soon</a>",
    $content
);

file_put_contents($file, $content);
echo "Trade Planner buttons replaced.\n";
