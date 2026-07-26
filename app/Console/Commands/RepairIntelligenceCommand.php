<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsCache;
use App\Services\News\TradeImpactAnalyzer;
use App\Services\News\CountryPortImpactMapper;
use App\Services\News\TradeIntelligenceSummaryService;
use Illuminate\Support\Facades\Log;

class RepairIntelligenceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:repair-intelligence {--all : Repair all existing records, not just those with NULL fields} {--dry-run : Simulate the repair process without writing to the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One-time repair command to backfill NULL intelligence fields in legacy news_cache records or re-analyze all records.';

    /**
     * Execute the console command.
     */
    public function handle(
        TradeImpactAnalyzer $tradeImpactAnalyzer,
        CountryPortImpactMapper $impactMapper,
        TradeIntelligenceSummaryService $summaryService
    ) {
        $isDryRun = $this->option('dry-run');
        $isAll = $this->option('all');

        $this->info("Starting News Intelligence Repair Process" . ($isDryRun ? " (DRY RUN)" : "") . "...");

        $query = NewsCache::withoutGlobalScopes();

        if (!$isAll) {
            $query->where(function ($q) {
                $q->whereNull('impact_score')
                  ->orWhereNull('impact_level')
                  ->orWhereNull('risk_direction')
                  ->orWhereNull('trade_exposure_type')
                  ->orWhereNull('intelligence_summary');
            });
        }

        $totalRecords = (clone $query)->count();
        
        if ($totalRecords === 0) {
            $this->info("No records found requiring repair. Everything is up to date.");
            return Command::SUCCESS;
        }

        $this->info("Found {$totalRecords} records to repair.");

        $processed = 0;
        $updated = 0;
        $skipped = 0;
        $failed = 0;
        
        $dirtyFieldsCount = [
            'risk_direction' => 0, 'impact_score' => 0, 'impact_level' => 0,
            'trade_exposure_type' => 0, 'operational_factors' => 0,
            'mapped_countries' => 0, 'mapped_ports' => 0, 'affected_sectors' => 0,
            'intelligence_summary' => 0,
        ];
        
        $directionTransitions = [
            'Increasing -> Decreasing' => 0, 'Increasing -> Stable' => 0,
            'Stable -> Increasing' => 0, 'Stable -> Decreasing' => 0,
            'Decreasing -> Increasing' => 0, 'Decreasing -> Stable' => 0,
            'Unchanged' => 0
        ];
        
        $changeCategories = [
            'summary_only' => 0, 'exposure_only' => 0, 'mapping_only' => 0,
            'score_only' => 0, 'multiple_fields' => 0, 'score_jump_over_20' => 0
        ];
        
        $examples = [];

        $query->chunkById(100, function ($articles) use (
            &$processed, &$updated, &$skipped, &$failed, $isDryRun,
            &$dirtyFieldsCount, &$directionTransitions, &$changeCategories, &$examples,
            $tradeImpactAnalyzer, $impactMapper, $summaryService
        ) {
            foreach ($articles as $article) {
                try {
                    $articleData = $article->toArray();
                    $title = $articleData['title'] ?? 'Unknown Title';
                    // We DO NOT update sentiment or category.
                    
                    $oldDirection = $articleData['risk_direction'] ?? 'Unknown';
                    $oldExposure = $articleData['trade_exposure_type'] ?? 'Unknown';

                    // 1. Re-run TradeImpactAnalyzer
                    try {
                        $tradeImpactData = $tradeImpactAnalyzer->analyze($articleData);
                    } catch (\Exception $e) {
                        Log::error("RepairIntelligenceCommand: TradeImpactAnalyzer failed for article '{$title}' - " . $e->getMessage());
                        $tradeImpactData = [
                            'impact_score' => 0,
                            'impact_level' => 'Low',
                            'risk_direction' => 'Stable',
                            'trade_exposure_type' => 'Unknown',
                            'affected_countries' => [],
                            'affected_sectors' => [],
                            'impact_factors' => [],
                            'operational_impact' => 'No significant trade impact detected.',
                            'confidence' => 0.50
                        ];
                    }

                    // 2. Re-run TradeIntelligenceSummaryService
                    $intelligenceSummary = 'No significant trade impact detected.';
                    try {
                        $summaryPayload = array_merge($articleData, [
                            'trade_impact' => $tradeImpactData,
                        ]);
                        $intelligenceSummary = $summaryService->generate($summaryPayload) ?? 'No significant trade impact detected.';
                    } catch (\Exception $e) {
                        Log::error("RepairIntelligenceCommand: Failed to generate summary for '{$title}' - " . $e->getMessage());
                    }

                    // 3. Re-run CountryPortImpactMapper
                    $mappedImpact = [
                        'mapped_countries' => [],
                        'mapped_ports' => [],
                        'regional_entities' => [],
                        'port_impact_type' => 'NONE',
                        'trade_exposure_type' => 'Unknown',
                        'mapping_confidence' => 0.00,
                    ];
                    try {
                        $mapperPayload = array_merge($articleData, [
                            'trade_impact' => $tradeImpactData,
                        ]);
                        $mappedImpact = array_merge($mappedImpact, $impactMapper->map($mapperPayload));
                    } catch (\Exception $e) {
                        Log::error("RepairIntelligenceCommand: Failed to map impact for '{$title}' - " . $e->getMessage());
                    }
                    
                    $newDirection = $tradeImpactData['risk_direction'] ?? 'Stable';
                    $newExposure = $mappedImpact['trade_exposure_type'] ?? 'Unknown';
                    
                    $newData = [
                        'impact_score' => $tradeImpactData['impact_score'] ?? 0,
                        'impact_level' => $tradeImpactData['impact_level'] ?? 'Low',
                        'risk_direction' => $newDirection,
                        'impact_confidence' => $tradeImpactData['confidence'] ?? 0.50,
                        'affected_countries' => $tradeImpactData['affected_countries'] ?? [],
                        'affected_sectors' => $tradeImpactData['affected_sectors'] ?? [],
                        'impact_factors' => $tradeImpactData['impact_factors'] ?? [],
                        'operational_impact' => $tradeImpactData['operational_impact'] ?? null,
                        'intelligence_summary' => $intelligenceSummary,
                        'mapped_countries' => $mappedImpact['mapped_countries'] ?? [],
                        'mapped_ports' => $mappedImpact['mapped_ports'] ?? [],
                        'regional_entities' => $mappedImpact['regional_entities'] ?? [],
                        'port_impact_type' => $mappedImpact['port_impact_type'] ?? 'NONE',
                        'trade_exposure_type' => $newExposure,
                        'mapping_confidence' => $mappedImpact['mapping_confidence'] ?? 0.00,
                    ];
                    
                    // Normalize helper to ignore key order in JSON equality
                    $normalize = function ($value) use (&$normalize) {
                        if (is_string($value)) {
                            $decoded = json_decode($value, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $value = $decoded;
                            }
                        }
                        if (is_array($value)) {
                            foreach ($value as &$v) {
                                $v = $normalize($v);
                            }
                            // ksort only if associative array or object cast to array
                            if (array_keys($value) !== range(0, count($value) - 1)) {
                                ksort($value);
                            }
                        }
                        return $value;
                    };

                    $arrayFields = ['affected_countries', 'affected_sectors', 'impact_factors', 'mapped_countries', 'mapped_ports', 'regional_entities'];
                    
                    foreach ($arrayFields as $field) {
                        if (isset($newData[$field])) {
                            $oldVal = $normalize($article->getOriginal($field));
                            $newVal = $normalize($newData[$field]);
                            
                            if (json_encode($oldVal) === json_encode($newVal)) {
                                unset($newData[$field]);
                            }
                        }
                    }
                    
                    $article->fill($newData);
                    
                    if (!$article->isDirty()) {
                        $skipped++;
                    } else {
                        $dirty = $article->getDirty();
                        
                        // Track individual fields
                        foreach (array_keys($dirty) as $field) {
                            if (isset($dirtyFieldsCount[$field])) {
                                $dirtyFieldsCount[$field]++;
                            }
                        }
                        
                        // Direction Transitions
                        $transitionKey = "{$oldDirection} -> {$newDirection}";
                        if ($oldDirection === $newDirection) {
                            $directionTransitions['Unchanged']++;
                        } else {
                            if (isset($directionTransitions[$transitionKey])) {
                                $directionTransitions[$transitionKey]++;
                            } else {
                                $directionTransitions[$transitionKey] = 1;
                            }
                            
                            // Representative examples
                            if (!isset($examples[$transitionKey]) || count($examples[$transitionKey]) < 5) {
                                $examples[$transitionKey][] = "ID: {$article->id} | Title: {$title}\n    OLD: {$oldDirection} | NEW: {$newDirection}";
                            }
                        }
                        
                        // Score jump > 20
                        $oldScore = $articleData['impact_score'] ?? 0;
                        $newScore = $newData['impact_score'] ?? 0;
                        if (abs($newScore - $oldScore) > 20) {
                            $changeCategories['score_jump_over_20']++;
                        }

                        // Exclusive change logic
                        $dirtyKeys = array_keys($dirty);
                        $hasSummary = in_array('intelligence_summary', $dirtyKeys);
                        $hasExposure = in_array('trade_exposure_type', $dirtyKeys);
                        $hasMapping = in_array('mapped_countries', $dirtyKeys) || in_array('mapped_ports', $dirtyKeys) || in_array('regional_entities', $dirtyKeys);
                        $hasScore = in_array('impact_score', $dirtyKeys) || in_array('impact_level', $dirtyKeys);

                        if ($hasSummary && count($dirtyKeys) === 1) $changeCategories['summary_only']++;
                        if ($hasExposure && count($dirtyKeys) === 1) $changeCategories['exposure_only']++;
                        
                        $mappingFieldsOnly = true;
                        foreach ($dirtyKeys as $f) {
                            if (!in_array($f, ['mapped_countries', 'mapped_ports', 'regional_entities', 'mapping_confidence'])) {
                                $mappingFieldsOnly = false;
                            }
                        }
                        if ($mappingFieldsOnly && $hasMapping) $changeCategories['mapping_only']++;

                        $scoreFieldsOnly = true;
                        foreach ($dirtyKeys as $f) {
                            if (!in_array($f, ['impact_score', 'impact_level', 'impact_confidence'])) {
                                $scoreFieldsOnly = false;
                            }
                        }
                        if ($scoreFieldsOnly && $hasScore) $changeCategories['score_only']++;
                        
                        if (count($dirtyKeys) > 1 && !$mappingFieldsOnly && !$scoreFieldsOnly) {
                            $changeCategories['multiple_fields']++;
                        }
                        
                        if (!$isDryRun) {
                            $article->save();
                        }
                        $updated++;
                    }
                } catch (\Exception $e) {
                    $failed++;
                    Log::error("RepairIntelligenceCommand: Failed to process article ID {$article->id}: " . $e->getMessage());
                }

                $processed++;
            }

            $this->info("Progress: Processed {$processed} records...");
        });

        $this->line(str_repeat('=', 50));
        $this->info("Repair Intelligence Complete" . ($isDryRun ? " (DRY RUN)" : ""));
        $this->line("Processed: {$processed}");
        $this->line($isDryRun ? "Would Update: {$updated}" : "Updated: {$updated}");
        $this->line("Skipped: {$skipped}");
        $this->line("Failed: {$failed}");
        
        if ($isDryRun) {
            $this->line(str_repeat('-', 50));
            $this->info("FIELD CHANGE COUNTS");
            foreach ($dirtyFieldsCount as $field => $count) {
                $this->line("{$field}: {$count}");
            }
            
            $this->line(str_repeat('-', 50));
            $this->info("RISK DIRECTION TRANSITIONS");
            foreach ($directionTransitions as $transition => $count) {
                $this->line("{$transition}: {$count}");
            }
            
            $this->line(str_repeat('-', 50));
            $this->info("DIAGNOSTIC GROUPS");
            foreach ($changeCategories as $cat => $count) {
                $this->line("{$cat}: {$count}");
            }
            
            $this->line(str_repeat('-', 50));
            $this->info("REPRESENTATIVE EXAMPLES");
            foreach ($examples as $transition => $items) {
                $this->info("Transition: {$transition}");
                foreach ($items as $item) {
                    $this->line("  - {$item}");
                }
            }
        }
        $this->line(str_repeat('=', 50));

        return Command::SUCCESS;
    }
}
