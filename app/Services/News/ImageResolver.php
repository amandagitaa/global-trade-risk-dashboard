<?php

namespace App\Services\News;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImageResolver
{
    /**
     * Resolves the best image for the article.
     * Returns a valid image URL string.
     *
     * @param array $article
     * @return string
     */
    public function resolve(array $article): string
    {
        $originalUrl = $article['original_url'] ?? '';

        // 1. Priority 1: Image URL from API Provider
        if (!empty($article['image_url']) && $this->isValidImage($article['image_url'])) {
            $this->logResolution($article['image_url'], 'Provider API');
            return $article['image_url'];
        }

        // If no API image and no valid original URL to scrape from, return empty string
        if (empty($originalUrl) || !filter_var($originalUrl, FILTER_VALIDATE_URL)) {
            $this->logResolution('none', 'No valid URL to scrape');
            return '';
        }

        // Try scraping meta tags from the original article URL
        $scrapedImage = $this->scrapeMetaImages($originalUrl);

        // 2. Priority 2: og:image
        if (!empty($scrapedImage['og'])) {
            $this->logResolution($scrapedImage['og'], 'og:image');
            return $scrapedImage['og'];
        }

        // 3. Priority 3: twitter:image
        if (!empty($scrapedImage['twitter'])) {
            $this->logResolution($scrapedImage['twitter'], 'twitter:image');
            return $scrapedImage['twitter'];
        }
        
        // 4. Priority 4: thumbnail meta
        if (!empty($scrapedImage['thumbnail'])) {
            $this->logResolution($scrapedImage['thumbnail'], 'thumbnail meta');
            return $scrapedImage['thumbnail'];
        }

        // 5. Priority 5: No image found
        $this->logResolution('none', 'No Image Found');
        
        return '';
    }

    /**
     * Attempts to scrape image meta tags from the given URL.
     */
    protected function scrapeMetaImages(string $url): array
    {
        $images = ['og' => null, 'twitter' => null, 'thumbnail' => null];

        try {
            // Short timeout to prevent sync process from hanging
            $response = Http::timeout(3)->get($url);

            if ($response->successful()) {
                $html = $response->body();

                // Extract og:image
                if (preg_match('/<meta[^>]*property=[\'"]og:image[\'"][^>]*content=[\'"]([^\'"]+)[\'"][^>]*>/i', $html, $matches) || 
                    preg_match('/<meta[^>]*content=[\'"]([^\'"]+)[\'"][^>]*property=[\'"]og:image[\'"][^>]*>/i', $html, $matches)) {
                    if ($this->isValidImage($matches[1])) {
                        $images['og'] = $matches[1];
                    }
                }

                // Extract twitter:image
                if (preg_match('/<meta[^>]*name=[\'"]twitter:image[\'"][^>]*content=[\'"]([^\'"]+)[\'"][^>]*>/i', $html, $matches) ||
                    preg_match('/<meta[^>]*content=[\'"]([^\'"]+)[\'"][^>]*name=[\'"]twitter:image[\'"][^>]*>/i', $html, $matches)) {
                    if ($this->isValidImage($matches[1])) {
                        $images['twitter'] = $matches[1];
                    }
                }

                // Extract thumbnail
                if (preg_match('/<meta[^>]*name=[\'"]thumbnail[\'"][^>]*content=[\'"]([^\'"]+)[\'"][^>]*>/i', $html, $matches) ||
                    preg_match('/<meta[^>]*content=[\'"]([^\'"]+)[\'"][^>]*name=[\'"]thumbnail[\'"][^>]*>/i', $html, $matches)) {
                    if ($this->isValidImage($matches[1])) {
                        $images['thumbnail'] = $matches[1];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning("ImageResolver: Failed to scrape URL [{$url}] - " . $e->getMessage());
        }

        return $images;
    }

    /**
     * Validates if a URL is structurally a valid image format.
     */
    protected function isValidImage(string $url): bool
    {
        $url = trim($url);
        
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Must be http/https
        if (!preg_match('/^https?:\/\//i', $url)) {
            return false;
        }

        // Avoid base64 data URIs or suspicious strings
        if (strlen($url) > 2048) {
            return false;
        }

        // Optional: Ensure it has an image extension (often APIs return images without extensions though, so we don't strictly enforce unless necessary)
        // For standard robustness, we assume HTTP validation + not empty is sufficient, 
        // but we can reject obvious non-image extensions if we want.
        $invalidExtensions = ['mp4', 'pdf', 'zip', 'exe', 'doc'];
        $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
        
        if (in_array($ext, $invalidExtensions)) {
            return false;
        }

        return true;
    }

    /**
     * Provides a default fallback image based on the category.
     * (Deprecated: System no longer uses fallback dummy images)
     */
    protected function getFallbackImage(string $category): string
    {
        return '';
    }

    /**
     * Logs the image resolution method.
     */
    protected function logResolution(string $resolvedUrl, string $method): void
    {
        Log::info("ImageResolver: Resolved image via [{$method}] -> {$resolvedUrl}");
    }
}
