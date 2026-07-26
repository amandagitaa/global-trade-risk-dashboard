<?php

namespace App\Services\News;

use App\Contracts\NewsRepositoryInterface;
use Illuminate\Support\Facades\Log;

class DuplicateDetector
{
    protected array $seenUrls = [];
    protected array $seenTitles = [];

    public function __construct(
        protected NewsRepositoryInterface $repository
    ) {}

    /**
     * Resets runtime cache for a new sync run
     */
    public function reset(): void
    {
        $this->seenUrls = [];
        $this->seenTitles = [];
    }

    /**
     * Normalizes a title for duplicate comparison.
     */
    protected function normalizeTitle(string $title, ?string $source = null): string
    {
        // Lowercase
        $title = mb_strtolower($title);
        
        // Remove known source suffix if provided
        if (!empty($source)) {
            $sourceLower = mb_strtolower($source);
            $title = preg_replace('/\s*[-|]\s*' . preg_quote($sourceLower, '/') . '\s*$/i', '', $title);
        }
        
        // Remove common trailing suffixes (e.g., "- report", "| source")
        $title = preg_replace('/(\s*[-|]\s*(report|reuters|ap|bloomberg|cnbc|cnn|bbc|update|video|audio|opinion|editorial|the hill)).*$/i', '', $title);
        
        // Remove harmless punctuation
        $title = preg_replace('/[^\p{L}\p{N}\s]/u', '', $title);
        
        // Collapse multiple spaces
        $title = preg_replace('/\s+/', ' ', $title);
        
        // Trim
        return trim($title);
    }

    /**
     * Normalizes a URL by stripping common tracking parameters.
     */
    public function normalizeUrl(string $url): string
    {
        $parsed = parse_url($url);
        if (!$parsed || empty($parsed['host'])) {
            return $url;
        }

        $normalized = ($parsed['scheme'] ?? 'https') . '://' . strtolower($parsed['host']);
        if (!empty($parsed['port'])) {
            $normalized .= ':' . $parsed['port'];
        }
        if (!empty($parsed['path'])) {
            $normalized .= $parsed['path'];
        }
        
        if (!empty($parsed['query'])) {
            parse_str($parsed['query'], $queryParams);
            
            // Strip tracking parameters
            $trackingParams = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'fbclid', 'gclid'];
            foreach ($trackingParams as $param) {
                unset($queryParams[$param]);
            }
            
            if (!empty($queryParams)) {
                $normalized .= '?' . http_build_query($queryParams);
            }
        }

        return $normalized;
    }

    /**
     * Detects if an article already exists in the database or the current batch.
     *
     * @param array|string $articleData Data or URL to check.
     * @return array
     */
    public function isDuplicate($articleData): array
    {
        // Handle backward compatibility if only URL string is passed
        if (is_string($articleData)) {
            $articleData = ['original_url' => $articleData];
        }

        $url = $articleData['original_url'] ?? null;
        $title = $articleData['title'] ?? null;
        $publishedAt = $articleData['published_at'] ?? null;
        $provider = $articleData['provider'] ?? null;
        $apiCategory = $articleData['api_category'] ?? 'unknown';
        
        $matchFound = false;
        $matchedField = '';
        $reason = '';
        $isCrossQuery = false;

        $sourceStr = $articleData['source'] ?? $provider;
        $normalizedTitle = !empty($title) ? $this->normalizeTitle($title, $sourceStr) : null;
        $normalizedUrl = !empty($url) ? $this->normalizeUrl($url) : null;

        // 1. Check Runtime Cache (Current Run)
        if (!empty($normalizedUrl) && isset($this->seenUrls[$normalizedUrl])) {
            $matchFound = true;
            $matchedField = 'runtime_url';
            if ($this->seenUrls[$normalizedUrl] !== $apiCategory) {
                $isCrossQuery = true;
                $reason = "Cross-query duplicate (URL) from {$this->seenUrls[$normalizedUrl]}";
            } else {
                $reason = "Batch URL Duplicate";
            }
        } elseif (!empty($normalizedTitle) && isset($this->seenTitles[$normalizedTitle])) {
            $matchFound = true;
            $matchedField = 'runtime_title';
            if ($this->seenTitles[$normalizedTitle] !== $apiCategory) {
                $isCrossQuery = true;
                $reason = "Cross-query duplicate (Title) from {$this->seenTitles[$normalizedTitle]}";
            } else {
                $reason = "Batch Title Duplicate";
            }
        }

        // 2. Check Database
        if (!$matchFound && !empty($normalizedUrl)) {
            if ($this->repository->exists('original_url', $normalizedUrl) || $this->repository->exists('original_url', $url)) {
                $matchFound = true;
                $matchedField = 'original_url';
                $reason = "Database URL Duplicate";
            }
        }

        if (!$matchFound && !empty($normalizedTitle) && !empty($title)) {
            // Because findByTitle is exact match in repository, we probably should do something else,
            // but for now, we will leave findByTitle as is for backward compatibility or we can check normalized
            // if we had a normalized column. The runtime cache catches normalized title duplicates.
            $existing = $this->repository->findByTitle($title);
            
            if ($existing) {
                $matchFound = true;
                $matchedField = 'title';
                $reason = "Database Title Duplicate";
            }
        }

        if ($matchFound) {
            $this->logDuplicate($title ?? $url, $matchedField);
            
            return [
                'is_duplicate' => true,
                'duplicate_reason' => $reason,
                'matched_field' => $matchedField,
                'is_cross_query' => $isCrossQuery,
            ];
        }

        // Track for future checks in the same run
        if (!empty($normalizedUrl)) {
            $this->seenUrls[$normalizedUrl] = $apiCategory;
        }
        if (!empty($normalizedTitle)) {
            $this->seenTitles[$normalizedTitle] = $apiCategory;
        }

        return [
            'is_duplicate' => false,
            'duplicate_reason' => null,
            'matched_field' => null,
        ];
    }

    /**
     * Logs the duplicate detection.
     */
    protected function logDuplicate(string $identifier, string $field): void
    {
        Log::info("DuplicateDetector: Skipped article [{$identifier}] - Matched field: {$field}");
    }
}
