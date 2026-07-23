<?php

return [
    'provider' => env('NEWS_PROVIDER', 'newsdata'), // newsdata, gnews, rss
    'api_key' => env('NEWS_API_KEY', ''),
    'timeout' => (int) env('NEWS_TIMEOUT', 10),
    'retry_times' => (int) env('NEWS_RETRY_TIMES', 2),
    'retry_sleep' => (int) env('NEWS_RETRY_SLEEP', 100), // ms
    'language' => env('NEWS_LANGUAGE', 'en'),
    'country' => env('NEWS_COUNTRY', ''),
    'max_results' => (int) env('NEWS_MAX_RESULT', 50),
    'batch_size' => (int) env('NEWS_BATCH_SIZE', 50),
    'cache_ttl' => (int) env('NEWS_CACHE_TTL', 3600), // seconds
    'sync_frequency' => env('NEWS_SYNC_FREQUENCY', '0 */6 * * *'),
    
    // Dynamic categories configuration for NewsSync
    'category_limit' => 10,
    'categories' => [
        'Business',
        'Trade',
        'Technology',
        'Shipping',
        'Logistics',
        'Manufacturing',
        'Energy',
        'Geopolitics',
    ],
];
