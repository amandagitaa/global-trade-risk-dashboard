<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\dashboard\index.blade.php';
$content = file_get_contents($file);

$content = str_replace(
    "if (typeof window.updateMapMarkers === 'function' && data.map_data) {
            window.updateMapMarkers(data.map_data);
        }",
    "if (typeof window.updateMapMarkers === 'function' && data.map_data) {
            window.updateMapMarkers(data.map_data, data.selected_country_id);
        }",
    $content
);

file_put_contents($file, $content);
echo "index.blade.php updated.\n";
