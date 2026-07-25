<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsCache;

class CleanIrrelevantNewsCommand extends Command
{
    protected $signature = 'news:clean-irrelevant {--delete : Actually delete the irrelevant articles}';
    protected $description = 'Clean up legacy news articles that fail the final trade intelligence quality gate';

    public function handle()
    {
        $isDelete = $this->option('delete');
        $this->info("Starting Final Quality Gate cleanup...");
        if (!$isDelete) {
            $this->info("RUNNING IN DRY-RUN MODE. Pass --delete to actually remove articles.");
        }

        $allArticles = NewsCache::withoutGlobalScopes()->get();
        $initialCount = $allArticles->count();
        $this->info("Total articles in database before cleanup: {$initialCount}");

        $rejectedArticles = collect();
        $keptArticles = collect();

        foreach ($allArticles as $article) {
            // Replicate the Final Quality Gate
            $score = $article->impact_score ?? 0;
            $factors = $article->impact_factors ?? [];
            $sectors = $article->affected_sectors ?? [];
            $opImpact = $article->operational_impact ?? '';

            $isQualityGatePassed = true;
            if ($score == 0 
                && empty($factors) 
                && empty($sectors)
                && stripos($opImpact, 'No direct operational') !== false
            ) {
                $isQualityGatePassed = false;
            }

            if (!$isQualityGatePassed) {
                $rejectedArticles->push($article);
            } else {
                $keptArticles->push($article);
            }
        }

        $this->info("Found {$rejectedArticles->count()} articles failing the final quality gate.");

        if ($rejectedArticles->count() > 0) {
            $this->line("");
            $this->info("--- ARTICLES TO BE REMOVED ---");
            foreach ($rejectedArticles as $rej) {
                $this->warn("Title: {$rej->title}");
                $this->line("Score: {$rej->impact_score} | Factors: " . (empty($rej->impact_factors) ? '[]' : implode(',', $rej->impact_factors)));
                $this->line("Reason: No direct operational supply-chain or international-trade event detected.");
                $this->line(str_repeat('-', 40));
                
                if ($isDelete) {
                    $rej->delete();
                }
            }
        }

        $this->line("");
        $this->info("--- ARTICLES REMAINING ---");
        foreach ($keptArticles as $kept) {
            $this->line("Title: {$kept->title}");
        }

        $this->line("");
        if ($isDelete) {
            $finalCount = NewsCache::withoutGlobalScopes()->count();
            $this->info("Cleanup complete. Deleted {$rejectedArticles->count()} articles.");
            $this->info("Final article count: {$finalCount}");
        } else {
            $this->info("Dry-run complete. Run with --delete to apply changes.");
        }
    }
}
