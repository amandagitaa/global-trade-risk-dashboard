<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\country-comparison\index.blade.php';
$content = file_get_contents($file);

$content = str_replace(
    'action="{{ route(\'country-comparison.index\') }}"',
    'action="{{ route(\'compare.index\') }}"',
    $content
);

file_put_contents($file, $content);
echo "View updated to compare.index route.\n";
