<?php

namespace App\Services\News;

class TradeIntelligenceSummaryService
{
    /**
     * Generates a 2-3 sentence operational trade intelligence summary.
     * Does NOT use any LLM/API. Generates deterministically from validated metadata.
     */
    public function generate(array $articleData): string
    {
        $title = $articleData['title'] ?? '';
        $category = $articleData['category'] ?? 'General';
        
        $impactData = $articleData['trade_impact'] ?? [];
        // Support flat array format if passed from command vs sync service
        if (empty($impactData) && isset($articleData['impact_score'])) {
            $impactData = $articleData;
        }

        $impactScore = $impactData['impact_score'] ?? 0;
        $impactLevel = $impactData['impact_level'] ?? 'Low';
        $riskDirection = $impactData['risk_direction'] ?? 'Stable';
        $affectedCountries = $impactData['affected_countries'] ?? [];
        $affectedSectors = $impactData['affected_sectors'] ?? [];
        $impactFactors = $impactData['impact_factors'] ?? [];
        
        // 1. EVENT
        $eventSentence = $this->buildEventSentence($title, $impactFactors, $category);
        
        // 2. CONSEQUENCE
        $consequenceSentence = $this->buildConsequenceSentence($riskDirection, $impactLevel, $category);
        
        // 3. EXPOSURE
        $exposureSentence = $this->buildExposureSentence($affectedCountries, $affectedSectors);

        // Assemble Summary
        $sentences = [];
        if ($eventSentence) $sentences[] = $eventSentence;
        if ($consequenceSentence) $sentences[] = $consequenceSentence;
        if ($exposureSentence) $sentences[] = $exposureSentence;

        // Anti-Hallucination Fallback
        if (empty($sentences)) {
            return "No significant trade impact detected.";
        }

        return implode(' ', $sentences);
    }

    protected function buildEventSentence(string $title, array $factors, string $category): string
    {
        $event = trim($title);

        // Remove mechanical trailing attributions like " - Reuters" or " | Bloomberg"
        $event = preg_replace('/\s*[-|]\s*(Reuters|Bloomberg|CNBC|CNN|BBC|AP|Financial Times)$/i', '', $event);

        // Normalize colon usage for reports/updates to read naturally
        $event = preg_replace_callback('/(Update|Report|Briefing|Analysis|Alert|Notice):\s+([A-Z])/i', function($matches) {
            return $matches[1] . ' indicates ' . strtolower($matches[2]);
        }, $event);
        
        // Remove mechanical prefixes
        $event = preg_replace('/^(BREAKING|NEWS|URGENT):\s+/i', '', $event);

        $event = ucfirst($event);
        if (!preg_match('/[.!?]$/', $event)) {
            $event .= '.';
        }

        return $event;
    }

    protected function buildConsequenceSentence(string $riskDirection, string $impactLevel, string $category): string
    {
        $strengthWord = $this->getStrengthWord($impactLevel);
        
        if ($riskDirection === 'Increasing') {
            if ($category === 'Trade') {
                return "The measure may increase cross-border trade costs and place {$strengthWord} pricing pressure on affected importers and exporters.";
            } elseif ($category === 'Shipping' || $category === 'Logistics') {
                return "This may increase disruption risk and raise {$strengthWord} transportation costs.";
            } else {
                return "This development may increase pressure and create {$strengthWord} uncertainty for trade operations.";
            }
        } elseif ($riskDirection === 'Decreasing') {
            if ($category === 'Shipping' || $category === 'Logistics') {
                return "The decline may reduce transportation costs and ease pressure on shipping operations.";
            } else {
                return "This development may ease pressure and improve {$strengthWord} supply-chain conditions.";
            }
        } else {
            // Stable
            return "The operational impact is expected to remain {$strengthWord} but stable.";
        }
    }

    protected function buildExposureSentence(array $countries, array $sectors): string
    {
        if (empty($countries) && empty($sectors)) {
            return ""; 
        }

        if (!empty($countries)) {
            $countryStr = implode('-', $countries);
            if (!empty($sectors)) {
                $sectorStr = strtolower(implode(', ', $sectors));
                if (str_contains($sectorStr, 'international trade')) {
                     return "{$countryStr} trade flows face the primary exposure.";
                }
                return "{$countryStr} operations within the {$sectorStr} sector face the primary exposure.";
            }
            return "{$countryStr} trade flows face the primary exposure.";
        }

        if (!empty($sectors)) {
            $sectorStr = strtolower(implode(', ', $sectors));
            return "The {$sectorStr} sector is primarily affected.";
        }

        return "";
    }

    protected function getStrengthWord(string $impactLevel): string
    {
        return match($impactLevel) {
            'High' => 'substantial',
            'Medium' => 'moderate',
            'Low' => 'limited',
            default => 'moderate',
        };
    }
}
