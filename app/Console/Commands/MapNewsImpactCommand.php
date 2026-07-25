<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsCache;
use App\Services\News\CountryPortImpactMapper;

class MapNewsImpactCommand extends Command
{
    protected $signature = 'news:map-impact {--force : Recompute impact mapping for all validated articles}';
    protected $description = 'Map trade intelligence articles to specific Country and Port entities';

    public function handle(CountryPortImpactMapper $mapper)
    {
        $this->info("Starting Country & Port Impact Mapping...");
        
        $articles = NewsCache::withoutGlobalScopes()->get();
        $total = $articles->count();
        $this->info("Total validated articles in cache: {$total}");

        $force = $this->option('force');
        if ($force) {
            $this->warn("FORCE MODE: Recomputing mapping for all articles.");
        }

        $processed = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($articles as $article) {
            if (!$force && $article->mapping_confidence !== null) {
                $skipped++;
                continue;
            }

            try {
                $mappedImpact = $mapper->map($article->toArray());
                
                $article->mapped_countries = $mappedImpact['mapped_countries'];
                $article->mapped_ports = $mappedImpact['mapped_ports'];
                $article->regional_entities = $mappedImpact['regional_entities'];
                $article->port_impact_type = $mappedImpact['port_impact_type'];
                $article->trade_exposure_type = $mappedImpact['trade_exposure_type'];
                $article->mapping_confidence = $mappedImpact['mapping_confidence'];
                
                $article->save();
                
                $processed++;

                $this->line(str_repeat('-', 50));
                $this->line("Title: {$article->title}");
                $this->line("Mapped Countries: " . json_encode(array_column($article->mapped_countries ?? [], 'name')));
                $this->line("Regional Entities: " . json_encode(array_column($article->regional_entities ?? [], 'name')));
                $this->line("Mapped Ports: " . json_encode(array_column($article->mapped_ports ?? [], 'name')));
                $this->line("Port Impact Type: {$article->port_impact_type}");
                $this->line("Trade Exposure Type: {$article->trade_exposure_type}");
                $this->line("Confidence: {$article->mapping_confidence}");

            } catch (\Exception $e) {
                $this->error("Failed to map impact for '{$article->title}': " . $e->getMessage());
                $failed++;
            }
        }

        $this->line(str_repeat('=', 50));
        $this->info("Mapping Complete");
        $this->line("Processed: {$processed}");
        $this->line("Skipped: {$skipped}");
        $this->line("Failed: {$failed}");
    }
}
