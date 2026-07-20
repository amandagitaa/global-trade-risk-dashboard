<?php
$file = 'C:\laragon\www\global-trade-risk-dashboard\resources\views\dashboard\index.blade.php';
$content = file_get_contents($file);

$pattern = '/\{\{-- ==========================================\s+Header\s+========================================== --\}\}.*?<\/div>\s+<\/div>/s';
$content = preg_replace($pattern, '', $content);

file_put_contents($file, $content);
echo "Welcome section removed from dashboard/index.blade.php\n";
