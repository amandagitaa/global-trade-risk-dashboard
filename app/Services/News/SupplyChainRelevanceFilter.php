<?php

namespace App\Services\News;

class SupplyChainRelevanceFilter
{
    /**
     * Threshold required for the final score.
     */
    protected const THRESHOLD = 2;

    /**
     * Supply Chain domains and their contextual keywords.
     * We group them to ensure the article has a broad context or a very strong specific context.
     */
    protected array $contextGroups = [
        'Trade' => [
            'import', 'export', 'tariff', 'customs', 'wto', 'fta', 'sanctions', 'embargo', 
            'trade agreement', 'trade war', 'free trade', 'export restriction', 'import regulation'
        ],
        'Shipping' => [
            'port ', 'vessel', 'freight', 'cargo', 'container', 'maersk', 'msc ', 'cosco', 
            'suez canal', 'panama canal', 'shipping line', 'harbor'
        ],
        'Logistics' => [
            'warehouse', 'inventory', 'distribution', 'dhl', 'fedex', 'ups ', 'cold chain', 
            'last mile', 'supply shortage'
        ],
        'Manufacturing' => [
            'factory', 'production', 'semiconductor', 'chip shortage', 'electronics manufacturing', 
            'industrial output', 'manufacturing capacity', 'industrial production'
        ],
        'Energy' => [
            'oil price', 'lng', 'gas supply', 'electricity grid', 'renewable energy', 'coal', 
            'shipping cost', 'energy crisis'
        ],
        'Technology' => [
            'supply chain ai', 'erp', 'sap ', 'oracle scm', 'rfid', 'iot logistics', 
            'digital twin', 'predictive logistics', 'fleet management', 'warehouse automation'
        ],
        'Geopolitics' => [
            'china-us trade', 'taiwan semiconductor', 'red sea', 'south china sea', 
            'russia sanctions', 'export ban', 'rare earth', 'trade embargo'
        ],
    ];

    /**
     * Negative keywords to penalize financial, entertainment, and general news.
     */
    protected array $negativeKeywords = [
        // Finance & Corporate
        'earnings', 'quarterly', 'profit', 'net income', 'eps', 'dividend', 'shareholders', 
        'stock market', 'nasdaq', 'nyse', 'analyst rating', 'price target', 'buy rating', 
        'sell rating', 'ipo', 'wall street',
        
        // Entertainment & General
        'celebrity', 'movie', 'football', 'entertainment', 'music', 'actor', 'actress', 
        'hollywood', 'netflix subscribers', 'gossip'
    ];

    /**
     * Determine if an article is relevant to the Global Supply Chain context.
     */
    public function isRelevant(array $article): bool
    {
        $title = strtolower($article['title'] ?? '');
        $description = strtolower($article['description'] ?? '');
        $content = strtolower($article['content'] ?? '');

        // Title has strong context, we duplicate it to give it more weight in scoring
        $textToAnalyze = $title . ' ' . $title . ' ' . $description . ' ' . $content;
        
        $activeGroups = [];
        $positiveScore = 0;

        // TAHAP 1 & 2: Deteksi kelompok konteks dan hitung skor positif
        foreach ($this->contextGroups as $groupName => $keywords) {
            $groupScore = 0;
            foreach ($keywords as $keyword) {
                // Count how many times the keyword appears
                $occurrences = substr_count($textToAnalyze, strtolower($keyword));
                if ($occurrences > 0) {
                    $groupScore += 1;
                }
            }

            if ($groupScore > 0) {
                $activeGroups[] = $groupName;
                $positiveScore += $groupScore;
            }
        }

        // TAHAP 3: Hitung skor negatif (Negative Context)
        $negativeScore = 0;
        foreach ($this->negativeKeywords as $keyword) {
            $occurrences = substr_count($textToAnalyze, strtolower($keyword));
            if ($occurrences > 0) {
                $negativeScore += 1;
            }
        }

        // TAHAP 4: Evaluasi Akhir
        
        // Artikel terlalu didominasi konteks negatif (misal murni berita saham/hiburan)
        // Kita gunakan penalti besar, jika negatif menutupi positif, langsung tolak.
        if ($negativeScore > 0 && ($positiveScore - ($negativeScore * 2) < 0)) {
            return false;
        }

        // Memenuhi syarat minimal 2 kelompok konteks, ATAU 1 kelompok tapi memiliki >= 2 keyword berbeda 
        // (Contoh: "Port" + "Container" = 1 grup Shipping tapi 2 keyword berbeda -> LOLOS)
        $hasMultiContext = count($activeGroups) >= 2;
        $hasStrongSingleContext = (count($activeGroups) === 1 && $positiveScore >= 2);

        if (!($hasMultiContext || $hasStrongSingleContext)) {
            return false;
        }

        // Memastikan total skor positif minimal mencapai threshold
        return $positiveScore >= self::THRESHOLD;
    }
}
