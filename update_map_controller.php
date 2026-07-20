<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\app\Http\Controllers\DashboardController.php';
$content = file_get_contents($file);

$content = str_replace(
    "'map_data' => \$data['mapCountries'],",
    "'map_data' => \$data['mapCountries'],\n                'selected_country_id' => \$countryId,",
    $content
);

file_put_contents($file, $content);
echo "DashboardController updated.\n";
