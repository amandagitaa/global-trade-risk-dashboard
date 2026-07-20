<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\routes\web.php';
$content = file_get_contents($file);

$content = str_replace(
    "Route::get('/news', [NewsController::class, 'index'])->name('news.index');",
    "Route::get('/news', [NewsController::class, 'index'])->name('news.index');\n    Route::get('/news/sync', [NewsController::class, 'sync'])->name('news.sync');",
    $content
);
file_put_contents($file, $content);
echo "News sync route updated.\n";
