<?php

namespace App\Services\News;

class TradeRelevanceFilter
{
    /**
     * Minimum score required for an article to be accepted.
     */
    protected int $acceptanceThreshold = 15;

    /**
     * Positive keywords and their respective score weights.
     */
    protected array $positiveKeywords = [
        'tariff' => 20,
        'tariffs' => 20,
        'import tariff' => 20,
        'export tariff' => 20,
        'trade restriction' => 20,
        'export restriction' => 20,
        'import restriction' => 20,
        'export control' => 20,
        'export ban' => 20,
        'import ban' => 20,
        'trade ban' => 20,
        'customs restriction' => 20,
        'trade agreement' => 20,
        'trade sanctions' => 20,
        'port congestion' => 20,
        'port closure' => 20,
        'shipping disruption' => 20,
        'shipping delay' => 20,
        'container shortage' => 20,
        'freight disruption' => 20,
        'freight rates' => 20,
        'cargo disruption' => 20,
        'maritime disruption' => 20,
        'vessel disruption' => 20,
        'supply chain disruption' => 20,
        'logistics disruption' => 20,
        'trucking disruption' => 20,
        'freight capacity' => 20,
        'warehouse disruption' => 20,
        'rail freight disruption' => 20,
        'procurement disruption' => 20,
        'supplier disruption' => 20,
        'semiconductor supply' => 20,
        'chip supply' => 20,
        'semiconductor shortage' => 20,
        'chip shortage' => 20,
        'manufacturing disruption' => 20,
        'production disruption' => 20,
        'factory shutdown' => 20,
        'production halt' => 20,
        'supply agreement' => 20,
        'oil export ban' => 20,
        'gasoline export ban' => 20,
        'gas export ban' => 20,
        'LNG supply' => 20,
        'energy supply disruption' => 20,
        'oil supply disruption' => 20,
        'refinery disruption' => 20,

        // Existing keywords that are not overridden by the above
        'customs' => 10,
        'export' => 10,
        'import' => 10,
        'supply chain' => 20,
        'logistics' => 15,
        'freight' => 15,
        'shipping' => 10,
        'vessel' => 10,
        'cargo' => 10,
        'strike' => 15,
        'trade war' => 25,
        'sanction' => 15,
        'embargo' => 20,
        'maritime' => 10,
        'wto' => 10,
    ];

    /**
     * Negative keywords indicating finance, stock, or entertainment noise.
     */
    protected array $negativeKeywords = [
        'stock market' => -20,
        'shares plunge' => -20,
        'dividend' => -15,
        'wall street' => -20,
        'hollywood' => -30,
        'celebrity' => -30,
        'movie' => -20,
        'gossip' => -30,
        'sports' => -20,
        'football' => -20,
        'basketball' => -20,
        'nasdaq' => -20,
        'crypto' => -15,
        'bitcoin' => -15,
    ];

    /**
     * Countries to check for context bonus.
     */
    protected array $countries = [
        'us', 'usa', 'united states', 'uk', 'united kingdom', 'china', 'eu', 'european union',
        'singapore', 'india', 'japan', 'germany', 'france', 'brazil', 'mexico', 'canada', 'taiwan'
    ];

    /**
     * Modifiers applied based on the API origin category.
     */
    protected array $apiCategoryModifiers = [
        'Logistics' => 10,
        'Shipping' => 10,
        'Trade' => 10,
        'Manufacturing' => 5,
        'General' => -5,
        'Business' => 0,
        'Economy' => 0,
        'Geopolitics' => 5,
        'Energy' => 5,
        'Technology' => 0,
    ];

    /**
     * Evaluates an article and returns scoring details.
     */
    public function evaluate(array $article): array
    {
        $title = $article['title'] ?? '';
        $description = $article['description'] ?? '';
        $content = $article['content'] ?? '';
        $apiCategory = $article['api_category'] ?? 'unknown';

        $fullText = strtolower($title . ' ' . $description . ' ' . $content);

        $score = 0;
        $matchedPositive = [];
        $matchedNegative = [];
        $reasons = [];

        // 1. Evaluate Positive Keywords
        foreach ($this->positiveKeywords as $keyword => $weight) {
            if (str_contains($fullText, $keyword)) {
                $score += $weight;
                $matchedPositive[] = $keyword;
                $reasons[] = "Matched positive keyword: '{$keyword}' (+{$weight})";
            }
        }

        // 2. Evaluate Negative Keywords
        foreach ($this->negativeKeywords as $keyword => $penalty) {
            if (str_contains($fullText, $keyword)) {
                $score += $penalty;
                $matchedNegative[] = $keyword;
                $reasons[] = "Matched negative keyword: '{$keyword}' ({$penalty})";
            }
        }

        // 3. Country + Trade Keyword Bonus
        if (!empty($matchedPositive)) {
            $hasCountry = false;
            foreach ($this->countries as $country) {
                if (preg_match('/\b' . preg_quote($country, '/') . '\b/', $fullText)) {
                    $hasCountry = true;
                    break;
                }
            }
            
            if ($hasCountry) {
                $bonus = 10;
                $score += $bonus;
                $reasons[] = "Country + Trade keyword contextual bonus (+{$bonus})";
            }
        }

        // 4. API Category Bonus/Penalty
        if (isset($this->apiCategoryModifiers[$apiCategory])) {
            $modifier = $this->apiCategoryModifiers[$apiCategory];
            $score += $modifier;
            $sign = $modifier > 0 ? '+' : '';
            $reasons[] = "API Category Modifier for '{$apiCategory}' ({$sign}{$modifier})";
        }

        $accepted = $score >= $this->acceptanceThreshold;

        if ($accepted) {
            $reasons[] = "Final score {$score} >= threshold {$this->acceptanceThreshold}. ACCEPTED.";
        } else {
            $reasons[] = "Final score {$score} < threshold {$this->acceptanceThreshold}. REJECTED.";
        }

        return [
            'score' => $score,
            'accepted' => $accepted,
            'matched_positive_keywords' => $matchedPositive,
            'matched_negative_keywords' => $matchedNegative,
            'reasons' => $reasons,
        ];
    }

    /**
     * Checks if the evaluation result is accepted.
     */
    public function shouldAccept(array $evaluation): bool
    {
        return $evaluation['accepted'] ?? false;
    }

    /**
     * Retrieves the list of reasons from the evaluation result.
     */
    public function getReasons(array $evaluation): array
    {
        return $evaluation['reasons'] ?? [];
    }
}
