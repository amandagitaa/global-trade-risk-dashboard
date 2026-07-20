<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\country-comparison\index.blade.php';
$content = file_get_contents($file);

$content = str_replace(
    '<button type="submit" class="btn btn-orange text-white btn-lg px-4 shadow-sm w-100">',
    '<button type="submit" class="btn btn-warning btn-lg px-4 shadow-sm w-100 fw-bold text-dark">',
    $content
);

file_put_contents($file, $content);
echo "Button fixed in index.blade.php\n";
