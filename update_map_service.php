<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\app\Services\DashboardService.php';
$content = file_get_contents($file);

// Replace getMapCountries so it does not filter by $countryId
$content = preg_replace(
    '/private function getMapCountries\(\$countryId\)\s*\{\s*\$query = Country::select\([^)]+\)\s*->with\([^)]+\);\s*if \(\$countryId\) \{\s*\$query->where\(\'id\', \$countryId\);\s*\}\s*return \$query->orderBy\(\'country_name\'\)->get\(\);\s*\}/',
    "private function getMapCountries(\$countryId)\n    {\n        \$query = Country::select('id', 'country_name', 'country_code', 'flag', 'currency_code', 'latitude', 'longitude', 'region')\n            ->with(['latestRisk', 'recommendation', 'latestWeather', 'latestCurrency']);\n            \n        // intentionally NOT filtering by countryId to keep all markers on map\n\n        return \$query->orderBy('country_name')->get();\n    }",
    $content
);

file_put_contents($file, $content);
echo "DashboardService map updated.\n";
