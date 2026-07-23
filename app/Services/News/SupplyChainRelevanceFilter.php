<?php

namespace App\Services\News;

class SupplyChainRelevanceFilter
{
    /**
     * Minimum score required for an article to be considered relevant to Supply Chain.
     */
    protected const THRESHOLD = 2;

    /**
     * Positive keywords that strongly indicate relevance to global trade, 
     * shipping, logistics, manufacturing, and supply chain.
     */
    protected array $keywords = [
        // Supply Chain & Trade (High Relevance)
        'supply chain' => 2,
        'logistics' => 2,
        'freight' => 2,
        'cargo' => 2,
        'customs' => 2,
        'tariff' => 2,
        'trade war' => 2,
        'trade agreement' => 2,
        'free trade' => 2,
        'fta ' => 2,
        'export restriction' => 2,
        'import regulation' => 2,
        'export ban' => 2,
        'trade embargo' => 2,
        'trade sanction' => 2,
        'unctad' => 2,
        'wto ' => 2,

        // Shipping & Maritime
        'shipping line' => 2,
        'port congestion' => 2,
        'suez canal' => 2,
        'panama canal' => 2,
        'red sea crisis' => 2,
        'red sea shipping' => 2,
        'maersk' => 2,
        'msc ' => 2,
        'cma cgm' => 2,
        'hapag-lloyd' => 2,
        'cosco' => 2,
        'evergreen' => 2,
        'vessel' => 1,
        'container' => 1,
        'port of ' => 2,
        'harbor' => 1,
        
        // Logistics & Operations
        'warehouse automation' => 2,
        'distribution center' => 2,
        'cold chain' => 2,
        'last mile' => 2,
        'freight forwarding' => 2,
        'supply shortage' => 2,
        'inventory' => 1,
        'dhl ' => 2,
        'fedex' => 2,
        'ups ' => 1,

        // Manufacturing & Industry
        'semiconductor manufacturing' => 2,
        'chip shortage' => 2,
        'semiconductor restriction' => 2,
        'automotive production' => 2,
        'electronics manufacturing' => 2,
        'industrial production' => 1,
        'industrial output' => 1,
        'production capacity' => 1,
        'moves production' => 2,
        'factory halted' => 2,
        'factory' => 1,
        'manufacturing' => 1,

        // Technology in Supply Chain
        'supply chain ai' => 2,
        'erp ' => 2,
        'sap ' => 2,
        'oracle scm' => 2,
        'rfid' => 2,
        'iot logistics' => 2,
        'digital twin' => 2,
        'predictive logistics' => 2,
        'fleet management' => 2,

        // Geopolitics & Energy (Contextual to Trade)
        'china-us trade' => 2,
        'russia sanctions' => 2,
        'taiwan semiconductor' => 2,
        'south china sea' => 2,
        'rare earth' => 2,
        'energy crisis' => 1,
        'lng' => 1,
        'oil price' => 1,
        'shipping cost' => 2,
        'power grid' => 1,
        
        // General Trade basics
        'import' => 1,
        'export' => 1,
    ];

    /**
     * Negative keywords to penalize articles that are likely out of scope 
     * (e.g., entertainment, pure finance without supply chain context).
     */
    protected array $negativeKeywords = [
        'quarterly earnings' => -2,
        'dividend' => -2,
        'subscribers' => -2,
        'celebrity' => -3,
        'football' => -3,
        'entertainment' => -3,
        'movie' => -3,
        'actor ' => -3,
        'hollywood' => -3,
    ];

    public function isRelevant(array $article): bool
    {
        $title = strtolower($article['title'] ?? '');
        $description = strtolower($article['description'] ?? '');
        $content = strtolower($article['content'] ?? '');

        // Title gets double weight in frequency implicitly by concatenating twice,
        // because a keyword in the title is a very strong signal.
        $textToAnalyze = $title . ' ' . $title . ' ' . $description . ' ' . $content;
        
        $score = 0;

        foreach ($this->keywords as $keyword => $points) {
            if (str_contains($textToAnalyze, $keyword)) {
                $score += $points;
            }
        }

        foreach ($this->negativeKeywords as $keyword => $points) {
            if (str_contains($textToAnalyze, $keyword)) {
                $score += $points;
            }
        }

        return $score >= self::THRESHOLD;
    }
}
