<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\dashboard\components\world-map.blade.php';
$content = file_get_contents($file);

$content = str_replace(
    ".marker-alert {\n    filter: hue-rotate(185deg) saturate(3) brightness(1.2);\n}",
    ".marker-alert {\n    filter: hue-rotate(200deg) saturate(3) brightness(1.3);\n}",
    $content
);

file_put_contents($file, $content);
echo "world-map CSS updated to yellow.\n";
