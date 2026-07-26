<?php
namespace App\Services\News;

class TradeImpactAnalyzer
{
    public function analyze(array $article): array
    {
        $title = $article['title'] ?? '';
        $desc = $article['description'] ?? '';
        $content = $article['content'] ?? '';
        
        $fullText = strtolower($title . ' ' . $desc . ' ' . $content);

        // 1. Operational Event Gate
        $events = $this->detectOperationalEvents($fullText);
        if (empty($events)) {
            return $this->zeroImpact();
        }

        // 2. Adjust for speculative language
        $isSpeculative = $this->isSpeculative($fullText);

        // 3. Extract affected countries and sectors (Context aware)
        $affectedCountries = $this->extractCountries($title, $desc, $content);
        $affectedSectors = $this->extractSectors($fullText, $events);

        // 4. Calculate Base Score based on events and magnitude
        $impactScore = $this->calculateScore($fullText, $events, $affectedCountries, $isSpeculative);

        // 5. Promotional Content Penality
        $isPromotional = $this->isPromotional($fullText);
        if ($isPromotional) {
            $impactScore = min($impactScore, 14); // Clamp to Low if promotional
        }

        $impactLevel = $this->determineImpactLevel($impactScore);

        // 6. Determine risk direction contextually
        $riskDirection = $this->determineRiskDirection($fullText, $events);

        // 7. Calculate confidence
        $confidence = $this->calculateConfidence($title, $fullText, $events, $affectedCountries, $affectedSectors, $isSpeculative);

        // 8. Generate operational impact explanation
        $operationalImpact = $this->generateOperationalImpact($events, $affectedSectors, $affectedCountries, $riskDirection);

        return [
            'impact_score' => $impactScore,
            'impact_level' => $impactLevel,
            'risk_direction' => $riskDirection,
            'affected_countries' => $affectedCountries,
            'affected_sectors' => $affectedSectors,
            'impact_factors' => array_values(array_unique($events)),
            'operational_impact' => $operationalImpact,
            'trade_exposure_type' => 'Unknown',
            'confidence' => round(min(max($confidence, 0.0), 1.0), 2)
        ];
    }

    protected function zeroImpact(): array
    {
        return [
            'impact_score' => 0,
            'impact_level' => 'Low',
            'risk_direction' => 'Stable',
            'affected_countries' => [],
            'affected_sectors' => [],
            'impact_factors' => [],
            'operational_impact' => 'No direct operational supply chain or trade event detected.',
            'trade_exposure_type' => 'Unknown',
            'confidence' => 0.50
        ];
    }

    protected function detectOperationalEvents(string $text): array
    {
        $events = [];
        $mappings = [
            'Tariff change' => ['tariff', 'duty', 'import tax', 'import tariff', 'export tariff'],
            'Export restriction' => ['export ban', 'export control', 'export restriction', 'export limit'],
            'Import restriction' => ['import ban', 'import control', 'import restriction'],
            'Trade restriction' => ['trade restriction', 'trade ban', 'customs restriction', 'trade sanctions', 'sanction', 'retaliatory tariff', 'tariff increase'],
            'Trade agreement' => ['trade agreement', 'free trade agreement', 'fta', 'trade deal', 'tariff reduction', 'tariff cut', 'lower tariffs', 'zero tariff'],
            'Trade policy investigation' => ['trade practice', 'trade investigation', 'trade dispute'],
            
            'Port closure' => ['port closure', 'port shutdown'],
            'Port congestion' => ['port congestion', 'port backlog'],
            
            'Shipping disruption' => ['shipping disruption', 'vessel reroute', 'route disruption', 'blockade', 'shipping delay', 'vessel delay'],
            'Cargo disruption' => ['cargo disruption', 'maritime disruption', 'vessel disruption'],
            
            'Freight rate movement' => ['freight rate', 'shipping cost', 'shipping rate', 'freight disruption', 'freight capacity'],
            'Container shortage' => ['container shortage', 'equipment shortage'],
            'Container rate movement' => ['container rate'],
            
            'Factory shutdown' => ['factory shutdown', 'production halt', 'plant closure', 'manufacturing disruption', 'production disruption'],
            'Technology supply' => ['semiconductor supply', 'chip supply', 'semiconductor shortage', 'chip shortage', 'supply agreement'],
            
            'Supplier disruption' => ['supplier disruption', 'procurement disruption', 'supplier shortage', 'material shortage', 'component shortage'],
            
            'Logistics disruption' => ['logistics disruption', 'transport disruption', 'supply chain disruption', 'trucking disruption', 'warehouse disruption', 'rail freight disruption'],
            
            'Strike' => ['strike', 'walkout', 'labor dispute'],
            'Energy disruption' => ['fuel shortage', 'energy disruption', 'power outage', 'oil export ban', 'gasoline export ban', 'gas export ban', 'lng supply', 'oil supply disruption', 'refinery disruption']
        ];

        foreach ($mappings as $factor => $keywords) {
            foreach ($keywords as $keyword) {
                if (preg_match('/\b' . preg_quote($keyword, '/') . '(s|es|d|ed|ing)?\b/i', $text)) {
                    
                    // Contextual check for generic words like tariff
                    if ($factor === 'Tariff change' && !preg_match('/\b(import(s)?|export(s)?|customs|border(s)?|trade|cross-border|tariff(s)?)\b/i', $text)) {
                        continue;
                    }
                    
                    $events[] = $factor;
                    break;
                }
            }
        }

        return $events;
    }

    protected function calculateScore(string $text, array $events, array $countries, bool $isSpeculative): int
    {
        $score = 0;
        
        $hasMagnitude = preg_match('/(\d+(\.\d+)?%|\$\d+[a-z]*|million|billion|one-third|half|double|triple|massive|major|severe|critical)/i', $text);
        
        foreach ($events as $event) {
            if (in_array($event, ['Port closure', 'Factory shutdown', 'Shipping disruption', 'Strike'])) {
                $score += 35;
            } elseif (in_array($event, ['Tariff change', 'Export restriction', 'Import restriction', 'Trade restriction'])) {
                $score += 30;
            } elseif (in_array($event, ['Port congestion', 'Supplier disruption', 'Logistics disruption', 'Energy disruption', 'Technology supply'])) {
                $score += 25;
            } elseif (in_array($event, ['Freight rate movement', 'Container rate movement', 'Trade policy investigation', 'Cargo disruption'])) {
                $score += 15;
            } else {
                $score += 10;
            }
        }

        // Add points for geographic scope
        if (count($countries) >= 2) {
            $score += 15;
        } elseif (count($countries) == 1) {
            $score += 5;
        }

        // Add points for magnitude
        if ($hasMagnitude) {
            $score += 15;
        }

        if ($isSpeculative) {
            $score = (int)round($score * 0.7);
        }

        return min($score, 100);
    }

    protected function determineImpactLevel(int $score): string
    {
        if ($score <= 14) return 'Low';
        if ($score <= 39) return 'Medium';
        if ($score <= 69) return 'High';
        return 'Critical';
    }

    protected function determineRiskDirection(string $text, array $events): string
    {
        // Negation stripping: Remove negated directional phrases so they don't trigger false positives.
        $negationPatterns = [
            '/\b(?:tariffs?|sanctions?|export ban|import ban|trade restrictions?)\s+(?:\w+\s+){0,3}(?:will not|won\'t|not)\s+(?:\w+\s+){0,3}(?:increase(d|s)?|reduce(d|s)?|ease(d|s)?|lift(ed|s)?|impose(d|s)?)\b/i',
            '/\b(?:no|not|will not|won\'t)\s+(?:\w+\s+){0,3}(?:tariffs?|sanctions?|export ban|import ban|trade restrictions?|increase(d|s)?|reduce(d|s)?|ease(d|s)?|lift(ed|s)?|impose(d|s)?)\b/i'
        ];
        foreach ($negationPatterns as $pattern) {
            $text = preg_replace($pattern, ' ', $text);
        }

        $decreasingPhraseScore = 0;
        $increasingPhraseScore = 0;
        $decreasingVerbScore = 0;
        $increasingVerbScore = 0;

        // Level 1: Explicit Directional Phrases (Strongest)
        $decreasingPhrases = [
            'trade agreement', 'free trade agreement', 'fta', 'tariff reduction', 'tariff cut', 
            'tariffs reduced', 'lower tariffs', 'tariff removal', 'tariffs removed', 'zero tariff', 
            'zero-tariff', 'zero duty', 'zero-duty', 'duty free', 'duty-free', 'market access', 
            'trade facilitation', 'customs simplification', 'export restrictions lifted', 
            'export ban lifted', 'lifts export ban', 'export ban removed', 'remove export ban', 'removes export ban',
            'export ban ended', 'ends export ban', 'end export ban', 'sanctions lifted', 'lifts sanctions', 'lift sanctions',
            'trade restrictions eased', 'restrictions eased', 'trade barriers reduced', 
            'trade deal signed', 'trade deal approved', 'trade deal enters into force'
        ];
        
        $increasingPhrases = [
            'tariff increase', 'higher tariffs', 'tariffs increased', 'new tariffs', 'new tariff',
            'tariff hike', 'tariff imposed', 'tariffs imposed', 'additional tariff', 'retaliatory tariff',
            'trade war', 'trade conflict', 'trade tension', 'export control', 
            'embargo', 'customs restriction', 'border closure', 
            'port closure', 'shipping disruption', 'supply disruption', 'factory shutdown', 
            'production halt', 'freight disruption', 'port congestion', 'container shortage',
            'export ban imposed', 'imposes export ban', 'new export ban', 'introduces export ban',
            'announces export ban', 'extends export ban', 'export ban extended', 'tightens export restrictions',
            'expands export ban', 'import ban imposed', 'imposes import ban', 'new import ban',
            'new sanctions', 'sanctions imposed', 'imposes sanctions', 'sanctions extended',
            'trade restrictions tightened', 'new trade restrictions'
        ];

        foreach ($decreasingPhrases as $phrase) {
            if (preg_match('/\b' . preg_quote($phrase, '/') . '(s)?\b/i', $text)) {
                $decreasingPhraseScore += 2;
            }
        }
        foreach ($increasingPhrases as $phrase) {
            if (preg_match('/\b' . preg_quote($phrase, '/') . '(s)?\b/i', $text)) {
                $increasingPhraseScore += 2;
            }
        }

        $increasingPatterns = [
            '/\b(?:impose[ds]?|new|introduce[ds]?|announce[ds]?|extend(?:s|ed)?|tighten(?:s|ed)?|expand(?:s|ed)?)\s+(?:\w+\s+){0,5}(?:export bans?|import bans?|export restrictions?|import restrictions?|trade restrictions?|sanctions?|tariffs?|restrictions?)\b/i',
            '/\b(?:export bans?|import bans?|export restrictions?|import restrictions?|trade restrictions?|sanctions?|tariffs?|restrictions?)\s+(?:\w+\s+){0,5}(?:imposed|extended|tightened|expanded|announced|introduced)\b/i'
        ];

        $decreasingPatterns = [
            '/\b(?:lift(?:s|ed)?|remove[ds]?|end(?:s|ed)?|ease[ds]?|reduce[ds]?)\s+(?:\w+\s+){0,5}(?:export bans?|import bans?|export restrictions?|import restrictions?|trade restrictions?|sanctions?|tariffs?|restrictions?)\b/i',
            '/\b(?:export bans?|import bans?|export restrictions?|import restrictions?|trade restrictions?|sanctions?|tariffs?|restrictions?)\s+(?:\w+\s+){0,5}(?:lifted|removed|ended|eased|reduced)\b/i'
        ];

        foreach ($decreasingPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                $decreasingPhraseScore += 2;
            }
        }
        foreach ($increasingPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                $increasingPhraseScore += 2;
            }
        }

        // Level 2: Verbs / Patterns (Weaker)
        $decreasingVerbs = [
            'soften', 'decline', 'fall', 'drop', 'decrease', 'ease', 
            'lower', 'cool', 'normalize', 'recover', 'resume', 'lift', 'remove', 'cancel', 'clear', 'improve'
        ];
        $increasingVerbs = [
            'rise', 'increase', 'surge', 'jump', 'spike', 'soar', 
            'higher', 'escalate', 'worsen', 'impose', 'new', 'launch', 'build', 'grow', 'extend', 'tighten', 'expand'
        ];

        foreach ($decreasingVerbs as $verb) {
            if (preg_match('/\b' . preg_quote($verb, '/') . '(s|es|d|ed|ing)?\b/i', $text)) {
                $decreasingVerbScore += 1;
            }
        }
        foreach ($increasingVerbs as $verb) {
            if (preg_match('/\b' . preg_quote($verb, '/') . '(s|es|d|ed|ing)?\b/i', $text)) {
                $increasingVerbScore += 1;
            }
        }

        // Tiered resolution
        if ($decreasingPhraseScore > $increasingPhraseScore) {
            return 'Decreasing';
        } elseif ($increasingPhraseScore > $decreasingPhraseScore) {
            return 'Increasing';
        } elseif ($decreasingPhraseScore > 0 && $decreasingPhraseScore == $increasingPhraseScore) {
            return 'Stable'; // Explicit events are genuinely conflicting, ignore generic verbs
        }
        
        // Contextual Fallbacks (if no strong phrase overrides)
        if ($decreasingPhraseScore == 0 && $increasingPhraseScore == 0) {
            $negativeEvents = ['Port closure', 'Strike', 'Blockade', 'Shipping disruption', 'Factory shutdown', 'Supplier shortage'];
            foreach ($negativeEvents as $ne) {
                if (in_array($ne, $events)) {
                    $increasingVerbScore += 1;
                    break;
                }
            }
        }
        
        if ($decreasingVerbScore > $increasingVerbScore) {
            return 'Decreasing';
        } elseif ($increasingVerbScore > $decreasingVerbScore) {
            return 'Increasing';
        }
        
        return 'Stable';
    }

    protected function extractCountries(string $title, string $desc, string $content): array
    {
        $text = $title . ' ' . $desc;
        
        $countryAliases = [
            'US' => 'United States',
            'USA' => 'United States',
            'U.S.' => 'United States',
            'U.S.A.' => 'United States',
            'United States' => 'United States',
            'United States of America' => 'United States',
            'UK' => 'United Kingdom',
            'U.K.' => 'United Kingdom',
            'Britain' => 'United Kingdom',
            'United Kingdom' => 'United Kingdom',
            'EU' => 'European Union',
            'European Union' => 'European Union',
            'China' => 'China',
            'Singapore' => 'Singapore',
            'Taiwan' => 'Taiwan',
            'India' => 'India',
            'Japan' => 'Japan',
            'Germany' => 'Germany',
            'France' => 'France',
            'Brazil' => 'Brazil',
            'Mexico' => 'Mexico',
            'Canada' => 'Canada'
        ];

        $found = [];
        foreach ($countryAliases as $alias => $normalized) {
            if (preg_match('/\b' . preg_quote($alias, '/') . '\b/', $text)) {
                $found[$normalized] = true;
            }
        }
        
        return array_keys($found);
    }

    protected function extractSectors(string $text, array $events): array
    {
        $sectors = [];

        // Only assign sector if contextually supported by events or specific strong keywords
        $hasShippingEvent = array_intersect(['Shipping disruption', 'Shipping delay', 'Freight rate movement', 'Port closure', 'Port congestion', 'Container shortage'], $events);
        if ($hasShippingEvent || preg_match('/\b(ocean freight|maritime shipping|vessel|container ship)\b/i', $text)) {
            $sectors[] = 'Maritime Shipping';
        }

        $hasTradeEvent = array_intersect(['Tariff change', 'Export restriction', 'Import restriction', 'Sanctions affecting trade', 'Customs action', 'Trade policy investigation'], $events);
        if ($hasTradeEvent) {
            $sectors[] = 'International Trade';
        }

        $hasMfgEvent = array_intersect(['Factory shutdown', 'Manufacturing disruption'], $events);
        if ($hasMfgEvent || preg_match('/\b(manufacturing plant|factory production)\b/i', $text)) {
            $sectors[] = 'Manufacturing';
        }

        $hasLogisticsEvent = array_intersect(['Logistics disruption', 'Warehouse disruption'], $events);
        if ($hasLogisticsEvent || preg_match('/\b(distribution center|warehouse operations)\b/i', $text)) {
            $sectors[] = 'Logistics';
        }

        if (preg_match('/\b(semiconductor|chip|semiconductors)\b/i', $text)) {
            $sectors[] = 'Technology';
        }

        if (preg_match('/\b(oil|gas|lng supply|energy disruption)\b/i', $text)) {
            $sectors[] = 'Energy';
        }

        return array_unique($sectors);
    }

    protected function isSpeculative(string $text): bool
    {
        return preg_match('/\b(could|may|proposed|expected to|warning|possible|potential|considering)\b/i', $text) === 1;
    }

    protected function isPromotional(string $text): bool
    {
        $promoWords = ['oem', 'odm', 'wholesale', 'distributor', 'product launch', 'press release', 'manufacturer offers'];
        $promoCount = 0;
        foreach ($promoWords as $word) {
            if (preg_match('/\b' . preg_quote($word, '/') . '\b/i', $text)) {
                $promoCount++;
            }
        }
        return $promoCount >= 1;
    }

    protected function generateOperationalImpact(array $factors, array $sectors, array $countries, string $direction): string
    {
        if (empty($factors)) {
            return "This event may have minor operational effects on supply chain or trade activities.";
        }

        $factorStr = strtolower($factors[0]);

        if (in_array('Tariff change', $factors)) {
            if ($direction === 'Decreasing') {
                return "A tariff reduction or removal may ease cross-border trade costs and improve market access.";
            } else {
                return "A new or increased tariff may raise cross-border trade costs and place pricing pressure on affected importers and exporters.";
            }
        }
        
        if (in_array('Trade agreement', $factors)) {
            return "The trade agreement may reduce cross-border trade barriers and improve market access between the affected economies.";
        }
        
        if (in_array('Freight rate movement', $factors)) {
            if ($direction === 'Decreasing') {
                return "Falling ocean freight rates may reduce transportation costs and ease shipping cost pressure.";
            } else {
                return "Rising freight rates may increase transportation costs and strain shipping budgets.";
            }
        }
        
        if (in_array('Port congestion', $factors)) {
            if ($direction === 'Decreasing') {
                return "An easing of port congestion may improve vessel waiting times and accelerate container movement.";
            } else {
                return "Port congestion may increase vessel waiting times and delay container movement.";
            }
        }

        if (in_array('Factory shutdown', $factors)) {
            return "A factory shutdown may reduce production availability and create downstream supplier delays.";
        }
        
        if (in_array('Shipping disruption', $factors)) {
            return "A shipping disruption may delay cargo delivery and force vessels to reroute.";
        }

        // Generic fallback with verified facts
        $action = "may cause operational constraints.";
        if ($direction === 'Decreasing') {
            $action = "may ease operational constraints.";
        }
        
        $countryStr = "";
        if (!empty($countries)) {
            $countryStr = " involving " . implode(' and ', array_slice($countries, 0, 2));
        }

        return ucfirst("The detected {$factorStr}{$countryStr} {$action}");
    }

    protected function calculateConfidence(string $title, string $fullText, array $events, array $countries, array $sectors, bool $isSpeculative): float
    {
        $confidence = 0.50;

        // Higher if event in title
        foreach ($events as $event) {
            if (stripos($title, strtolower($event)) !== false) {
                $confidence += 0.20;
                break;
            }
        }

        if (count($countries) > 0) $confidence += 0.10;
        if (count($sectors) > 0) $confidence += 0.10;
        if (preg_match('/(\d+(\.\d+)?%|\$\d+[a-z]*|million|billion|one-third|half|double)/i', $fullText)) $confidence += 0.10;

        if ($isSpeculative) $confidence -= 0.20;

        return min(max($confidence, 0.0), 1.0);
    }
}
