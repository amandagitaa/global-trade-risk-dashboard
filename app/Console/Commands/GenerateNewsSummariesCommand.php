<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsCache;
use App\Services\News\TradeIntelligenceSummaryService;

class GenerateNewsSummariesCommand extends Command
{
    protected $signature = 'news:generate-summaries {--force : Regenerate all summaries even if they already exist}';
    protected $description = 'Generate deterministic trade intelligence summaries for existing validated articles';

    public function handle(TradeIntelligenceSummaryService $summaryService)
    {
        $this->info("Starting Summary Generation...");
        
        $articles = NewsCache::withoutGlobalScopes()->get();
        $total = $articles->count();
        $this->info("Total validated articles in cache: {$total}");

        $force = $this->option('force');
        if ($force) {
            $this->warn("FORCE MODE: Overwriting all existing summaries.");
        }

        $processed = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($articles as $article) {
            if (!$force && !empty($article->intelligence_summary)) {
                $skipped++;
                continue;
            }

            try {
                $summary = $summaryService->generate($article->toArray());
                
                $article->intelligence_summary = $summary;
                $article->save();
                
                $processed++;

                $this->line(str_repeat('-', 50));
                $this->line("Title: {$article->title}");
                $this->line("Category: {$article->category}");
                $this->line("Impact Level: {$article->impact_level}");
                $this->line("Risk Direction: {$article->risk_direction}");
                $this->line("Affected Countries: " . (empty($article->affected_countries) ? '[]' : implode(', ', $article->affected_countries)));
                $this->line("Affected Sectors: " . (empty($article->affected_sectors) ? '[]' : implode(', ', $article->affected_sectors)));
                $this->line("Impact Factors: " . (empty($article->impact_factors) ? '[]' : implode(', ', $article->impact_factors)));
                $this->line("");
                $this->info("Generated Intelligence Summary:");
                $this->line($summary);

            } catch (\Exception $e) {
                $this->error("Failed to generate summary for '{$article->title}': " . $e->getMessage());
                $failed++;
            }
        }

        $this->line(str_repeat('=', 50));
        $this->info("Summary Generation Complete");
        $this->line("Processed: {$processed}");
        $this->line("Skipped: {$skipped}");
        $this->line("Failed: {$failed}");
    }
}
