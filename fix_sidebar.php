<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\layouts\sidebar.blade.php';
$content = file_get_contents($file);

$search = <<<EOD
    <a href="{{ route('countries.index') }}"
       class="{{ request()->routeIs('countries.*') ? 'active' : '' }}">

        <i class="bi bi-globe2"></i>

        Countries

    </a>
EOD;

$replace = <<<EOD
    <a href="{{ route('countries.index') }}"
       class="{{ request()->routeIs('countries.*') ? 'active' : '' }}">

        <i class="bi bi-globe2"></i>

        Countries

    </a>

    <a href="{{ route('compare.index') }}"
       class="{{ request()->routeIs('compare.*') ? 'active' : '' }}">

        <i class="bi bi-arrow-left-right"></i>

        Compare

    </a>
EOD;

// If exact match fails due to line endings, use regex
if (strpos($content, "Compare") === false) {
    $content = preg_replace(
        '/<a href="\{\{ route\(\'countries.index\'\) \}\}".*?<\/a>/s',
        $replace,
        $content
    );
}

file_put_contents($file, $content);
echo "Sidebar fixed.\n";
