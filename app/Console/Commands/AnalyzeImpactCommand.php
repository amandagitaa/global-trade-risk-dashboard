<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsCache;
use App\Services\News\TradeImpactAnalyzer;

class AnalyzeImpactCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:analyze-impact {--limit=10} {--all} {--persist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the Trade Impact Analyzer on existing articles in the database for diagnostic purposes.';

    /**
     * Execute the console command.
     */
    public function handle(TradeImpactAnalyzer $analyzer)
    {
        $isPersist = $this->option('persist');

        $query = NewsCache::withoutGlobalScopes();
        
        if (!$this->option('all') && $this->option('limit')) {
            $query->limit((int) $this->option('limit'));
        }
        
        $articles = $query->orderBy('id', 'desc')->get();

        if ($articles->isEmpty()) {
            $this->info("No articles found in news_cache.");
            return;
        }

        $uniqueArticles = collect();
        $seenUrls = [];
        $seenTitles = [];

        foreach ($articles as $articleModel) {
            $url = trim($articleModel->url ?? '');
            
            $rawTitle = $articleModel->title ?? '';
            $normTitle = strtolower(trim(preg_replace('/\s+/', ' ', preg_replace('/[^\w\s]/', '', $rawTitle))));

            if ($url && in_array($url, $seenUrls)) continue;
            if ($normTitle && in_array($normTitle, $seenTitles)) continue;

            if ($url) $seenUrls[] = $url;
            if ($normTitle) $seenTitles[] = $normTitle;
            
            $uniqueArticles->push($articleModel);
        }

        $this->info("Analyzing {$uniqueArticles->count()} UNIQUE articles (filtered from {$articles->count()})...");
        
        if ($isPersist) {
            $this->info("PERSIST MODE: Saving intelligence metadata back to the database.");
        }

        $persistedCount = 0;

        foreach ($uniqueArticles as $articleModel) {
            $article = [
                'title' => $articleModel->title,
                'description' => $articleModel->description,
                'content' => $articleModel->content,
            ];

            try {
                $impact = $analyzer->analyze($article);
            } catch (\Exception $e) {
                $this->error("Failed to analyze: {$article['title']} - " . $e->getMessage());
                continue;
            }

            if ($isPersist) {
                $articleModel->update([
                    'impact_score' => $impact['impact_score'],
                    'impact_level' => $impact['impact_level'],
                    'risk_direction' => $impact['risk_direction'],
                    'impact_confidence' => $impact['confidence'],
                    'affected_countries' => $impact['affected_countries'],
                    'affected_sectors' => $impact['affected_sectors'],
                    'impact_factors' => $impact['impact_factors'],
                    'operational_impact' => $impact['operational_impact']
                ]);
                $persistedCount++;
            }

            $this->line("--------------------------------------------------");
            $this->line("Title: " . $articleModel->title);
            $this->line("Source: " . ($articleModel->source ?? 'Unknown'));
            $this->line("Existing Category: " . ($articleModel->category ?? 'None'));
            $this->line("");
            $this->line("TRADE IMPACT ANALYSIS");
            $this->line("");
            $this->line("Impact Score: " . $impact['impact_score']);
            $this->line("Impact Level: " . $impact['impact_level']);
            $this->line("Risk Direction: " . $impact['risk_direction']);
            $this->line("Confidence: " . number_format($impact['confidence'], 2));
            $this->line("");
            $this->line("Affected Countries: " . (empty($impact['affected_countries']) ? 'None' : implode(', ', $impact['affected_countries'])));
            $this->line("Affected Sectors: " . (empty($impact['affected_sectors']) ? 'None' : implode(', ', $impact['affected_sectors'])));
            $this->line("");
            $this->line("Impact Factors: " . (empty($impact['impact_factors']) ? 'None' : implode(', ', $impact['impact_factors'])));
            $this->line("");
            $this->line("Operational Impact: " . $impact['operational_impact']);
            $this->line("--------------------------------------------------");
        }

        $this->info("Analysis diagnostic complete.");
        if ($isPersist) {
            $this->line("");
            $this->info("Persisted intelligence metadata for {$persistedCount} articles.");
        }
    }
}
