<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\NewsCache;
use App\Services\News\NewsSyncService;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function __construct(
        protected NewsSyncService $syncService
    ) {}

    /**
     * Daftar berita
     */
    public function index(Request $request)
    {
        $query = NewsCache::with('country');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('source', 'like', '%' . $request->search . '%')
                  ->orWhereHas('country', function ($country) use ($request) {
                      $country->where('country_name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('country')) {
            $query->where('country_id', $request->country);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('sentiment')) {
            $query->where('sentiment', $request->sentiment);
        }

        if ($request->filled('impact_level')) {
            $query->where('impact_level', $request->impact_level);
        }

        if ($request->filled('risk_direction')) {
            $query->where('risk_direction', $request->risk_direction);
        }

        if ($request->filled('trade_exposure_type')) {
            $query->where('trade_exposure_type', $request->trade_exposure_type);
        }

        $news = $query->latest('published_at')->paginate(9)->withQueryString();

        $ttl = config('news.cache_ttl', 3600);

        $countries = \Illuminate\Support\Facades\Cache::remember('news_countries_list', $ttl, function() {
            return Country::orderBy('country_name')->get();
        });
        
        $categories = [
            'Business', 'Energy', 'General', 'Geopolitics', 'Logistics', 
            'Manufacturing', 'Shipping', 'Technology', 'Trade'
        ];
        
        $sources = \Illuminate\Support\Facades\Cache::remember('news_sources_list', $ttl, function() {
            return NewsCache::select('source')->whereNotNull('source')->distinct()->orderBy('source')->pluck('source');
        });

        $stats = \Illuminate\Support\Facades\Cache::remember('news_global_stats_v2', $ttl, function() {
            $allCountries = NewsCache::whereNotNull('mapped_countries')->pluck('mapped_countries');
            $allRegions = NewsCache::whereNotNull('regional_entities')->pluck('regional_entities');
            
            $uniqueGeo = collect();
            foreach ($allCountries as $countries) {
                if (is_array($countries)) {
                    foreach ($countries as $c) {
                        $uniqueGeo->push($c['name'] ?? null);
                    }
                }
            }
            foreach ($allRegions as $regions) {
                if (is_array($regions)) {
                    foreach ($regions as $r) {
                        $uniqueGeo->push($r['name'] ?? null);
                    }
                }
            }

            return [
                'totalNews' => NewsCache::count(),
                'highImpactCount' => NewsCache::where('impact_level', 'High')->count(),
                'increasingRiskCount' => NewsCache::where('risk_direction', 'Increasing')->count(),
                'affectedCountriesCount' => $uniqueGeo->filter()->unique()->count(),
                'latestUpdate' => NewsCache::max('updated_at')
            ];
        });

        extract($stats);

        return view('news.index', compact(
            'news', 'countries', 'categories', 'sources', 'totalNews', 
            'highImpactCount', 'increasingRiskCount', 'affectedCountriesCount', 
            'latestUpdate'
        ));
    }

    /**
     * Detail berita
     */
    public function show(NewsCache $news)
    {
        $relatedNews = NewsCache::where('id', '!=', $news->id)
            ->where('country_id', $news->country_id)
            ->latest('published_at')
            ->take(4)
            ->get();

        return view('news.show', compact('news', 'relatedNews'));
    }

    /**
     * Sync News
     */
    public function sync()
    {
        try {
            $this->syncService->sync();
            return redirect()->route('news.index')->with('success', 'Real news database has been successfully synchronized from external API.');
        } catch (\Exception $e) {
            return redirect()->route('news.index')->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Sync Country
     */
    public function syncCountry(Country $country)
    {
        try {
            $code = $country->iso_alpha2 ?? $country->iso_alpha3;
            if ($code) {
                $this->syncService->syncByCountry($code);
                return back()->with('success', 'Real news synchronized specifically for ' . $country->country_name);
            }
            return back()->with('error', 'Country code not found for sync.');
        } catch (\Exception $e) {
            return back()->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }
}