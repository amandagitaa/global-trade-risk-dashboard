<?php

namespace App\Services;

use App\Models\Country;
use App\Models\NewsCache;
use App\Models\PositiveWord;
use App\Models\NegativeWord;
use Illuminate\Support\Facades\Http;

class NewsService
{
    public function fetchAndStore(Country $country): bool
    {
        $countryName = $country->country_name;

        $response = Http::get('https://gnews.io/api/v4/search', [
            'q' => $countryName,
            'lang' => 'en',
            'max' => 5,
            'apikey' => env('GNEWS_API_KEY')
        ]);

        if (!$response->successful()) {
            return false;
        }

        $data = $response->json();

        if (!isset($data['articles'])) {
            return false;
        }

        $positiveWords = PositiveWord::all();
        $negativeWords = NegativeWord::all();

        foreach ($data['articles'] as $article) {
            $title = $article['title'] ?? '';
            $description = $article['description'] ?? '';
            $content = strtolower($title . ' ' . $description);

            $positiveScore = 0;
            $negativeScore = 0;

            foreach ($positiveWords as $word) {
                if (str_contains($content, strtolower($word->word))) {
                    $positiveScore += $word->weight;
                }
            }

            foreach ($negativeWords as $word) {
                if (str_contains($content, strtolower($word->word))) {
                    $negativeScore += $word->weight;
                }
            }

            $sentiment = 'neutral';

            if ($negativeScore > $positiveScore) {
                $sentiment = 'negative';
            } elseif ($positiveScore > $negativeScore) {
                $sentiment = 'positive';
            }

            $category = $this->detectCategory($content);

            NewsCache::create([
                'country_id' => $country->id,
                'title' => $title,
                'description' => $description,
                'source' => $article['source']['name'] ?? 'Unknown',
                'url' => $article['url'] ?? null,
                'category' => $category,
                'positive_score' => $positiveScore,
                'negative_score' => $negativeScore,
                'sentiment' => $sentiment,
                'published_at' => now()
            ]);
        }

        return true;
    }

    private function detectCategory(string $content): string
    {
        if (
            str_contains($content, 'shipping') ||
            str_contains($content, 'port')
        ) {
            return 'shipping';
        }

        if (
            str_contains($content, 'trade') ||
            str_contains($content, 'export') ||
            str_contains($content, 'import')
        ) {
            return 'trade';
        }

        if (
            str_contains($content, 'war') ||
            str_contains($content, 'conflict') ||
            str_contains($content, 'sanction')
        ) {
            return 'geopolitics';
        }

        return 'economy';
    }
}