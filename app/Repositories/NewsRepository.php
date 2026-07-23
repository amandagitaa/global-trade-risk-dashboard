<?php

namespace App\Repositories;

use App\Contracts\NewsRepositoryInterface;
use App\Models\NewsCache;
use Illuminate\Support\Facades\Log;

class NewsRepository implements NewsRepositoryInterface
{
    /**
     * Saves a new news article into the database.
     */
    public function save(array $data)
    {
        try {
            $news = NewsCache::create($data);
            Log::info("NewsRepository: Saved new article ID [{$news->id}]");
            return $news;
        } catch (\Exception $e) {
            Log::error("NewsRepository: Failed to save article - " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Updates an existing news article.
     */
    public function update($id, array $data)
    {
        $news = $this->find($id);
        if ($news) {
            $news->update($data);
            return $news;
        }
        return null;
    }

    /**
     * Deletes a news article.
     */
    public function delete($id)
    {
        $news = $this->find($id);
        if ($news) {
            return $news->delete();
        }
        return false;
    }

    /**
     * Finds a news article by ID.
     */
    public function find($id)
    {
        return NewsCache::find($id);
    }

    /**
     * Finds a news article by its original URL.
     */
    public function findByUrl(string $url)
    {
        return NewsCache::where('original_url', $url)->first();
    }

    /**
     * Finds a news article by its title.
     */
    public function findByTitle(string $title)
    {
        return NewsCache::where('title', $title)->first();
    }

    /**
     * Batch inserts multiple news articles into the database.
     */
    public function insert(array $data): bool
    {
        try {
            NewsCache::unguard();
            foreach ($data as $item) {
                $originalUrl = $item['original_url'] ?? null;

                // Validate original_url: if empty or starts with /news/, consider it invalid
                if (empty($originalUrl) || str_starts_with($originalUrl, '/news/')) {
                    $originalUrl = null;
                    $item['original_url'] = null;
                }

                if ($originalUrl !== null) {
                    // Update or create using ONLY original_url as identity key.
                    // Using withoutGlobalScopes to ensure we find it even if it's not 'Published'.
                    NewsCache::withoutGlobalScopes()->updateOrCreate(
                        ['original_url' => $originalUrl],
                        $item
                    );
                } else {
                    // Legacy data or invalid url. Insert as new record. Do NOT merge by title.
                    NewsCache::create($item);
                }
            }
            NewsCache::reguard();
            
            return true;
        } catch (\Exception $e) {
            NewsCache::reguard();
            Log::error("NewsRepository: Failed batch insert - " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieves the latest news articles.
     */
    public function latest(int $limit = 10)
    {
        return NewsCache::with('country')->latest('published_at')->limit($limit)->get();
    }

    /**
     * Searches for news articles by keyword.
     */
    public function search(string $keyword)
    {
        return NewsCache::with('country')
            ->where('title', 'like', "%{$keyword}%")
            ->orWhere('description', 'like', "%{$keyword}%")
            ->orWhere('content', 'like', "%{$keyword}%")
            ->get();
    }

    /**
     * Paginates news articles.
     */
    public function paginate(int $perPage = 15)
    {
        return NewsCache::with('country')->latest('published_at')->paginate($perPage);
    }

    /**
     * Checks if a record exists by field and value.
     */
    public function exists(string $field, $value): bool
    {
        return NewsCache::where($field, $value)->exists();
    }
}
