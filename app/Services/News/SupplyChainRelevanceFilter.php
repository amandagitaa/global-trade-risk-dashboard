<?php

namespace App\Services\News;

class SupplyChainRelevanceFilter
{
    /**
     * Required minimum score for an article to be saved.
     */
    protected const THRESHOLD = 5;

    /**
     * STRONG PHRASES (Unambiguous Core Signals)
     * These phrases provide strong evidence of supply-chain relevance.
     */
    protected array $strongPhrases = [
        'supply chain' => 5,
        'supply-chain' => 5,
        'container shipping' => 5,
        'shipping industry' => 5,
        'shipping route' => 5,
        'trade route' => 5,
        'global trade' => 5,
        'international trade' => 5,
        'port congestion' => 5,
        'port operations' => 5,
        'freight rates' => 5,
        'freight forwarding' => 5,
        'air freight' => 5,
        'ocean freight' => 5,
        'maritime trade' => 5,
        'maritime shipping' => 5,
        'container freight' => 5,
        'container vessel' => 5,
        'container ship' => 5,
        'cargo vessel' => 5,
        'logistics network' => 5,
        'logistics operations' => 5,
        'warehouse operations' => 5,
        'manufacturing supply chain' => 5,
        'supplier disruption' => 5,
        'procurement disruption' => 5,
        'export restrictions' => 5,
        'import restrictions' => 5,
        'trade sanctions' => 5,
        'trade tariffs' => 5,
        'shipping disruption' => 5,
        'supply disruption' => 5,
    ];

    /**
     * AMBIGUOUS CORE SIGNALS
     * These words require supporting context and MUST NOT be near false-positive context.
     */
    protected array $ambiguousCoreSignals = [
        'shipping' => 3,
        'container' => 3,
        'cargo' => 3,
        'port' => 3,
        'terminal' => 2,
        'supplier' => 2,
        'trade' => 3,
        'warehouse' => 3,
        'freight' => 3,
        'manufacturing' => 2,
        'export' => 2,
        'import' => 2,
        'maritime' => 3,
        'logistics' => 3,
        'vessel' => 3,
        'tanker' => 3,
        'tariff' => 3,
        'customs' => 2,
        'procurement' => 2,
    ];

    /**
     * CONTEXT SIGNALS
     * Words that add value ONLY IF a core signal is present.
     */
    protected array $contextSignals = [
        'ai' => 1,
        'artificial intelligence' => 1,
        'automation' => 1,
        'iot' => 1,
        'semiconductor' => 1,
        'chip' => 1,
        'factory' => 2,
        'energy' => 1,
        'oil' => 1,
        'gas' => 1,
        'lng' => 1,
        'geopolitical' => 1,
        'conflict' => 1,
        'war' => 1,
        'business' => 1,
        'investment' => 1,
        'company' => 1,
        'technology' => 1,
    ];

    /**
     * CONTEXT GROUPS FOR FALSE-POSITIVE DETECTION
     */
    protected array $negativeContexts = [
        'CRIME_CONTEXT' => [
            'migrant', 'smuggling', 'police', 'arrest', 'murder', 'dead', 'body', 'trafficking', 'criminal', 'court', 'indicted'
        ],
        'AUTOMOTIVE_CONSUMER_CONTEXT' => [
            'suv', 'sedan', 'car review', 'vehicle review', 'engine', 'horsepower', 'mileage', 'test drive', 'trunk', 'boot space', 'car buying'
        ],
        'COMPUTING_PORT_CONTEXT' => [
            'usb', 'tcp', 'udp', 'computer', 'server', 'network port', 'charging port', 'software port'
        ],
        'SPORTS_CONTEXT' => [
            'football', 'soccer', 'nba', 'nfl', 'cricket', 'tennis', 'match', 'tournament'
        ],
        'ENTERTAINMENT_CONTEXT' => [
            'celebrity', 'movie', 'film', 'music', 'actor', 'singer', 'tv show'
        ],
        'PROMOTIONAL_CONTEXT' => [
            'wholesale', 'oem service', 'odm service', 'product catalog', 
            'manufacturer advertisement', 'supplier advertisement', 'press release',
            'buy now', 'product launch', 'supplier offers', 'wholesale ready', 'oem odm',
            'manufacturer offers', 'distributor opportunity', 'contact us',
            'market report', 'industry report', 'forecast report', 'global market'
        ]
    ];

    /**
     * OPERATIONAL TRADE SIGNALS
     * Required for geopolitical/security articles to prove actual trade impact.
     */
    protected array $operationalTradeSignals = [
        'trade flows', 'shipping operations', 'shipping routes', 'freight', 'cargo',
        'port operations', 'exports', 'imports', 'tariffs', 'sanctions affecting trade',
        'supply disruption', 'logistics disruption', 'vessel routing', 'transport costs',
        'energy transportation', 'commercial shipping', 'supply chain', 'trade route',
        'export restrictions', 'import restrictions', 'trade sanctions', 'commercial vessels',
        'container shipping'
    ];

    /**
     * Match keywords and return score for a text section.
     * deduplicates keywords per section.
     */
    protected function scoreTextSection(string $text, array $keywords, float $multiplier = 1.0, array &$detected = []): float
    {
        $score = 0;
        foreach ($keywords as $keyword => $points) {
            if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/i', $text)) {
                $score += ($points * $multiplier);
                $detected[] = $keyword;
            }
        }
        return $score;
    }
    
    /**
     * Check if text contains any operational trade signal.
     */
    protected function hasOperationalTradeSignal(string $text, array &$detected = []): bool
    {
        $hasSignal = false;
        foreach ($this->operationalTradeSignals as $signal) {
            if (preg_match('/\b' . preg_quote($signal, '/') . '\b/i', $text)) {
                $detected[] = $signal;
                $hasSignal = true;
            }
        }
        return $hasSignal;
    }

    /**
     * Check for negative context groups in text.
     */
    protected function getNegativeContextScore(string $text, array &$detectedConflicts = []): float
    {
        $penalty = 0;
        foreach ($this->negativeContexts as $group => $words) {
            foreach ($words as $word) {
                if (preg_match('/\b' . preg_quote($word, '/') . '\b/i', $text)) {
                    // We apply a soft penalty to suppress ambiguous scores, not a hard block
                    $penalty -= 5;
                    $detectedConflicts[] = $group . ':' . $word;
                }
            }
        }
        return $penalty;
    }

    /**
     * Calculate score and return details.
     */
    public function calculateScore(array $article): array
    {
        $title = strtolower($article['title'] ?? '');
        $description = strtolower($article['description'] ?? '');
        $content = strtolower($article['content'] ?? '');
        
        $fullText = $title . ' ' . $description . ' ' . $content;

        $coreScore = 0;
        $contextScore = 0;
        $negativeScore = 0;

        $detectedStrongPhrases = [];
        $detectedAmbiguous = [];
        $detectedConflicts = [];
        $detectedOperationalSignals = [];

        // 1. Calculate Core Score (Strong Phrases)
        // Title gets 2.0x weight, Description/Content gets 1.0x weight
        $coreScore += $this->scoreTextSection($title, $this->strongPhrases, 2.0, $detectedStrongPhrases);
        $coreScore += $this->scoreTextSection($description . ' ' . $content, $this->strongPhrases, 1.0, $detectedStrongPhrases);

        // Calculate Ambiguous Core Signals
        $ambiguousScoreTitle = $this->scoreTextSection($title, $this->ambiguousCoreSignals, 2.0, $detectedAmbiguous);
        $ambiguousScoreBody = $this->scoreTextSection($description . ' ' . $content, $this->ambiguousCoreSignals, 1.0, $detectedAmbiguous);
        
        // 2. Negative/Conflict Check
        $negativeScore += $this->getNegativeContextScore($title, $detectedConflicts);
        $negativeScore += $this->getNegativeContextScore($description . ' ' . $content, $detectedConflicts);

        // Operational Trade Signal Validation (especially for Geopolitics/Crime)
        $hasOperationalSignal = $this->hasOperationalTradeSignal($fullText, $detectedOperationalSignals);

        // If there's a geopolitical/crime/security context, require operational trade context to pass it.
        $hasGeopoliticsOrCrime = false;
        foreach ($detectedConflicts as $conflict) {
            if (str_starts_with($conflict, 'CRIME_CONTEXT')) {
                $hasGeopoliticsOrCrime = true;
                break;
            }
        }
        // Also check if generic context signals like war, conflict, geopolitics are present
        $geopoliticsKeywords = ['war', 'conflict', 'geopolitical', 'sanctions', 'attack', 'missile', 'iran', 'russia', 'china'];
        foreach ($geopoliticsKeywords as $gk) {
            if (preg_match('/\b' . preg_quote($gk, '/') . '\b/i', $fullText)) {
                $hasGeopoliticsOrCrime = true;
                $detectedConflicts[] = 'GEOPOLITICAL_SECURITY:' . $gk;
            }
        }

        if ($hasGeopoliticsOrCrime && !$hasOperationalSignal) {
            return [
                'score' => 0,
                'core_score' => 0,
                'context_score' => 0,
                'reason' => 'Failed Operational Trade Validation for Security/Geopolitical Event',
                'is_relevant' => false,
                'detected_phrases' => [],
                'detected_conflicts' => array_unique($detectedConflicts),
                'operational_signals' => []
            ];
        }

        // Ambiguous Penalty Logic:
        // If there are negative conflicts, ambiguous keywords are suppressed (score ignored)
        if ($negativeScore < 0 && empty($detectedStrongPhrases) && !$hasOperationalSignal) {
            // Negative context present AND no strong phrases to override it => suppress ambiguous signals
            $coreScore += 0;
        } else {
            $coreScore += $ambiguousScoreTitle + $ambiguousScoreBody;
        }
        
        // TITLE QUALITY GATE
        // If title has ZERO core evidence (strong or ambiguous) AND there's no strong phrase anywhere, reject.
        $titleCoreScore = $this->scoreTextSection($title, $this->strongPhrases, 1.0) + $this->scoreTextSection($title, $this->ambiguousCoreSignals, 1.0);
        
        if ($titleCoreScore == 0 && empty($detectedStrongPhrases)) {
            return [
                'score' => 0,
                'core_score' => $coreScore,
                'context_score' => 0,
                'reason' => 'Failed Title Quality Gate (No supply-chain signal in title and no strong phrase in body)',
                'is_relevant' => false,
                'detected_phrases' => [],
                'detected_conflicts' => array_unique($detectedConflicts),
                'operational_signals' => array_unique($detectedOperationalSignals)
            ];
        }

        // Core Gate Check
        if ($coreScore <= 0) {
            return [
                'score' => 0,
                'core_score' => 0,
                'context_score' => 0,
                'reason' => 'No supply-chain/global-trade core signal',
                'is_relevant' => false,
                'detected_phrases' => [],
                'detected_conflicts' => array_unique($detectedConflicts),
                'operational_signals' => array_unique($detectedOperationalSignals)
            ];
        }

        // 3. Calculate Context Score (only if core passed)
        $contextScore += $this->scoreTextSection($title, $this->contextSignals, 2.0);
        $contextScore += $this->scoreTextSection($description . ' ' . $content, $this->contextSignals, 1.0);

        // If it passed operational signal override, cancel negative score penalty to let it pass
        if ($hasOperationalSignal) {
            $negativeScore = 0;
        }

        $finalScore = $coreScore + $contextScore + $negativeScore;
        $finalScore = max(0, $finalScore);

        $reason = $finalScore >= self::THRESHOLD ? 'Passes core gate and threshold' : 'Failed relevance threshold';

        if ($negativeScore < 0 && $finalScore < self::THRESHOLD) {
            $reason = 'Rejected due to strong context conflict (False Positive detection)';
        }

        return [
            'score' => $finalScore,
            'core_score' => $coreScore,
            'context_score' => $contextScore,
            'reason' => $reason,
            'is_relevant' => $finalScore >= self::THRESHOLD,
            'detected_phrases' => array_unique(array_merge($detectedStrongPhrases, $detectedAmbiguous)),
            'detected_conflicts' => array_unique($detectedConflicts),
            'operational_signals' => array_unique($detectedOperationalSignals)
        ];
    }

    /**
     * Determine if an article is relevant to the Global Supply Chain context.
     */
    public function isRelevant(array $article): bool
    {
        return $this->calculateScore($article)['is_relevant'];
    }
}
