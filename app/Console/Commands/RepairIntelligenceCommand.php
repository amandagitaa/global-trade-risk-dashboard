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
    protected $signature = 'news:repair-intelligence';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One-time repair command to backfill NULL intelligence fields in legacy news_cache records.';

    /**
     * Execute the console command.
     */
    public function handle(
        TradeImpactAnalyzer $tradeImpactAnalyzer,
        CountryPortImpactMapper $impactMapper,
        TradeIntelligenceSummaryService $summaryService
    ) {
        $this->info("Starting News Intelligence Repair Process...");

        $query = NewsCache::withoutGlobalScopes()
            ->where(function ($q) {
                $q->whereNull('impact_score')
                  ->orWhereNull('impact_level')
                  ->orWhereNull('risk_direction')
                  ->orWhereNull('trade_exposure_type')
                  ->orWhereNull('intelligence_summary');
            });

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
            &$processed, &$updated, &$skipped, &$failed,
            $tradeImpactAnalyzer, $impactMapper, $summaryService
        ) {
            foreach ($articles as $article) {
                try {
                    $articleData = $article->toArray();
                    $title = $articleData['title'] ?? 'Unknown Title';
                    $category = $articleData['category'] ?? 'General';

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

                    // 4. Update the existing row
                    $article->update([
                        'impact_score' => $tradeImpactData['impact_score'] ?? 0,
                        'impact_level' => $tradeImpactData['impact_level'] ?? 'Low',
                        'risk_direction' => $tradeImpactData['risk_direction'] ?? 'Stable',
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
                        'trade_exposure_type' => $mappedImpact['trade_exposure_type'] ?? 'Unknown',
                        'mapping_confidence' => $mappedImpact['mapping_confidence'] ?? 0.00,
                    ]);

                    $updated++;
                } catch (\Exception $e) {
                    $failed++;
                    Log::error("RepairIntelligenceCommand: Failed to process article ID {$article->id}: " . $e->getMessage());
                }

                $processed++;
            }

            $this->info("Progress: Processed {$processed} records...");
        });

        $this->line(str_repeat('=', 50));
        $this->info("Repair Intelligence Complete");
        $this->line("Processed: {$processed}");
        $this->line("Updated: {$updated}");
        $this->line("Skipped: {$skipped}");
        $this->line("Failed: {$failed}");
        $this->line(str_repeat('=', 50));

        return Command::SUCCESS;
    }
}
