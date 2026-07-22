<?php

namespace App\Services\News;

use Illuminate\Support\Facades\Log;

class TradeRiskService
{
    /**
     * Dictionary of risk factors and their base weights (1-10)
     */
    protected array $riskFactors = [
        'war' => 10,
        'sanction' => 9,
        'embargo' => 9,
        'cyber attack' => 8,
        'political conflict' => 8,
        'port closure' => 8,
        'natural disaster' => 7,
        'earthquake' => 7,
        'tsunami' => 7,
        'hurricane' => 7,
        'supply chain disruption' => 7,
        'currency crisis' => 6,
        'strike' => 6,
        'protest' => 6,
        'shipping delay' => 5,
        'container shortage' => 5,
        'inflation' => 5,
        'tariff' => 5,
        'export ban' => 5,
        'import restriction' => 5,
        'fuel crisis' => 4,
        'boycott' => 4
    ];

    /**
     * Category risk multipliers
     */
    protected array $categoryMultipliers = [
        'logistics' => 1.2,
        'supply chain' => 1.2,
        'trade' => 1.1,
        'natural disaster' => 1.1,
        'economy' => 1.0,
        'business' => 1.0,
        'politics' => 1.0,
        'general' => 0.8
    ];

    /**
     * Analyzes the trade risk based on article content, category, and sentiment.
     * Returns an array with risk details.
     *
     * @param array $article
     * @return array
     */
    public function analyze(array $article): array
    {
        $category = strtolower($article['category'] ?? 'general');
        
        // Sentiment data might be nested inside 'sentiment' key if passed directly from SentimentService,
        // or we expect the orchestrator to pass the raw array and we use the 'sentiment' string.
        // Assuming the orchestrator passes the article array and we expect 'sentiment' to be a string or array.
        $sentimentVal = $article['sentiment'] ?? 'Neutral';
        if (is_array($sentimentVal)) {
            $sentiment = strtolower($sentimentVal['sentiment'] ?? 'neutral');
        } else {
            $sentiment = strtolower($sentimentVal);
        }

        $title = $article['title'] ?? '';
        $description = $article['description'] ?? '';
        $content = $article['content'] ?? '';
        
        $fullText = strtolower($title . ' ' . $description . ' ' . $content);

        // 1. Calculate Base Risk from Keywords
        $matchedRisks = [];
        $baseRiskScore = 0;
        
        foreach ($this->riskFactors as $keyword => $weight) {
            if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/i', $fullText)) {
                $matchedRisks[] = ucwords($keyword);
                $baseRiskScore += $weight;
            }
        }

        // 2. Adjust Risk via Sentiment Matrix
        $sentimentMultiplier = 1.0;
        if ($sentiment === 'negative') {
            $sentimentMultiplier = 1.5; // Negative news amplifies risk
            $baseRiskScore += 10; // Baseline bump for negative news
        } elseif ($sentiment === 'positive') {
            $sentimentMultiplier = 0.5; // Positive news mitigates risk
        }

        // 3. Adjust Risk via Category Matrix
        $categoryMultiplier = $this->categoryMultipliers[$category] ?? 1.0;

        // 4. Calculate Final Risk Score (0-100)
        $finalScore = (int) round(($baseRiskScore * $sentimentMultiplier * $categoryMultiplier) * 2); // Scale up for 0-100 range
        
        // Cap the score at 100
        $finalScore = min(100, max(0, $finalScore));

        // 5. Determine Risk Level
        if ($finalScore >= 80) {
            $riskLevel = 'CRITICAL';
        } elseif ($finalScore >= 50) {
            $riskLevel = 'HIGH';
        } elseif ($finalScore >= 20) {
            $riskLevel = 'MEDIUM';
        } else {
            $riskLevel = 'LOW';
        }

        // 6. Format Reason
        $reason = empty($matchedRisks) 
            ? "No specific risk factors detected in text."
            : "Detected risk factors: " . implode(', ', $matchedRisks) . ".";

        if ($sentiment === 'negative') {
            $reason .= " Amplified by negative sentiment.";
        }

        $result = [
            'risk_level' => $riskLevel,
            'risk_score' => $finalScore,
            'risk_reason' => $reason
        ];

        $this->logRisk($result['risk_level'], $result['risk_score'], $result['risk_reason']);
        return $result;
    }

    /**
     * Logs the trade risk resolution.
     */
    protected function logRisk(string $level, int $score, string $reason): void
    {
        Log::info("TradeRiskService: Evaluated as [{$level}] (Score: {$score}) - Reason: {$reason}");
    }
}
