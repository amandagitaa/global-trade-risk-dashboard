<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\NewsCache;
use Illuminate\Http\Request;

class NewsController extends Controller
{


    /**
     * Daftar berita
     */
    public function index(Request $request)
    {


        $query = NewsCache::with('country');



        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {


            $query->where(function ($q) use ($request) {


                $q->where('title', 'like', '%' . $request->search . '%')

                ->orWhere('description', 'like', '%' . $request->search . '%')

                ->orWhere('source', 'like', '%' . $request->search . '%')


                ->orWhereHas('country', function ($country) use ($request) {


                    $country->where(
                        'country_name',
                        'like',
                        '%' . $request->search . '%'
                    );


                });


            });


        }




        /*
        |--------------------------------------------------------------------------
        | Filter Country
        |--------------------------------------------------------------------------
        */

        if ($request->filled('country')) {


            $query->where(
                'country_id',
                $request->country
            );


        }




        /*
        |--------------------------------------------------------------------------
        | Filter Category
        |--------------------------------------------------------------------------
        */

        if ($request->filled('category')) {


            $query->where(
                'category',
                $request->category
            );


        }




        /*
        |--------------------------------------------------------------------------
        | Filter Sentiment
        |--------------------------------------------------------------------------
        */

        if ($request->filled('sentiment')) {


            $query->where(
                'sentiment',
                $request->sentiment
            );


        }




        /*
        |--------------------------------------------------------------------------
        | News Data
        |--------------------------------------------------------------------------
        */

        $news = $query

            ->latest('published_at')

            ->paginate(9)

            ->withQueryString();







        /*
        |--------------------------------------------------------------------------
        | Dropdown Data
        |--------------------------------------------------------------------------
        */


        $countries = Country::orderBy(
            'country_name'
        )->get();




        $categories = NewsCache::select('category')

            ->whereNotNull('category')

            ->distinct()

            ->orderBy('category')

            ->pluck('category');





        $sources = NewsCache::select('source')

            ->whereNotNull('source')

            ->distinct()

            ->orderBy('source')

            ->pluck('source');







        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */


        $totalNews = NewsCache::count();



        $positiveNews = NewsCache::where(
            'sentiment',
            'positive'
        )->count();



        $neutralNews = NewsCache::where(
            'sentiment',
            'neutral'
        )->count();



        $negativeNews = NewsCache::where(
            'sentiment',
            'negative'
        )->count();



        $totalSources = NewsCache::whereNotNull(
            'source'
        )
        ->distinct()
        ->count('source');



        $totalCategories = NewsCache::whereNotNull(
            'category'
        )
        ->distinct()
        ->count('category');



        $latestUpdate = NewsCache::max(
            'updated_at'
        );







        return view(
            'news.index',
            compact(

                'news',

                'countries',

                'categories',

                'sources',

                'totalNews',

                'positiveNews',

                'neutralNews',

                'negativeNews',

                'totalSources',

                'totalCategories',

                'latestUpdate'

            )
        );



    }







    /**
     * Detail berita
     */
    public function show(NewsCache $news)
    {


        $relatedNews = NewsCache::where(
                'id',
                '!=',
                $news->id
            )

            ->where(
                'country_id',
                $news->country_id
            )

            ->latest('published_at')

            ->take(4)

            ->get();




        return view(
            'news.show',
            compact(
                'news',
                'relatedNews'
            )
        );


    }







    /**
     * Sync News
     *
     * Karena data sudah tersedia dari NewsSeeder
     */
    public function sync()
    {


        return redirect()

            ->route('news.index')

            ->with(
                'success',
                '2500 news database already synchronized.'
            );


    }







    /**
     * Sync Country
     *
     */
    public function syncCountry(Country $country)
    {


        return back()

            ->with(
                'success',
                'News available for ' .
                $country->country_name
            );


    }


}