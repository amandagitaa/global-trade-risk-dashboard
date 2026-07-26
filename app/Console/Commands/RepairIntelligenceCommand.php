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

        $query->chunkById(100, function ($articles) use (
            &$processed, &$updated, &$skipped, &$failed, $isDryRun,
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
                        if ($isDryRun) {
                            $this->line("[DRY RUN] Change detected for ID {$article->id}: Direction '{$oldDirection}' -> '{$newDirection}', Exposure '{$oldExposure}' -> '{$newExposure}'");
                            $dirty = $article->getDirty();
                            foreach ($dirty as $k => $v) {
                                $oldVal = $article->getOriginal($k);
                                $oldStr = is_array($oldVal) ? json_encode($oldVal) : $oldVal;
                                $newStr = is_array($v) ? json_encode($v) : $v;
                                $this->line("  DIRTY $k: OLD => $oldStr | NEW => $newStr");
                            }
                        } else {
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
        $this->line(str_repeat('=', 50));

        return Command::SUCCESS;
    }
}
