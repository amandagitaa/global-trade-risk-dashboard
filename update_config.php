<?php
$services = file_get_contents('config/services.php');
if (!str_contains($services, 'news_api')) {
    $newServices = str_replace(
        'return [',
        "return [\n\n    'news_api' => [\n        'provider' => env('NEWS_API_PROVIDER', 'gnews'),\n        'key' => env('NEWS_API_KEY', ''),\n    ],",
        $services
    );
    file_put_contents('config/services.php', $newServices);
    echo "Updated services.php\n";
} else {
    echo "services.php already updated\n";
}

$env = file_get_contents('.env.example');
if (!str_contains($env, 'NEWS_API_KEY')) {
    file_put_contents('.env.example', $env . "\nNEWS_API_PROVIDER=gnews\nNEWS_API_KEY=\n");
    echo "Updated .env.example\n";
}

$envReal = file_get_contents('.env');
if (!str_contains($envReal, 'NEWS_API_KEY')) {
    file_put_contents('.env', $envReal . "\nNEWS_API_PROVIDER=gnews\nNEWS_API_KEY=\n");
    echo "Updated .env\n";
}
