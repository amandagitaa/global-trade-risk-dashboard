<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\app\Http\Controllers\ReportExportController.php';
$content = file_get_contents($file);

$oldReturn = <<<'EOD'
            'ports_a' => $comp->comparison_result['ports_a'] ?? '-',
            'ports_b' => $comp->comparison_result['ports_b'] ?? '-',
            'risk_score_a' => $comp->risk_score_a ?? '-',
            'risk_score_b' => $comp->risk_score_b ?? '-',
            'recommendation' => $comp->recommended_country,
            'summary' => $comp->recommendation,
            'comparison_date' => $comp->created_at->format('Y-m-d H:i')
        ]];
EOD;

$newReturn = <<<'EOD'
            'ports_a' => $comp->comparison_result['ports_a'] ?? '-',
            'ports_b' => $comp->comparison_result['ports_b'] ?? '-',
            'news_sentiment_a' => $comp->comparison_result['news_sentiment_a'] ?? '-',
            'news_sentiment_b' => $comp->comparison_result['news_sentiment_b'] ?? '-',
            'risk_score_a' => $comp->risk_score_a ?? '-',
            'risk_score_b' => $comp->risk_score_b ?? '-',
            'recommendation' => $comp->recommended_country,
            'summary' => $comp->recommendation,
            'comparison_date' => $comp->created_at->format('Y-m-d H:i'),
            'created_by' => $comp->user->name ?? 'User'
        ]];
EOD;

// I need to make sure user relationship is eager loaded
$oldQuery = <<<'EOD'
        $comp = \App\Models\CountryComparison::with(['countryA', 'countryB'])
EOD;
$newQuery = <<<'EOD'
        $comp = \App\Models\CountryComparison::with(['countryA', 'countryB', 'user'])
EOD;

$content = str_replace($oldReturn, $newReturn, $content);
$content = str_replace($oldQuery, $newQuery, $content);
file_put_contents($file, $content);
echo "ReportExportController updated with news sentiment and created by.\n";
