<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\country-comparison\index.blade.php';
$content = file_get_contents($file);

$oldButtons = <<<'EOD'
    <button class="btn btn-outline-danger px-4"><i class="bi bi-file-pdf"></i> Export PDF</button>
    <button class="btn btn-outline-success px-4"><i class="bi bi-file-excel"></i> Export Excel</button>
EOD;

$newButtons = <<<'EOD'
    <a href="{{ route('compare.export.live.pdf', ['country_a' => $countryA->id, 'country_b' => $countryB->id]) }}" class="btn btn-outline-danger px-4" target="_blank"><i class="bi bi-file-pdf"></i> Export PDF</a>
    <a href="{{ route('compare.export.live.excel', ['country_a' => $countryA->id, 'country_b' => $countryB->id]) }}" class="btn btn-outline-success px-4"><i class="bi bi-file-excel"></i> Export Excel</a>
EOD;

$content = str_replace($oldButtons, $newButtons, $content);

file_put_contents($file, $content);
echo "Buttons updated in compare view.\n";
