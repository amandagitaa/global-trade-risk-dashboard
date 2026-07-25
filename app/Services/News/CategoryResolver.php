<?php

namespace App\Services\News;

use Illuminate\Support\Facades\Log;

class CategoryResolver
{
    /**
     * Standard allowed categories exactly as requested.
     */
    protected const ALLOWED_CATEGORIES = [
        'Business', 'Energy', 'General', 'Geopolitics', 'Logistics', 
        'Manufacturing', 'Shipping', 'Technology', 'Trade'
    ];

    /**
     * Resolves the category based on the article data using dominant supply-chain context.
     */
    public function resolve(array $article): array
    {
        $title = strtolower($article['title'] ?? '');
        $description = strtolower($article['description'] ?? '');

        $keywords = $this->getKeywordsMap();
        $scores = [];

        // Initialize scores
        foreach (self::ALLOWED_CATEGORIES as $cat) {
            $scores[$cat] = 0;
        }

        // Score based on title (x2) and description (x1)
        foreach ($keywords as $category => $categoryKeywords) {
            foreach ($categoryKeywords as $keyword) {
                // Title matches
                $titleHits = preg_match_all('/\b' . preg_quote($keyword, '/') . '(s|es|d|ed|ing)?\b/i', $title);
                $scores[$category] += ($titleHits * 2);

                // Description matches
                $descHits = preg_match_all('/\b' . preg_quote($keyword, '/') . '(s|es|d|ed|ing)?\b/i', $description);
                $scores[$category] += ($descHits * 1);
            }
        }

        // Find the maximum score
        $maxScore = 0;
        $dominantCategory = null;

        foreach ($scores as $category => $score) {
            if ($score > $maxScore) {
                $maxScore = $score;
                $dominantCategory = $category;
            }
        }

        if ($dominantCategory && $maxScore > 0) {
            return $this->formatResult($dominantCategory, 'HIGH', 'Dominant Context Match');
        }

        // Fallback Category
        return $this->formatResult('General', 'LOW', 'Fallback');
    }

    /**
     * Formats the standardized resolution result.
     */
    protected function formatResult(string $category, string $confidence, string $method): array
    {
        return [
            'category' => $category,
            'confidence_score' => $confidence,
            'resolution_method' => $method
        ];
    }

    /**
     * Dictionary of categories and their associated keywords based on supply chain context.
     */
    protected function getKeywordsMap(): array
    {
        return [
            'Shipping' => [
                'shipping', 'maritime', 'vessel', 'container ship', 'cargo ship', 
                'tanker', 'port', 'seaport', 'ocean freight', 'shipping route', 'shipping disruption'
            ],
            'Logistics' => [
                'logistics', 'freight', 'warehouse', 'warehousing', 'distribution', 
                'delivery network', 'transport network', 'supply chain operations'
            ],
            'Trade' => [
                'export', 'import', 'tariff', 'customs', 'trade agreement', 
                'trade turnover', 'trade flow', 'international trade', 'global trade', 'trade restriction',
                'trade practice', 'trade investigation', 'trade dispute', 'trade policy'
            ],
            'Manufacturing' => [
                'factory', 'production', 'manufacturer', 'industrial production', 
                'manufacturing supply chain', 'supplier production', 'manufacturing plant'
            ],
            'Energy' => [
                'oil', 'gas', 'lng', 'energy supply', 'oil tanker', 
                'energy shipment', 'fuel supply', 'energy trade'
            ],
            'Technology' => [
                'ai', 'automation', 'iot', 'semiconductor', 'chip', 
                'digital supply chain', 'supply-chain technology'
            ],
            'Geopolitics' => [
                'sanctions', 'trade war', 'blockade', 'conflict', 
                'geopolitical trade risk', 'strait of hormuz', 'red sea', 'government trade restrictions'
            ],
            'Business' => [
                'company supply-chain strategy', 'supplier agreement', 'procurement', 
                'corporate logistics', 'supply-chain investment', 'company trade expansion'
            ]
        ];
    }
}
