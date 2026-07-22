<?php

namespace App\Services\News;

use App\Models\Country;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CountryResolver
{
    /**
     * Resolves the country based on the article data.
     * Returns an array containing resolution details.
     *
     * @param array $article
     * @return array
     */
    public function resolve(array $article): array
    {
        $countries = $this->getCachedCountries();
        $aliases = $this->getAliases();

        // 1. Priority 1: country_code from API
        if (!empty($article['country_code']) && strtolower($article['country_code']) !== 'global') {
            $matched = $countries->first(function ($c) use ($article) {
                return strtolower($c->country_code) === strtolower($article['country_code']);
            });

            if ($matched) {
                $this->logResolution($matched->country_name, 'API Country Code', 'HIGH');
                return $this->formatResult($matched, 'HIGH', 'API Country Code');
            }
        }

        // Combine text for searching (Title > Description > Content)
        $title = $article['title'] ?? '';
        $description = $article['description'] ?? '';
        $content = $article['content'] ?? '';

        // 2. Priority 3: Nama negara pada title
        if (!empty($title)) {
            $match = $this->searchInText($title, $countries, $aliases);
            if ($match) {
                $this->logResolution($match['country']->country_name, 'Title Match', 'HIGH');
                return $this->formatResult($match['country'], 'HIGH', 'Title Match');
            }
        }

        // 3. Priority 4: Nama negara pada description
        if (!empty($description)) {
            $match = $this->searchInText($description, $countries, $aliases);
            if ($match) {
                $this->logResolution($match['country']->country_name, 'Description Match', 'MEDIUM');
                return $this->formatResult($match['country'], 'MEDIUM', 'Description Match');
            }
        }

        // 4. Priority 5: Nama negara pada content
        if (!empty($content)) {
            $match = $this->searchInText($content, $countries, $aliases);
            if ($match) {
                $this->logResolution($match['country']->country_name, 'Content Match', 'LOW');
                return $this->formatResult($match['country'], 'LOW', 'Content Match');
            }
        }

        // 5. Priority 7: Global
        $this->logResolution('Global', 'Fallback', 'LOW');
        return [
            'country_id' => null,
            'country_name' => 'Global',
            'country_code' => null,
            'confidence_score' => 'LOW',
            'resolution_method' => 'Fallback'
        ];
    }

    /**
     * Searches for a country name or alias in a given text.
     */
    protected function searchInText(string $text, $countries, array $aliases): ?array
    {
        // Add word boundaries to avoid partial matches (e.g., "Oman" in "Roman")
        foreach ($countries as $country) {
            if (preg_match('/\b' . preg_quote($country->country_name, '/') . '\b/i', $text)) {
                return ['country' => $country];
            }
        }

        foreach ($aliases as $alias => $realName) {
            if (preg_match('/\b' . preg_quote($alias, '/') . '\b/i', $text)) {
                $matchedCountry = $countries->first(function ($c) use ($realName) {
                    return strtolower($c->country_name) === strtolower($realName);
                });

                if ($matchedCountry) {
                    return ['country' => $matchedCountry];
                }
            }
        }

        return null;
    }

    /**
     * Retrieves all countries from cache to prevent direct/repeated DB queries.
     */
    protected function getCachedCountries()
    {
        return Cache::remember('resolver_all_countries', 3600, function () {
            return Country::all();
        });
    }

    /**
     * Formats the standardized resolution result.
     */
    protected function formatResult($country, string $confidence, string $method): array
    {
        return [
            'country_id' => $country->id,
            'country_name' => $country->country_name,
            'country_code' => $country->country_code ?? null,
            'confidence_score' => $confidence,
            'resolution_method' => $method
        ];
    }

    /**
     * Logs the resolution outcome.
     */
    protected function logResolution(string $countryName, string $method, string $confidence): void
    {
        Log::info("CountryResolver: Matched [{$countryName}] via [{$method}] with [{$confidence}] confidence.");
    }

    /**
     * Dictionary of country aliases to their official database names.
     */
    protected function getAliases(): array
    {
        return [
            'USA' => 'United States',
            'US' => 'United States',
            'America' => 'United States',
            'United States of America' => 'United States',
            'UK' => 'United Kingdom',
            'Britain' => 'United Kingdom',
            'England' => 'United Kingdom',
            'Great Britain' => 'United Kingdom',
            'UAE' => 'United Arab Emirates',
            'South Korea' => 'South Korea', // Assuming database uses 'South Korea'
            'Korea Selatan' => 'South Korea',
            'Republic of Korea' => 'South Korea',
            'North Korea' => 'North Korea',
            'DPRK' => 'North Korea',
            'Russia' => 'Russian Federation',
            'PRC' => 'China',
            'People\'s Republic of China' => 'China',
            'Taiwan' => 'Taiwan, Province of China',
            'Vietnam' => 'Viet Nam',
        ];
    }
}
