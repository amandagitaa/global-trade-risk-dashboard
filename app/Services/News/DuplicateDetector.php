<?php

namespace App\Services\News;

use App\Contracts\NewsRepositoryInterface;
use Illuminate\Support\Facades\Log;

class DuplicateDetector
{
    public function __construct(
        protected NewsRepositoryInterface $repository
    ) {}

    /**
     * Detects if an article already exists in the database.
     * Returns an array detailing the duplicate status.
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
        
        $matchFound = false;
        $matchedField = '';
        $reason = '';

        if (!empty($url)) {
            if ($this->repository->exists('original_url', $url)) {
                $matchFound = true;
                $matchedField = 'original_url';
                $reason = "Exact match on original_url";
            }
        }

        if (!$matchFound && !empty($title)) {
            $existing = $this->repository->findByTitle($title);
            
            // If title matches, verify if it's the same provider or same published date to confirm duplicate
            if ($existing) {
                if ($provider && $existing->source === $provider) {
                    $matchFound = true;
                    $matchedField = 'title + provider';
                    $reason = "Title matched from the same provider";
                } elseif ($publishedAt && $existing->published_at && $existing->published_at->format('Y-m-d') === date('Y-m-d', strtotime($publishedAt))) {
                    $matchFound = true;
                    $matchedField = 'title + published_at';
                    $reason = "Title matched on the same publication date";
                } else {
                    // Exact same title across providers is considered duplicate
                    $matchFound = true;
                    $matchedField = 'title';
                    $reason = "Exact title match";
                }
            }
        }

        if ($matchFound) {
            $this->logDuplicate($title ?? $url, $matchedField);
            
            return [
                'is_duplicate' => true,
                'duplicate_reason' => $reason,
                'matched_field' => $matchedField,
            ];
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
