<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\reports\index.blade.php';
$content = file_get_contents($file);

// Fix Trade Planner missing routes by replacing the buttons with "Coming Soon" or removing the invalid route calls
$tradePlannerHtmlOld = <<<'EOD'
                            <a href="{{ route('reports.trade-planner.view') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-eye me-1"></i> View
                            </a>
                            <a href="{{ route('reports.trade-planner.pdf') }}" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                            </a>
                            <a href="{{ route('reports.trade-planner.excel') }}" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-file-earmark-excel me-1"></i> Excel
                            </a>
EOD;

$tradePlannerHtmlNew = <<<'EOD'
                            <a href="#" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn disabled">
                                <i class="bi bi-hourglass-split me-1"></i> Coming Soon
                            </a>
EOD;

$content = str_replace($tradePlannerHtmlOld, $tradePlannerHtmlNew, $content);

// Remove the 'reports.' prefix for all other routes
$content = str_replace("route('reports.risk-analysis.", "route('risk-analysis.", $content);
$content = str_replace("route('reports.countries.", "route('countries.", $content);
$content = str_replace("route('reports.weather.", "route('weather.", $content);
$content = str_replace("route('reports.currency.", "route('currency.", $content);
$content = str_replace("route('reports.economy.", "route('economy.", $content);
$content = str_replace("route('reports.ports.", "route('ports.", $content);
$content = str_replace("route('reports.watch-list.", "route('watch-list.", $content);
$content = str_replace("route('reports.news.", "route('news.", $content);

file_put_contents($file, $content);
echo "Reports index fixed.\n";
