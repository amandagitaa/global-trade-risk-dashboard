<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\layouts\sidebar.blade.php';
$content = file_get_contents($file);

$menuCode = <<<EOD
    <a href="{{ route('countries.index') }}"
       class="{{ request()->routeIs('countries.*') ? 'active' : '' }}">

        <i class="bi bi-globe2"></i>

        Countries

    </a>

    <a href="{{ route('country-comparison.index') }}"
       class="{{ request()->routeIs('country-comparison.*') ? 'active' : '' }}">

        <i class="bi bi-arrow-left-right"></i>

        Country Comparison

    </a>
EOD;

$content = str_replace(
    '<a href="{{ route(\'countries.index\') }}"
       class="{{ request()->routeIs(\'countries.*\') ? \'active\' : \'\' }}">

        <i class="bi bi-globe2"></i>

        Countries

    </a>',
    $menuCode,
    $content
);

file_put_contents($file, $content);
echo "Sidebar updated.\n";
