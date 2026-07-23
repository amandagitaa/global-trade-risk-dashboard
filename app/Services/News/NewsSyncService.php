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
        protected CacheService $cacheService
    ) {}

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
        $successCount = 0;
        $failCount = 0;
        $batch = [];
        $batchSize = config('news.batch_size', 50);

        foreach ($articles as $article) {
            try {
                Log::info('NEWS TRACE 2', [
                    'title' => $article['title'] ?? null,
                    'article_original_url' => $article['original_url'] ?? null,
                ]);

                // 1. Validation & Duplicate Check
                $duplicateStatus = $this->duplicateDetector->isDuplicate($article);
                if ($duplicateStatus['is_duplicate'] ?? false) {
                    continue;
                }

                if ($duplicateStatus['is_duplicate']) {
    Log::info('NEWS DUPLICATE', [
        'title' => $article['title'],
        'reason' => $duplicateStatus['duplicate_reason'],
        'matched_field' => $duplicateStatus['matched_field'],
    ]);
}

                // 2. Resolvers
                $countryData = $this->countryResolver->resolve($article);
                $categoryData = $this->categoryResolver->resolve($article);
                $imageUrl = $this->imageResolver->resolve($article);

                // Inject resolved category into article for Sentiment & Risk
                $article['category'] = $categoryData['category'];

                // 3. Analysis
                $sentimentData = $this->sentimentService->analyze($article);
                
                // Inject sentiment for Risk Service
                $article['sentiment'] = $sentimentData['sentiment'];
                $riskData = $this->tradeRiskService->analyze($article);

                // 4. Data Assembly (Mapping to Database Columns)
                $processedData = [
                    'title' => $article['title'],
                    'description' => $article['description'],
                    'content' => $article['content'],
                    'original_url' => $article['original_url'],
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
                    
                    /* 
                     * LEGACY/UNSYNCED FIELDS REMOVED TO PREVENT SQLSTATE[42S22] Unknown column error:
                     * - 'language': Not present in news_cache schema.
                     * - 'sentiment_score': Not mapped. DB schema uses positive_score & negative_score 
                     *   which represent two independent scores, whereas sentiment_score is a single metric. 
                     *   Mapping them is a business logic violation.
                     * - 'is_dummy': Not present in news_cache schema. (Not to be confused with status/verification).
                     * 
                     * RETAINED:
                     * - 'risk_level' and 'risk_score' because both columns exist in news_cache schema.
                     */
                     
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                Log::info('NEWS TRACE 3', [
                    'title' => $processedData['title'] ?? null,
                    'processed_original_url' => $processedData['original_url'] ?? null,
                ]);

                $batch[] = $processedData;

                if (count($batch) >= $batchSize) {
                    $this->repository->insert($batch);
                    foreach ($batch as $item) {
                        $this->cacheService->store($item);
                    }
                    $successCount += count($batch);
                    $batch = [];
                }

            } catch (\Exception $e) {
                Log::info('NEWS TRACE 8', [
                    'title' => $article['title'] ?? null,
                    'original_url' => $article['original_url'] ?? null,
                    'payload' => $article,
                    'stacktrace' => $e->getTraceAsString(),
                ]);
                $failCount++;
                Log::error("NewsSyncService: Failed to process article '{$article['title']}' - " . $e->getMessage());
                // Continue to the next article without breaking the loop
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
                $successCount += count($batch);
            } catch (\Exception $e) {
                $failCount += count($batch);
                Log::error("NewsSyncService: Failed to process final batch - " . $e->getMessage());
            }
        }

        Log::info("NewsSyncService: Processing complete. Success: {$successCount}, Failed: {$failCount}");
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
