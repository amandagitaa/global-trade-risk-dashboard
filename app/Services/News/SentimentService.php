<?php

namespace App\Services\News;

use Illuminate\Support\Facades\Log;

class SentimentService
{
    /**
     * Positive and Negative keywords dictionary.
     */
    protected array $positiveWords = [
        'growth', 'grow', 'profit', 'surge', 'boom', 'boost', 'expand', 'expansion',
        'gain', 'gains', 'recovery', 'recover', 'jump', 'rise', 'soar', 'strong',
        'success', 'successful', 'agreement', 'deal', 'pact', 'alliance', 'partnership',
        'record', 'up', 'upgrade', 'optimistic', 'optimism', 'stable', 'stabilize',
        'outperform', 'outperforms', 'surpass', 'exceed', 'thrive', 'prosper', 'surge',
        'benefit', 'benefits', 'breakthrough', 'innovate', 'innovation'
    ];

    protected array $negativeWords = [
        'loss', 'lose', 'drop', 'fall', 'slump', 'crash', 'crisis', 'decline',
        'plunge', 'shrink', 'slowdown', 'slow', 'weaken', 'weak', 'down', 'downgrade',
        'pessimistic', 'pessimism', 'risk', 'threat', 'warning', 'warn', 'concern',
        'ban', 'restrict', 'restriction', 'tariff', 'boycott', 'sanction', 'tension',
        'conflict', 'war', 'strike', 'delay', 'disrupt', 'disruption', 'shortage',
        'collapse', 'debt', 'bankrupt', 'bankruptcy', 'deficit', 'inflation', 'breach'
    ];

    /**
     * Analyzes the sentiment of the article using a rule-based approach.
     * Returns an array with sentiment details.
     *
     * @param array $article
     * @return array
     */
    public function analyze(array $article): array
    {
        $title = $article['title'] ?? '';
        $description = $article['description'] ?? '';
        $content = $article['content'] ?? '';
        
        // Combine text, giving more weight to the title
        $fullText = strtolower($title . ' ' . $title . ' ' . $description . ' ' . $content);
        
        $positiveMatches = $this->countMatches($fullText, $this->positiveWords);
        $negativeMatches = $this->countMatches($fullText, $this->negativeWords);
        
        $totalMatches = $positiveMatches + $negativeMatches;
        
        if ($totalMatches === 0) {
            $result = [
                'sentiment' => 'Neutral',
                'sentiment_score' => 50, // 0-100 scale (50 is neutral)
                'confidence_score' => 'LOW',
                'analysis_method' => 'Rule-based Lexicon (No matches)'
            ];
            
            $this->logSentiment($result['sentiment'], $result['confidence_score'], $result['analysis_method']);
            return $result;
        }

        $sentimentScore = (int) round(($positiveMatches / $totalMatches) * 100);
        
        if ($sentimentScore > 55) {
            $sentiment = 'Positive';
        } elseif ($sentimentScore < 45) {
            $sentiment = 'Negative';
        } else {
            $sentiment = 'Neutral';
        }

        // Determine confidence based on the number of matches found
        if ($totalMatches > 5) {
            $confidence = 'HIGH';
        } elseif ($totalMatches > 2) {
            $confidence = 'MEDIUM';
        } else {
            $confidence = 'LOW';
        }

        $result = [
            'sentiment' => $sentiment,
            'sentiment_score' => $sentimentScore,
            'confidence_score' => $confidence,
            'analysis_method' => 'Rule-based Lexicon'
        ];

        $this->logSentiment($result['sentiment'], $result['confidence_score'], $result['analysis_method']);
        return $result;
    }

    /**
     * Counts occurrences of word dictionary in the text.
     */
    protected function countMatches(string $text, array $dictionary): int
    {
        $count = 0;
        foreach ($dictionary as $word) {
            // Match word boundaries
            if (preg_match_all('/\b' . preg_quote($word, '/') . '\b/i', $text, $matches)) {
                $count += count($matches[0]);
            }
        }
        return $count;
    }

    /**
     * Logs the sentiment resolution.
     */
    protected function logSentiment(string $sentiment, string $confidence, string $method): void
    {
        Log::info("SentimentService: Evaluated as [{$sentiment}] with [{$confidence}] confidence via [{$method}].");
    }
}
