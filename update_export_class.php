<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\app\Exports\CountryComparisonExport.php';
$content = file_get_contents($file);

$oldHeadings = <<<'EOD'
            'Risk Score A',
            'Risk Score B',
            'Recommended Country',
            'Recommendation Summary',
            'Comparison Date',
        ];
    }
EOD;

$newHeadings = <<<'EOD'
            'News Sentiment A',
            'News Sentiment B',
            'Risk Score A',
            'Risk Score B',
            'Recommended Country',
            'Recommendation Summary',
            'Comparison Date',
            'Created By',
        ];
    }
EOD;

$content = str_replace($oldHeadings, $newHeadings, $content);
file_put_contents($file, $content);
echo "CountryComparisonExport updated.\n";
