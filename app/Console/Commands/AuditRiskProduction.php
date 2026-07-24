<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RiskScore;

class AuditRiskProduction extends Command
{
    protected $signature = 'risk:audit-production';
    protected $description = '100% Read-only audit for RiskScore final_score and risk_level mismatches on production';

    public function handle()
    {
        $this->info("=== PRODUCTION RISK SCORE AUDIT ===");

        $scores = RiskScore::all();

        $totalScores = $scores->count();
        $matchedScores = 0;
        $mismatchedScores = 0;
        $validLevels = 0;
        $invalidLevels = 0;

        $largestDifference = 0;
        $totalDifference = 0;

        $examples = [];

        foreach ($scores as $s) {
            $expectedFinal = round(
                ($s->weather_score * 0.25) +
                ($s->currency_score * 0.20) +
                ($s->economic_score * 0.15) +
                ($s->port_score * 0.20) +
                ($s->news_score * 0.20),
                2
            );

            $expectedLevel = 'critical';
            if ($expectedFinal <= 20) {
                $expectedLevel = 'safe';
            } elseif ($expectedFinal <= 40) {
                $expectedLevel = 'stable';
            } elseif ($expectedFinal <= 60) {
                $expectedLevel = 'alert';
            } elseif ($expectedFinal <= 80) {
                $expectedLevel = 'dangerous';
            }

            $diff = abs($expectedFinal - $s->final_score);

            if ($diff > 0.01) {
                $mismatchedScores++;
                $totalDifference += $diff;

                if ($diff > $largestDifference) {
                    $largestDifference = $diff;
                }

                if (count($examples) < 10) {
                    $examples[] = [
                        'country_id' => $s->country_id,
                        'stored_score' => $s->final_score,
                        'expected_score' => $expectedFinal,
                        'diff' => $diff,
                        'stored_level' => $s->risk_level,
                        'expected_level' => $expectedLevel,
                    ];
                }
            } else {
                $matchedScores++;
            }

            if ($s->risk_level === $expectedLevel) {
                $validLevels++;
            } else {
                $invalidLevels++;
            }
        }

        $averageDifference = $mismatchedScores > 0 ? ($totalDifference / $mismatchedScores) : 0;

        $this->info("");
        $this->info("Total Risk Scores: {$totalScores}");
        $this->info("Matched Final Scores: {$matchedScores}");
        $this->info("Mismatched Final Scores: {$mismatchedScores}");
        $this->info("Valid Risk Levels: {$validLevels}");
        $this->info("Invalid Risk Levels: {$invalidLevels}");
        
        if ($mismatchedScores > 0) {
            $this->info("");
            $this->info("Largest Score Difference: " . round($largestDifference, 2));
            $this->info("Average Score Difference: " . round($averageDifference, 2));

            $this->info("\n--- Examples of Mismatch ---");
            foreach ($examples as $idx => $ex) {
                $this->info(($idx + 1) . ". Country ID: {$ex['country_id']}");
                $this->info("   Stored Final Score: {$ex['stored_score']} | Expected Final Score: {$ex['expected_score']} | Difference: " . round($ex['diff'], 2));
                $this->info("   Stored Risk Level: {$ex['stored_level']} | Expected Risk Level: {$ex['expected_level']}");
                $this->info("");
            }
        } else {
            $this->info("\nAll scores are matched and valid!");
        }
    }
}
