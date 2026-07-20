<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\app\Http\Controllers\ReportController.php';
$content = file_get_contents($file);

$content = str_replace(
    "\$countryComparisons = \App\Models\CountryComparison::where('user_id', auth()->id())->count();",
    "\$countryComparisonsCount = \App\Models\CountryComparison::where('user_id', auth()->id())->count();\n        \$countryComparisonsList = \App\Models\CountryComparison::with(['countryA', 'countryB'])->where('user_id', auth()->id())->latest()->get();",
    $content
);

$content = str_replace(
    "'countryComparisons'",
    "'countryComparisonsCount',\n            'countryComparisonsList'",
    $content
);

// If I previously used $countryComparisons in the index.blade.php as a count, I need to update index.blade.php
file_put_contents($file, $content);
echo "ReportController updated.\n";
