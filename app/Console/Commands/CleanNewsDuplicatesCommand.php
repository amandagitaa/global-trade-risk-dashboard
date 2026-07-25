<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsCache;
use App\Services\News\DuplicateDetector;

class CleanNewsDuplicatesCommand extends Command
{
    protected $signature = 'news:clean-duplicates';
    protected $description = 'Clean up legacy duplicate news articles';

    public function handle(DuplicateDetector $detector)
    {
        $this->info("Starting legacy duplicate cleanup...");

        $allArticles = NewsCache::withoutGlobalScopes()->get();
        $this->info("Total articles in database: " . $allArticles->count());

        $urlGroups = [];
        $titleGroups = [];

        foreach ($allArticles as $article) {
            $url = $article->original_url ?: $article->url;
            $title = $article->title;

            $normalizedUrl = $detector->normalizeUrl((string)$url);
            
            // Replicate normalizeTitle logic here since it's protected in DuplicateDetector
            $normalizedTitle = mb_strtolower((string)$title);
            $normalizedTitle = preg_replace('/(\s*[-|]\s*(report|reuters|ap|bloomberg|cnbc|cnn|bbc|update|video|audio|opinion|editorial)).*$/i', '', $normalizedTitle);
            $normalizedTitle = preg_replace('/[^\p{L}\p{N}\s]/u', '', $normalizedTitle);
            $normalizedTitle = preg_replace('/\s+/', ' ', $normalizedTitle);
            $normalizedTitle = trim($normalizedTitle);

            if ($normalizedUrl) {
                $urlGroups[$normalizedUrl][] = $article;
            } else if ($normalizedTitle) {
                $titleGroups[$normalizedTitle][] = $article;
            }
        }

        $deletedCount = 0;

        foreach ($urlGroups as $url => $group) {
            if (count($group) > 1) {
                $this->info("Found duplicate URL group: $url (Count: " . count($group) . ")");
                $deletedCount += $this->processGroup($group);
            }
        }

        // Title groups might overlap with URL groups, so we re-fetch to ensure we don't process deleted records
        // For our specific dataset, URL groups caught all of them, but we'll do titles just in case.
        $allArticlesAfter = NewsCache::withoutGlobalScopes()->get();
        $titleGroupsAfter = [];
        foreach ($allArticlesAfter as $article) {
            $title = $article->title;
            $normalizedTitle = mb_strtolower((string)$title);
            $normalizedTitle = preg_replace('/(\s*[-|]\s*(report|reuters|ap|bloomberg|cnbc|cnn|bbc|update|video|audio|opinion|editorial)).*$/i', '', $normalizedTitle);
            $normalizedTitle = preg_replace('/[^\p{L}\p{N}\s]/u', '', $normalizedTitle);
            $normalizedTitle = preg_replace('/\s+/', ' ', $normalizedTitle);
            $normalizedTitle = trim($normalizedTitle);

            if ($normalizedTitle) {
                $titleGroupsAfter[$normalizedTitle][] = $article;
            }
        }

        foreach ($titleGroupsAfter as $title => $group) {
            if (count($group) > 1) {
                $this->info("Found duplicate Title group: $title (Count: " . count($group) . ")");
                $deletedCount += $this->processGroup($group);
            }
        }

        $this->info("Cleanup complete. Removed $deletedCount legacy duplicates.");
        $this->info("Final article count: " . NewsCache::withoutGlobalScopes()->count());
    }

    protected function processGroup(array $group): int
    {
        // Keep the "best" record.
        // Preference order:
        // 1. Has image > No image
        // 2. Longer description > Shorter description
        // 3. Newest published_at > older

        usort($group, function ($a, $b) {
            $aImage = !empty($a->image_url) ? 1 : 0;
            $bImage = !empty($b->image_url) ? 1 : 0;
            if ($aImage !== $bImage) {
                return $bImage <=> $aImage;
            }

            $aDesc = strlen($a->description ?? '');
            $bDesc = strlen($b->description ?? '');
            if ($aDesc !== $bDesc) {
                return $bDesc <=> $aDesc;
            }

            return $b->published_at <=> $a->published_at;
        });

        $best = array_shift($group);
        $this->info("  - KEEPING ID: {$best->id} | {$best->title}");

        $deletedCount = 0;
        foreach ($group as $duplicate) {
            $this->warn("  - DELETING ID: {$duplicate->id}");
            $duplicate->delete();
            $deletedCount++;
        }

        return $deletedCount;
    }
}
