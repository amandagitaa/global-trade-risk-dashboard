<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\app\Http\Controllers\ReportExportController.php';
$content = file_get_contents($file);

$content = str_replace(
    "return view('reports.exports.country-comparison', compact('data'));",
    "return view('reports.exports.country-comparison', compact('data', 'id'));",
    $content
);
file_put_contents($file, $content);

$file2 = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\reports\exports\country-comparison.blade.php';
$content2 = file_get_contents($file2);

$content2 = str_replace(
    "route('compare.pdf')",
    "route('compare.pdf', \$id)",
    $content2
);
$content2 = str_replace(
    "route('compare.excel')",
    "route('compare.excel', \$id)",
    $content2
);
file_put_contents($file2, $content2);

echo "Fixed View export buttons.\n";
