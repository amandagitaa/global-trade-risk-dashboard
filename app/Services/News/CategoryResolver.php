<?php

namespace App\Services\News;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoryResolver
{
    /**
     * Standard allowed categories
     */
    protected const ALLOWED_CATEGORIES = [
        'Business', 'Economy', 'Trade', 'Politics', 'Finance', 
        'Technology', 'Energy', 'Transportation', 'Agriculture', 
        'Manufacturing', 'Supply Chain', 'Logistics', 'Natural Disaster', 
        'Weather', 'Healthcare', 'Environment', 'General'
    ];

    /**
     * Resolves the category based on the article data.
     * Returns an array containing resolution details.
     *
     * @param array $article
     * @return array
     */
    public function resolve(array $article): array
    {
        $providerCategory = $article['category'] ?? null;
        $title = $article['title'] ?? '';
        $description = $article['description'] ?? '';
        $content = $article['content'] ?? '';

        $keywords = $this->getKeywordsMap();

        // 1. Priority 1: Provider Category
        if (!empty($providerCategory)) {
            $mappedCategory = $this->mapProviderCategory($providerCategory);
            if ($mappedCategory) {
                $this->logResolution($mappedCategory, 'Provider Category', 'HIGH', $providerCategory);
                return $this->formatResult($mappedCategory, 'HIGH', 'Provider Category');
            }
        }

        // 2. Priority 2: Keyword Title
        if (!empty($title)) {
            $match = $this->searchKeywords($title, $keywords);
            if ($match) {
                $this->logResolution($match['category'], 'Title Match', 'HIGH', $match['keyword']);
                return $this->formatResult($match['category'], 'HIGH', 'Title Match');
            }
        }

        // 3. Priority 3: Keyword Description
        if (!empty($description)) {
            $match = $this->searchKeywords($description, $keywords);
            if ($match) {
                $this->logResolution($match['category'], 'Description Match', 'MEDIUM', $match['keyword']);
                return $this->formatResult($match['category'], 'MEDIUM', 'Description Match');
            }
        }

        // 4. Priority 4: Keyword Content
        if (!empty($content)) {
            $match = $this->searchKeywords($content, $keywords);
            if ($match) {
                $this->logResolution($match['category'], 'Content Match', 'LOW', $match['keyword']);
                return $this->formatResult($match['category'], 'LOW', 'Content Match');
            }
        }

        // 5. Priority 5: Fallback General
        $this->logResolution('General', 'Fallback', 'LOW', 'None');
        return $this->formatResult('General', 'LOW', 'Fallback');
    }

    /**
     * Searches for keywords in a given text.
     */
    protected function searchKeywords(string $text, array $keywordsMap): ?array
    {
        foreach ($keywordsMap as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/i', $text)) {
                    return [
                        'category' => $category,
                        'keyword' => $keyword
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Maps a raw provider category string to one of the standard categories.
     */
    protected function mapProviderCategory(string $rawCategory): ?string
    {
        $raw = strtolower(trim($rawCategory));
        
        // Direct match case-insensitive
        foreach (self::ALLOWED_CATEGORIES as $allowed) {
            if (strtolower($allowed) === $raw) {
                return $allowed;
            }
        }

        // Partial match mappings
        $mapping = [
            'tech' => 'Technology',
            'health' => 'Healthcare',
            'science' => 'Technology',
            'sports' => 'General', // Not relevant, fallback to general
            'entertainment' => 'General',
            'world' => 'Politics', // Often world news maps to politics/geopolitics
            'nation' => 'Politics',
        ];

        return $mapping[$raw] ?? null;
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
     * Logs the resolution outcome.
     */
    protected function logResolution(string $category, string $method, string $confidence, string $keyword): void
    {
        Log::info("CategoryResolver: Matched [{$category}] via [{$method}] (Keyword: {$keyword}) with [{$confidence}] confidence.");
    }

    /**
     * Dictionary of categories and their associated keywords.
     */
    protected function getKeywordsMap(): array
    {
        return [
            'Business' => [
                'company', 'corporation', 'enterprise', 'startup', 'investment', 'merger', 'acquisition'
            ],
            'Economy' => [
                'gdp', 'inflation', 'economic growth', 'economy', 'recession', 'monetary', 'fiscal'
            ],
            'Trade' => [
                'export', 'import', 'tariff', 'custom', 'trade agreement', 'supply', 'wto', 'freetrade'
            ],
            'Finance' => [
                'bank', 'interest rate', 'stock', 'bond', 'exchange rate', 'currency', 'forex'
            ],
            'Technology' => [
                'AI', 'robot', 'automation', 'software', 'semiconductor', 'chip', 'cybersecurity'
            ],
            'Energy' => [
                'oil', 'gas', 'coal', 'renewable', 'electricity', 'solar', 'wind'
            ],
            'Transportation' => [
                'shipping', 'port', 'cargo', 'airline', 'container', 'vessel', 'freight'
            ],
            'Supply Chain' => [
                'supplier', 'distribution', 'warehouse', 'inventory', 'lead time', 'procurement'
            ],
            'Logistics' => [
                'delivery', 'fleet', 'shipping route', 'freight', 'trucking', 'last mile'
            ],
            'Natural Disaster' => [
                'earthquake', 'flood', 'eruption', 'tsunami', 'storm', 'hurricane', 'typhoon'
            ],
            'Weather' => [
                'rainfall', 'temperature', 'climate', 'cyclone', 'drought'
            ],
            'Healthcare' => [
                'virus', 'pandemic', 'hospital', 'medical', 'vaccine', 'disease'
            ],
            'Environment' => [
                'pollution', 'forest', 'emission', 'carbon', 'sustainability', 'green'
            ],
            'Politics' => [
                'election', 'government', 'parliament', 'senate', 'legislation', 'diplomacy'
            ],
            'Agriculture' => [
                'farming', 'crop', 'harvest', 'wheat', 'corn', 'soybean', 'fertilizer'
            ],
            'Manufacturing' => [
                'factory', 'production line', 'assembly', 'industrial', 'plant'
            ]
        ];
    }
}
