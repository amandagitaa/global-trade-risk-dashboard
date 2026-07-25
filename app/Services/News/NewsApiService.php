<?php

namespace App\Services\News;

use App\Contracts\NewsProviderInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NewsApiService implements NewsProviderInterface
{
    protected array $config;

    public function __construct()
    {
        $this->config = config('news');
    }

    public function fetch(): Collection
    {
        return $this->fetchLatest();
    }

    public function fetchLatest(): Collection
    {
        // Using explicit OR and exact phrases to avoid overlap
        $queries = [
            'Business' => '"procurement" OR "supplier network" OR "supply-chain operations" OR "sourcing"',
            'Energy' => '"oil tanker" OR "LNG shipping" OR "energy supply disruption" OR "oil supply chain"',
            'General' => '"global supply chain disruption" OR "supply chain resilience"',
            'Geopolitics' => '"shipping sanctions" OR "trade sanctions" OR "blockade trade" OR "shipping route conflict" OR "trade route disruption"',
            'Logistics' => '"freight logistics" OR "warehousing" OR "distribution" OR "rail freight" OR "trucking logistics"',
            'Manufacturing' => '"factory production" OR "supplier shortages" OR "manufacturing disruption" OR "industrial production"',
            'Shipping' => '"container shipping" OR "ocean freight" OR "vessel operations" OR "port congestion" OR "shipping routes"',
            'Technology' => '"semiconductor supply chain" OR "warehouse automation" OR "logistics technology" OR "supply-chain AI"',
            'Trade' => '"exports" OR "imports" OR "tariffs" OR "customs" OR "trade restrictions" OR "trade agreements"'
        ];

        $allArticles = collect();

        foreach ($queries as $category => $query) {
            $articles = $this->executeFetch($query);
            // Append the query category to the article array so SyncService knows origin query
            $articles = $articles->map(function ($article) use ($category, $query) {
                $article['api_category'] = $category;
                $article['api_query'] = $query;
                return $article;
            });
            $allArticles = $allArticles->merge($articles);
        }

        return $allArticles;
    }

    public function fetchBusiness(): Collection
    {
        return $this->executeFetch('business');
    }

    public function fetchTrade(): Collection
    {
        return $this->executeFetch('trade');
    }

    public function fetchEconomy(): Collection
    {
        return $this->executeFetch('economy');
    }

    public function fetchByCategory(string $category): Collection
    {
        return $this->executeFetch($category);
    }

    public function fetchByCountry(string $countryCode): Collection
    {
        return $this->executeFetch('business', $countryCode);
    }

    public function fetchEverything(): Collection
    {
        return $this->executeFetch('global');
    }

    public function healthCheck(): bool
    {
        // Simple check if API key is present and configured
        return !empty($this->config['api_key']);
    }

    protected int $apiEmptyCount = 0;
    protected int $providerFailureCount = 0;

    public function getApiEmptyCount(): int
    {
        return $this->apiEmptyCount;
    }

    public function getProviderFailureCount(): int
    {
        return $this->providerFailureCount;
    }

    public function resetCounters(): void
    {
        $this->apiEmptyCount = 0;
        $this->providerFailureCount = 0;
    }

    /**
     * Executes the fetch with a cascading fallback strategy.
     */
    protected function executeFetch(string $query, string $countryCode = null): Collection
    {
        $providers = ['newsdata', 'gnews'];
        $primary = $this->config['provider'] ?? 'newsdata';

        $providers = array_diff($providers, [$primary]);
        array_unshift($providers, $primary);

        foreach ($providers as $provider) {
            try {
                Log::info("NewsApiService: Attempting fetch using provider [{$provider}] for query [{$query}]");
                
                $startTime = microtime(true);
                $articles = $this->fetchFromProvider($provider, $query, $countryCode);
                $duration = round((microtime(true) - $startTime) * 1000, 2);
                
                if ($articles->isEmpty()) {
                    Log::info("NewsApiService: Provider [{$provider}] returned 0 articles for query [{$query}].");
                    $this->apiEmptyCount++;
                    return $articles; // Zero articles is a valid result. Do not fall back.
                }
                
                Log::info("NewsApiService: Successfully fetched {$articles->count()} articles from [{$provider}] in {$duration}ms");
                return $articles;

            } catch (\Exception $e) {
                $msg = $e->getMessage();
                Log::error("NewsApiService: Provider Failure on [{$provider}] - {$msg}");
                
                if (str_contains($msg, 'API Key is missing')) {
                    throw new \Exception("{$provider} API key is not configured. Supply-chain news synchronization cannot use the primary provider. Please configure NEWS_API_KEY in your .env file.");
                }
                
                $this->providerFailureCount++;
            }
            
            Log::warning("NewsApiService: Falling back to next provider due to failure...");
        }

        Log::error("NewsApiService: All providers failed to fetch news.");
        return collect([]);
    }

    protected function fetchFromProvider(string $provider, string $query, ?string $countryCode): Collection
    {
        return match ($provider) {
            'newsdata' => $this->newsDataIoStrategy($query, $countryCode),
            'gnews'    => $this->gNewsStrategy($query, $countryCode),
            default    => collect([]),
        };
    }

    protected function newsDataIoStrategy(string $query, ?string $countryCode): Collection
    {
        $apiKey = $this->config['api_key'];
        if (empty($apiKey)) {
            throw new \Exception("NewsData.io API Key is missing");
        }

        $url = "https://newsdata.io/api/1/news";
        
        $params = [
            'apikey' => $apiKey,
            'q' => $query,
            'language' => $this->config['language'],
        ];

        if ($countryCode) {
            $params['country'] = strtolower($countryCode);
        }

        $response = Http::timeout($this->config['timeout'])->get($url, $params);

        if ($response->status() === 429) {
            throw new \Exception("Rate Limit Exceeded (429)");
        }
        
        if ($response->status() === 403) {
            throw new \Exception("Forbidden (403) - Invalid API Key");
        }

        if (!$response->successful()) {
            throw new \Exception("HTTP Error: " . $response->status());
        }

        $data = $response->json();
        $articles = [];

        foreach ($data['results'] ?? [] as $item) {
            $articles[] = $this->formatArticle(
                title: $item['title'] ?? '',
                description: $item['description'] ?? '',
                content: $item['content'] ?? '',
                url: $item['link'] ?? '',
                imageUrl: $item['image_url'] ?? null,
                source: $item['source_id'] ?? 'NewsData',
                author: $item['creator'][0] ?? 'Editorial',
                publishedAt: $item['pubDate'] ?? null,
                language: $item['language'] ?? $this->config['language'],
                countryCode: $item['country'][0] ?? 'Global',
                // Category is resolved later in NewsSyncService
                category: 'unknown',
                provider: 'newsdata'
            );
        }

        $limit = config('news.category_limit', 10);
        return collect($articles)->take($limit);
    }

    protected function gNewsStrategy(string $query, ?string $countryCode): Collection
    {
        $apiKey = $this->config['api_key'];
        if (empty($apiKey)) {
            throw new \Exception("GNews API Key is missing");
        }

        $url = "https://gnews.io/api/v4/search";
        
        $limit = config('news.category_limit', 10);
        $params = [
            'apikey' => $apiKey,
            'q' => $query,
            'lang' => $this->config['language'],
            'max' => min($limit, 100),
        ];

        if ($countryCode) {
            $params['country'] = strtolower($countryCode);
        }

        $response = Http::timeout($this->config['timeout'])->get($url, $params);

        if ($response->status() === 429) {
            throw new \Exception("Rate Limit Exceeded (429)");
        }
        
        if ($response->status() === 403) {
            throw new \Exception("Forbidden (403) - Invalid API Key");
        }

        if (!$response->successful()) {
            throw new \Exception("HTTP Error: " . $response->status());
        }

        $data = $response->json();
        $articles = [];

        foreach ($data['articles'] ?? [] as $item) {
            $articles[] = $this->formatArticle(
                title: $item['title'] ?? '',
                description: $item['description'] ?? '',
                content: $item['content'] ?? '',
                url: $item['url'] ?? '',
                imageUrl: $item['image'] ?? null,
                source: $item['source']['name'] ?? 'GNews',
                author: 'Editorial',
                publishedAt: $item['publishedAt'] ?? null,
                language: $this->config['language'],
                countryCode: $countryCode ?? 'Global',
                category: 'unknown',
                provider: 'gnews'
            );
        }

        return collect($articles);
    }

    protected function rssFallbackStrategy(string $category): Collection
    {
        $feeds = [
            'BBC Business' => 'http://feeds.bbci.co.uk/news/business/rss.xml',
            'Yahoo Finance' => 'https://finance.yahoo.com/news/rssindex',
            'Maritime Executive' => 'https://www.maritime-executive.com/api/rss'
        ];

        $articles = [];

        foreach ($feeds as $source => $url) {
            try {
                $response = Http::timeout($this->config['timeout'])->get($url);
                if ($response->successful()) {
                    $xml = @simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);
                    if ($xml && isset($xml->channel->item)) {
                        foreach ($xml->channel->item as $item) {
                            $title = strip_tags((string) $item->title);
                            $description = strip_tags((string) $item->description);
                            $link = (string) $item->link;
                            $pubDate = (string) $item->pubDate;
                            
                            $title = trim($title);
                            $description = Str::limit(trim($description), 200);
                            
                            if (!empty($title) && !empty($link)) {
                                $articles[] = $this->formatArticle(
                                    title: $title,
                                    description: $description ?: $title,
                                    content: '',
                                    url: $link,
                                    imageUrl: null,
                                    source: $source,
                                    author: 'Editorial',
                                    publishedAt: $pubDate,
                                    language: 'en',
                                    countryCode: 'Global',
                                    category: $category,
                                    provider: 'rss'
                                );
                            }

                            $limit = config('news.category_limit', 10);
                            if (count($articles) >= $limit) {
                                break 2;
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("NewsApiService: RSS fetch error for {$source} - " . $e->getMessage());
            }
        }

        return collect($articles);
    }

    /**
     * Standardizes the article format across all providers.
     */
    protected function formatArticle(
        string $title,
        string $description,
        string $content,
        string $url,
        ?string $imageUrl,
        string $source,
        string $author,
        ?string $publishedAt,
        string $language,
        string $countryCode,
        string $category,
        string $provider
    ): array {
        $formatted = [
            'title' => trim($title),
            'description' => trim($description),
            'content' => trim($content),
            'original_url' => trim($url),
            'image_url' => $imageUrl,
            'source' => trim($source),
            'author' => trim($author),
            'published_at' => $publishedAt ? Carbon::parse($publishedAt)->format('Y-m-d H:i:s') : Carbon::now()->format('Y-m-d H:i:s'),
            'language' => strtolower($language),
            'country_code' => strtolower($countryCode),
            'category' => strtolower($category),
            'provider' => strtolower($provider),
        ];

        Log::info('NEWS TRACE 1', [
            'provider' => $provider,
            'title' => $formatted['title'],
            'url' => $url,
            'original_url' => $formatted['original_url'],
        ]);

        return $formatted;
    }
}
