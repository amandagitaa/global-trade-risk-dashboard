<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\layouts\sidebar.blade.php';
$content = file_get_contents($file);

$content = str_replace(
    '<a href="{{ route(\'country-comparison.index\') }}"
       class="{{ request()->routeIs(\'country-comparison.*\') ? \'active\' : \'\' }}">

        <i class="bi bi-arrow-left-right"></i>

        Country Comparison

    </a>',
    '<a href="{{ route(\'compare.index\') }}"
       class="{{ request()->routeIs(\'compare.*\') ? \'active\' : \'\' }}">

        <i class="bi bi-arrow-left-right"></i>

        Compare

    </a>',
    $content
);

file_put_contents($file, $content);
echo "Sidebar updated to Compare.\n";
