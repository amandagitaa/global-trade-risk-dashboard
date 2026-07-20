<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\reports\compare-history.blade.php';
$content = file_get_contents($file);

$pattern = '/<a href="\{\{ route\(\'compare\.pdf\'.*?<\/a>/s';
$content = preg_replace($pattern, '', $content);

$pattern = '/<a href="\{\{ route\(\'compare\.excel\'.*?<\/a>/s';
$content = preg_replace($pattern, '', $content);

file_put_contents($file, $content);
echo "History view updated.\n";
