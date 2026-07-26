<?php

namespace App\Services\News;

use App\Contracts\NewsProviderInterface;
use App\Contracts\NewsRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class NewsSyncService
{
    public function __construct(
        protected NewsProviderInterface $apiService,
        protected NewsRepositoryInterface $repository,
        protected CountryResolver $countryResolver,
        protected CategoryResolver $categoryResolver,
        protected ImageResolver $imageResolver,
        protected DuplicateDetector $duplicateDetector,
        protected SentimentService $sentimentService,
        protected TradeRiskService $tradeRiskService,
        protected CacheService $cacheService,
        protected SupplyChainRelevanceFilter $relevanceFilter,
        protected TradeImpactAnalyzer $tradeImpactAnalyzer,
        protected CountryPortImpactMapper $impactMapper,
        protected TradeIntelligenceSummaryService $summaryService,
        protected TradeRelevanceFilter $tradeRelevanceFilter
    ) {}
    protected ?\Illuminate\Console\Command $command = null;

    public function setCommand(\Illuminate\Console\Command $command): void
    {
        $this->command = $command;
    }

    public function sync(): void
    {
        Log::info("NewsSyncService: Starting general sync");
        $startTime = microtime(true);
        
        $articles = $this->apiService->fetch();
        $this->processArticles($articles);
        
        $this->logCompletion('sync', $articles->count(), $startTime);
    }

    public function syncLatest(): void
    {
        Log::info("NewsSyncService: Starting latest sync");
        $startTime = microtime(true);
        
        $articles = $this->apiService->fetchLatest();
        $this->processArticles($articles);
        
        $this->logCompletion('syncLatest', $articles->count(), $startTime);
    }

    public function syncByCountry(string $countryCode): void
    {
        Log::info("NewsSyncService: Starting sync by country [{$countryCode}]");
        $startTime = microtime(true);
        
        $articles = $this->apiService->fetchByCountry($countryCode);
        $this->processArticles($articles);
        
        $this->logCompletion('syncByCountry', $articles->count(), $startTime);
    }

    public function syncByCategory(string $category): void
    {
        Log::info("NewsSyncService: Starting sync by category [{$category}]");
        $startTime = microtime(true);
        
        $articles = $this->apiService->fetchByCategory($category);
        $this->processArticles($articles);
        
        $this->logCompletion('syncByCategory', $articles->count(), $startTime);
    }

    public function refresh(): void
    {
        Log::info("NewsSyncService: Starting full refresh");
        $startTime = microtime(true);
        
        $this->cacheService->clear();
        $articles = $this->apiService->fetchEverything();
        $this->processArticles($articles);
        $this->cacheService->refresh();
        
        $this->logCompletion('refresh', $articles->count(), $startTime);
    }

    /**
     * Orchestrates the processing of a collection of articles.
     */
    protected function processArticles(Collection $articles): void
    {
        $this->duplicateDetector->reset();

        $stats = [
            'fetched' => $articles->count(),
            'duplicates' => 0,
            'rejected' => 0,
            'saved' => 0,
        ];
        
        $dupStats = [
            'batch_url' => 0,
            'batch_title' => 0,
            'db_url' => 0,
            'db_title' => 0,
            'cross_query' => 0,
        ];
        
        $queryStats = [];
        $categoryCounts = [];
        $batch = [];
        $batchSize = config('news.batch_size', 50);
        
        if ($this->command) {
            $this->command->line('');
            $this->command->info("Fetched: {$stats['fetched']} articles");
            $this->command->line('');
        }

        foreach ($articles as $article) {
            try {
                $title = $article['title'] ?? 'Unknown Title';
                $apiCategory = $article['api_category'] ?? 'unknown';
                $apiQuery = $article['api_query'] ?? 'unknown';
                
                if (!isset($queryStats[$apiCategory])) {
                    $queryStats[$apiCategory] = [
                        'query' => $apiQuery,
                        'returned' => 0,
                        'unique' => 0,
                        'cross_query_dup' => 0
                    ];
                }
                
                $queryStats[$apiCategory]['returned']++;
                
                // 1. Validation & Duplicate Check
                $relevanceData = $this->relevanceFilter->calculateScore($article);
                $coreScore = $relevanceData['core_score'] ?? 0;
                $contextScore = $relevanceData['context_score'] ?? 0;
                $finalScore = $relevanceData['score'] ?? 0;
                $hasCore = $coreScore > 0 ? 'YES' : 'NO';
                
                $duplicateStatus = $this->duplicateDetector->isDuplicate($article);
                
                if ($duplicateStatus['is_duplicate'] ?? false) {
                    $stats['duplicates']++;
                    
                    if ($duplicateStatus['is_cross_query'] ?? false) {
                        $dupStats['cross_query']++;
                        $queryStats[$apiCategory]['cross_query_dup']++;
                    } else if (str_contains($duplicateStatus['duplicate_reason'], 'Batch URL Duplicate')) {
                        $dupStats['batch_url']++;
                    } else if (str_contains($duplicateStatus['duplicate_reason'], 'Batch Title Duplicate')) {
                        $dupStats['batch_title']++;
                    } else if (str_contains($duplicateStatus['duplicate_reason'], 'Database URL Duplicate')) {
                        $dupStats['db_url']++;
                    } else if (str_contains($duplicateStatus['duplicate_reason'], 'Database Title Duplicate')) {
                        $dupStats['db_title']++;
                    }
                    
                    if ($this->command) {
                        $this->command->line("Article:\n\"{$title}\"");
                        $this->command->line('');
                        $this->command->line("API Query Origin: {$apiCategory}");
                        $this->command->line("Core Signal: {$hasCore}");
                        $this->command->line("Core Score: {$coreScore}");
                        $this->command->line("Context Score: {$contextScore}");
                        $this->command->line("Final Score: {$finalScore}");
                        
                        $detectedPhrases = empty($relevanceData['detected_phrases']) ? 'None' : implode(', ', $relevanceData['detected_phrases']);
                        $detectedConflicts = empty($relevanceData['detected_conflicts']) ? 'None' : implode(', ', $relevanceData['detected_conflicts']);
                        $operationalSignals = empty($relevanceData['operational_signals']) ? 'None' : implode(', ', $relevanceData['operational_signals']);
                        
                        $this->command->line("Detected Strong Phrases: {$detectedPhrases}");
                        if ($operationalSignals !== 'None') {
                            $this->command->line("Operational Trade Signals: {$operationalSignals}");
                        }
                        if ($detectedConflicts !== 'None') {
                            $this->command->line("Detected Context Conflict: {$detectedConflicts}");
                        }
                        
                        $this->command->line("Result: DUPLICATE");
                        $this->command->line("Reason: {$duplicateStatus['duplicate_reason']}");
                        $this->command->line("------------------------------------------------------------");
                        $this->command->line('');
                    }
                    continue;
                }
                
                // Track as unique for this query
                $queryStats[$apiCategory]['unique']++;

                // 2. Intelligent Trade Relevance Filter
                $tradeFilterEval = $this->tradeRelevanceFilter->evaluate($article);
                if (!$this->tradeRelevanceFilter->shouldAccept($tradeFilterEval)) {
                    $stats['rejected']++;
                    if ($this->command) {
                        $this->command->line("Article:\n\"{$title}\"");
                        $this->command->line('');
                        $this->command->line("API Query Origin: {$apiCategory}");
                        $this->command->line("Trade Relevance Score: {$tradeFilterEval['score']}");
                        $this->command->line("Result: REJECTED (Intelligent Filter)");
                        foreach ($this->tradeRelevanceFilter->getReasons($tradeFilterEval) as $reason) {
                            $this->command->line("- {$reason}");
                        }
                        $this->command->line("------------------------------------------------------------");
                        $this->command->line('');
                    }
                    continue;
                }

                // 3. Resolvers
                $categoryData = $this->categoryResolver->resolve($article);
                $article['category'] = $categoryData['category'];

                if (!$relevanceData['is_relevant']) {
                    $stats['rejected']++;
                    
                    if ($this->command) {
                        $this->command->line("Article:\n\"{$title}\"");
                        $this->command->line('');
                        $this->command->line("API Query Origin: {$apiCategory}");
                        $this->command->line("Core Signal: {$hasCore}");
                        $this->command->line("Core Score: {$coreScore}");
                        $this->command->line("Context Score: {$contextScore}");
                        $this->command->line("Final Score: {$finalScore}");
                        
                        $detectedPhrases = empty($relevanceData['detected_phrases']) ? 'None' : implode(', ', $relevanceData['detected_phrases']);
                        $detectedConflicts = empty($relevanceData['detected_conflicts']) ? 'None' : implode(', ', $relevanceData['detected_conflicts']);
                        $operationalSignals = empty($relevanceData['operational_signals']) ? 'None' : implode(', ', $relevanceData['operational_signals']);
                        
                        $this->command->line("Detected Strong Phrases: {$detectedPhrases}");
                        if ($operationalSignals !== 'None') {
                            $this->command->line("Operational Trade Signals: {$operationalSignals}");
                        }
                        if ($detectedConflicts !== 'None') {
                            $this->command->line("Detected Context Conflict: {$detectedConflicts}");
                        }
                        
                        $this->command->line("Category: N/A");
                        $this->command->line("Result: REJECTED");
                        $this->command->line("Why Passed: {$relevanceData['reason']}");
                        $this->command->line("------------------------------------------------------------");
                        $this->command->line('');
                    }
                    continue;
                }

                if ($this->command) {
                    $this->command->line("Article:\n\"{$title}\"");
                    $this->command->line('');
                    $this->command->line("API Query Origin: {$apiCategory}");
                    $this->command->line("Core Signal: {$hasCore}");
                    $this->command->line("Core Score: {$coreScore}");
                    $this->command->line("Context Score: {$contextScore}");
                    $this->command->line("Final Score: {$finalScore}");
                    
                    $detectedPhrases = empty($relevanceData['detected_phrases']) ? 'None' : implode(', ', $relevanceData['detected_phrases']);
                    $detectedConflicts = empty($relevanceData['detected_conflicts']) ? 'None' : implode(', ', $relevanceData['detected_conflicts']);
                    $operationalSignals = empty($relevanceData['operational_signals']) ? 'None' : implode(', ', $relevanceData['operational_signals']);
                    
                    $this->command->line("Detected Strong Phrases: {$detectedPhrases}");
                    if ($operationalSignals !== 'None') {
                        $this->command->line("Operational Trade Signals: {$operationalSignals}");
                    }
                    if ($detectedConflicts !== 'None') {
                        $this->command->line("Detected Context Conflict: {$detectedConflicts}");
                    }
                    
                    $this->command->line("Category: {$article['category']}");
                    $this->command->line("Result: SAVED");
                    $this->command->line("Why Passed: {$relevanceData['reason']}");
                    $this->command->line("------------------------------------------------------------");
                    $this->command->line('');
                }

                $countryData = $this->countryResolver->resolve($article);
                $imageUrl = $this->imageResolver->resolve($article);
                

                // 3. Analysis
                $sentimentData = $this->sentimentService->analyze($article);
                
                // Inject sentiment for Risk Service
                $article['sentiment'] = $sentimentData['sentiment'];
                $riskData = $this->tradeRiskService->analyze($article);
                
                // Trade Impact Analysis with failure boundary
                try {
                    $tradeImpactData = $this->tradeImpactAnalyzer->analyze($article);
                } catch (\Exception $e) {
                    Log::error("NewsSyncService: TradeImpactAnalyzer failed for article '{$title}' - " . $e->getMessage());
                    $tradeImpactData = [
                        'impact_score' => 0,
                        'impact_level' => 'Low',
                        'risk_direction' => 'Stable',
                        'trade_exposure_type' => 'Unknown',
                        'affected_countries' => [],
                        'affected_sectors' => [],
                        'impact_factors' => [],
                        'operational_impact' => null,
                        'confidence' => 0.50
                    ];
                }

                // URL Normalization for Database Persistence
                $finalUrl = $article['original_url'];
                if (method_exists($this->duplicateDetector, 'normalizeUrl')) {
                    $finalUrl = $this->duplicateDetector->normalizeUrl($article['original_url']);
                }

                // FINAL TRADE INTELLIGENCE QUALITY GATE
                // An article is rejected if it has 0 score, no factors, no sectors, and explicitly no operational event.
                $isQualityGatePassed = true;
                if ($tradeImpactData['impact_score'] == 0 
                    && empty($tradeImpactData['impact_factors']) 
                    && empty($tradeImpactData['affected_sectors'])
                    && isset($tradeImpactData['operational_impact'])
                    && stripos($tradeImpactData['operational_impact'], 'No direct operational') !== false
                ) {
                    $isQualityGatePassed = false;
                }

                if (!$isQualityGatePassed) {
                    $stats['quality_rejected'] = ($stats['quality_rejected'] ?? 0) + 1;
                    
                    Log::info("NewsSyncService: Quality Gate Rejected - [{$title}]");
                    
                    if ($this->command) {
                        $this->command->line("Article:\n\"{$title}\"");
                        $this->command->line('');
                        $this->command->line("API Query Origin: {$apiCategory}");
                        $this->command->line("Category: {$categoryData['category']}");
                        $this->command->line("Impact Score: {$tradeImpactData['impact_score']}");
                        $this->command->line("Impact Factors: " . (empty($tradeImpactData['impact_factors']) ? '[]' : implode(', ', $tradeImpactData['impact_factors'])));
                        $this->command->line("Affected Sectors: " . (empty($tradeImpactData['affected_sectors']) ? '[]' : implode(', ', $tradeImpactData['affected_sectors'])));
                        $this->command->line("Operational Impact: {$tradeImpactData['operational_impact']}");
                        $this->command->line("Quality Gate Result: REJECTED");
                        $this->command->line("Reason: No direct operational supply-chain or international-trade event detected.");
                        $this->command->line("------------------------------------------------------------");
                        $this->command->line('');
                    }
                    continue;
                }

                // Generate Intelligence Summary safely
                $intelligenceSummary = 'No significant trade impact detected.';
                try {
                    // Combine necessary data for the summary generator
                    $summaryPayload = array_merge($article, [
                        'category' => $categoryData['category'],
                        'trade_impact' => $tradeImpactData,
                    ]);
                    $intelligenceSummary = $this->summaryService->generate($summaryPayload) ?? 'No significant trade impact detected.';
                } catch (\Exception $e) {
                    Log::error("NewsSyncService: Failed to generate summary for '{$title}' - " . $e->getMessage());
                }

                // Map Country & Port Impact safely
                $mappedImpact = [
                    'mapped_countries' => [],
                    'mapped_ports' => [],
                    'regional_entities' => [],
                    'port_impact_type' => 'NONE',
                    'trade_exposure_type' => 'Unknown',
                    'mapping_confidence' => 0.00,
                ];
                try {
                    $mapperPayload = array_merge($article, [
                        'category' => $categoryData['category'],
                        'trade_impact' => $tradeImpactData,
                    ]);
                    $mappedImpact = array_merge($mappedImpact, $this->impactMapper->map($mapperPayload));
                } catch (\Exception $e) {
                    Log::error("NewsSyncService: Failed to map impact for '{$title}' - " . $e->getMessage());
                }

                // 4. Data Assembly
                $processedData = [
                    'title' => $article['title'],
                    'description' => $article['description'],
                    'content' => $article['content'],
                    'url' => $finalUrl,
                    'original_url' => $finalUrl,
                    'image_url' => $imageUrl,
                    'source' => $article['source'],
                    'author' => $article['author'],
                    'published_at' => $article['published_at'],
                    'country_id' => $countryData['country_id'],
                    'country_name' => $countryData['country_name'],
                    'category' => $categoryData['category'],
                    'sentiment' => $sentimentData['sentiment'],
                    'risk_level' => $riskData['risk_level'],
                    'risk_score' => $riskData['risk_score'],
                    'trade_impact' => $tradeImpactData,
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
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $batch[] = $processedData;

                // Track category distribution for saved articles
                $catName = $processedData['category'];
                $categoryCounts[$catName] = ($categoryCounts[$catName] ?? 0) + 1;

                if (count($batch) >= $batchSize) {
                    $this->repository->insert($batch);
                    foreach ($batch as $item) {
                        $this->cacheService->store($item);
                    }
                    $stats['saved'] += count($batch);
                    $batch = [];
                }

            } catch (\Exception $e) {
                Log::error("NewsSyncService: Failed to process article '{$article['title']}' - " . $e->getMessage());
                continue;
            }
        }

        // Insert remaining
        if (count($batch) > 0) {
            try {
                $this->repository->insert($batch);
                foreach ($batch as $item) {
                    $this->cacheService->store($item);
                }
                $stats['saved'] += count($batch);
            } catch (\Exception $e) {
                Log::error("NewsSyncService: Failed to process final batch - " . $e->getMessage());
            }
        }

        if ($this->command) {
            $this->command->info("--- QUERY ORIGIN DIAGNOSTICS ---");
            foreach ($queryStats as $cat => $qData) {
                $this->command->line("Category: {$cat}");
                $this->command->line("Query: {$qData['query']}");
                $this->command->line("Articles returned: {$qData['returned']}");
                $this->command->line("Unique articles: {$qData['unique']}");
                $this->command->line("Cross-query duplicates: {$qData['cross_query_dup']}");
                $this->command->line("");
            }
            
            $this->command->info("--- DUPLICATE BREAKDOWN ---");
            $this->command->line("Batch URL: {$dupStats['batch_url']}");
            $this->command->line("Batch Title: {$dupStats['batch_title']}");
            $this->command->line("Database URL: {$dupStats['db_url']}");
            $this->command->line("Database Title: {$dupStats['db_title']}");
            $this->command->line("Cross-query: {$dupStats['cross_query']}");
            $this->command->line("");

            $this->command->info("Summary");
            
            $apiEmpty = method_exists($this->apiService, 'getApiEmptyCount') ? $this->apiService->getApiEmptyCount() : 0;
            $apiFailures = method_exists($this->apiService, 'getProviderFailureCount') ? $this->apiService->getProviderFailureCount() : 0;
            
            $totalQueries = 9;
            $queriesWithResults = count($queryStats);
            $queriesZero = $totalQueries - $queriesWithResults;

            $this->command->line("Queries Executed: {$totalQueries}");
            $this->command->line("Queries Returning Results: {$queriesWithResults}");
            $this->command->line("Queries Returning Zero Results: {$queriesZero}");
            $this->command->line('');
            $this->command->line("Fetched: {$stats['fetched']}");
            $this->command->line("API Empty Queries: {$apiEmpty}");
            $this->command->line("Provider Failures: {$apiFailures}");
            $this->command->line("Duplicates: {$stats['duplicates']}");
            $this->command->line("    Batch Title: {$dupStats['batch_title']}");
            $this->command->line("    Cross Query: {$dupStats['cross_query']}");
            $this->command->line("    Database: " . ($dupStats['db_url'] + $dupStats['db_title']));
            $this->command->line("Rejected: {$stats['rejected']}");
            if (isset($stats['quality_rejected']) && $stats['quality_rejected'] > 0) {
                $this->command->line("Quality Gate Rejected: {$stats['quality_rejected']}");
            }
            $this->command->line("Saved: {$stats['saved']}");
            $this->command->line('');
            
            $this->command->info("Category Distribution (Saved):");
            $allowedCategories = [
                'Business', 'Energy', 'General', 'Geopolitics', 'Logistics', 
                'Manufacturing', 'Shipping', 'Technology', 'Trade'
            ];
            
            $finalCounts = [];
            foreach ($allowedCategories as $cat) {
                $finalCounts[$cat] = $categoryCounts[$cat] ?? 0;
            }
            
            arsort($finalCounts);
            
            foreach ($finalCounts as $cat => $count) {
                $this->command->line(ucfirst($cat) . ": {$count}");
            }
            $this->command->line('');
        }

        Log::info("NewsSyncService: Processing complete. Saved: {$stats['saved']}, Rejected: {$stats['rejected']}, Duplicates: {$stats['duplicates']}");
    }

    /**
     * Logs the completion of a sync operation.
     */
    protected function logCompletion(string $operation, int $totalFetched, float $startTime): void
    {
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        Log::info("NewsSyncService: Completed {$operation}. Fetched: {$totalFetched} articles. Duration: {$duration}ms.");
    }
}
