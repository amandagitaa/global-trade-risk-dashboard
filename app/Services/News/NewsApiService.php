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
        $categories = config('news.categories', [
            'business',
            'trade',
            'technology',
            'shipping',
            'logistics',
            'manufacturing',
            'energy',
            'geopolitics'
        ]);

        $allArticles = collect();

        foreach ($categories as $category) {
            $articles = $this->executeFetch(strtolower($category));
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

    /**
     * Executes the fetch with a cascading fallback strategy.
     */
    protected function executeFetch(string $category, string $countryCode = null): Collection
    {
        $providers = ['newsdata', 'gnews', 'rss'];
        $primary = $this->config['provider'] ?? 'newsdata';

        // Move primary provider to the top of the attempt list
        $providers = array_diff($providers, [$primary]);
        array_unshift($providers, $primary);

        foreach ($providers as $provider) {
            try {
                Log::info("NewsApiService: Attempting fetch using provider [{$provider}]");
                
                $startTime = microtime(true);
                $articles = $this->fetchFromProvider($provider, $category, $countryCode);
                $duration = round((microtime(true) - $startTime) * 1000, 2);
                
                if ($articles->isNotEmpty()) {
                    Log::info("NewsApiService: Successfully fetched {$articles->count()} articles from [{$provider}] in {$duration}ms");
                    return $articles;
                }
                
                Log::warning("NewsApiService: Provider [{$provider}] returned 0 articles.");

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error("NewsApiService: Connection timeout/error on [{$provider}] - " . $e->getMessage());
            } catch (\Exception $e) {
                Log::error("NewsApiService: API Error on [{$provider}] - " . $e->getMessage());
            }
            
            Log::warning("NewsApiService: Falling back to next provider...");
        }

        Log::error("NewsApiService: All providers failed to fetch news.");
        return collect([]);
    }

    protected function fetchFromProvider(string $provider, string $category, ?string $countryCode): Collection
    {
        return match ($provider) {
            'newsdata' => $this->newsDataIoStrategy($category, $countryCode),
            'gnews'    => $this->gNewsStrategy($category, $countryCode),
            'rss'      => $this->rssFallbackStrategy($category),
            default    => collect([]),
        };
    }

    protected function newsDataIoStrategy(string $category, ?string $countryCode): Collection
    {
        $apiKey = $this->config['api_key'];
        if (empty($apiKey)) {
            throw new \Exception("NewsData.io API Key is missing");
        }

        $url = "https://newsdata.io/api/1/news";
        $params = [
            'apikey' => $apiKey,
            'category' => $category,
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
                category: $item['category'][0] ?? $category,
                provider: 'newsdata'
            );
        }

        $limit = config('news.category_limit', 10);
        return collect($articles)->take($limit);
    }

    protected function gNewsStrategy(string $category, ?string $countryCode): Collection
    {
        $apiKey = $this->config['api_key'];
        if (empty($apiKey)) {
            throw new \Exception("GNews API Key is missing");
        }

        // GNews has specific allowed categories
        $allowedCategories = ['general', 'world', 'nation', 'business', 'technology', 'entertainment', 'sports', 'science', 'health'];
        $queryCategory = in_array($category, $allowedCategories) ? $category : 'business';

        $url = "https://gnews.io/api/v4/top-headlines";
        
        $limit = config('news.category_limit', 10);
        $params = [
            'apikey' => $apiKey,
            'category' => $queryCategory,
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
                category: $category,
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
