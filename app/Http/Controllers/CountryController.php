<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\CountryDetailService;
use Illuminate\Http\Request;
use App\Services\CountryService;

class CountryController extends Controller
{
    /**
     * ======================================================
     * Countries List
     * ======================================================
     */

    public function index(Request $request)
    {
        $query = Country::with([
            'latestRisk',
            'latestWeather',
            'latestCurrency',
            'recommendation'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $query->where(function ($q) use ($request) {

                $q->where('country_name', 'like', '%' . $request->search . '%')
                  ->orWhere('country_code', 'like', '%' . $request->search . '%');

            });

        }

        /*
        |--------------------------------------------------------------------------
        | Region Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('region')) {

            $query->where('region', $request->region);

        }

        /*
        |--------------------------------------------------------------------------
        | Order
        |--------------------------------------------------------------------------
        */

        $countries = $query
            ->orderBy('country_name')
            ->paginate(12)
            ->withQueryString();

        $regions = Country::select('region')
            ->distinct()
            ->whereNotNull('region')
            ->orderBy('region')
            ->pluck('region');

        return view('countries.index', compact(
            'countries',
            'regions'
        ));
    }

    /**
     * ======================================================
     * Country Detail
     * ======================================================
     */

    public function show(
        Country $country,
        CountryDetailService $service
    )
    {
        return view(
            'countries.show',
            $service->build($country)
        );
    }

    public function search(Request $request)
{
    $keyword = trim($request->keyword);

    if ($keyword === '') {
        return response()->json([]);
    }

    $countries = Country::query()
        ->where('country_name', 'LIKE', "%{$keyword}%")
        ->orWhere('country_code', 'LIKE', "%{$keyword}%")
        ->orderBy('country_name')
        ->limit(10)
        ->get([
            'id',
            'country_name',
            'country_code'
        ]);

    return response()->json($countries);
}
public function sync(CountryService $service)
{
    $service->syncCountries();

    return redirect()
        ->route('countries.index')
        ->with('success', 'Countries synchronized successfully.');
}
}