<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\app\Http\Controllers\CountryComparisonController.php';
$content = file_get_contents($file);

$oldComparisonResult = <<<'EOD'
            'ports_b' => $countryB->ports->count(),
            'reasons' => $recommendation['reasons']
        ];
EOD;

$newComparisonResult = <<<'EOD'
            'ports_b' => $countryB->ports->count(),
            'news_sentiment_a' => clone $countryA->latestNews, // just get sentiment or use count? The user just says News Sentiment. Actually we can just get average sentiment or text.
            'news_sentiment_a' => count($countryA->latestNews) > 0 ? $countryA->latestNews->avg('sentiment_score') : 0,
            'news_sentiment_b' => count($countryB->latestNews) > 0 ? $countryB->latestNews->avg('sentiment_score') : 0,
            'reasons' => $recommendation['reasons']
        ];
EOD;

$content = str_replace($oldComparisonResult, $newComparisonResult, $content);
file_put_contents($file, $content);
echo "Added news sentiment to comparison result.\n";
