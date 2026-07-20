<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\watch-list\index.blade.php';
$content = file_get_contents($file);

// Remove the functions from the loop
$patternToRemove = <<<'EOD'
                                        // Badge Class Logic
                                        function riskClass($r) {
                                            if (in_array(strtolower($r), ['low', 'safe'])) return 'success';
                                            if (in_array(strtolower($r), ['medium', 'monitoring', 'volatile'])) return 'warning text-dark';
                                            if (in_array(strtolower($r), ['high', 'warning'])) return 'danger';
                                            return 'dark'; // critical
                                        }

                                        function weatherClass($w) {
                                            $w = strtolower($w);
                                            if (str_contains($w, 'clear') || str_contains($w, 'sunny')) return 'success';
                                            if (str_contains($w, 'rain')) return 'info text-dark';
                                            if (str_contains($w, 'storm')) return 'warning text-dark';
                                            if (str_contains($w, 'extreme')) return 'danger';
                                            return 'secondary';
                                        }
EOD;

$content = str_replace($patternToRemove, '', $content);

// Add the functions at the top of the file safely
$functionsAtTop = <<<'EOD'
@php
if (!function_exists('riskClass')) {
    function riskClass($r) {
        if (in_array(strtolower($r), ['low', 'safe'])) return 'success';
        if (in_array(strtolower($r), ['medium', 'monitoring', 'volatile'])) return 'warning text-dark';
        if (in_array(strtolower($r), ['high', 'warning'])) return 'danger';
        return 'dark'; // critical
    }
}

if (!function_exists('weatherClass')) {
    function weatherClass($w) {
        $w = strtolower($w);
        if (str_contains($w, 'clear') || str_contains($w, 'sunny')) return 'success';
        if (str_contains($w, 'rain')) return 'info text-dark';
        if (str_contains($w, 'storm')) return 'warning text-dark';
        if (str_contains($w, 'extreme')) return 'danger';
        return 'secondary';
    }
}
@endphp
EOD;

$content = str_replace("@section('content')\n", "@section('content')\n\n" . $functionsAtTop . "\n", $content);

file_put_contents($file, $content);
echo "Watch List functions fixed.\n";
