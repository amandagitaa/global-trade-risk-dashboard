<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$user = App\Models\User::first();
Auth::login($user);

$response = $kernel->handle(Illuminate\Http\Request::create('/news/afghanistan-faces-international-trade-pressure-0VFC1yc5', 'GET'));
echo "STATUS: " . $response->getStatusCode() . "\n";
$content = $response->getContent();
if (strpos($content, 'Afghanistan faces international trade pressure') !== false) {
    echo "CONTENT: Title successfully rendered in view.\n";
} else {
    echo "CONTENT: Title NOT found in view.\n";
}