<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Services\CountryService;
use Illuminate\Validation\Rule;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $query = Country::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('country_name', 'like', "%{$search}%")
                  ->orWhere('country_code', 'like', "%{$search}%")
                  ->orWhere('region', 'like', "%{$search}%")
                  ->orWhere('currency_name', 'like', "%{$search}%")
                  ->orWhere('currency_code', 'like', "%{$search}%");
            });
        }

        // Filter by Region
        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        // Filter by Currency
        if ($request->filled('currency')) {
            $query->where('currency_code', $request->currency);
        }

        $countries = $query->orderBy('country_name', 'asc')->paginate(15)->withQueryString();

        // Get distinct regions and currencies for filters
        $regions = Country::select('region')->whereNotNull('region')->distinct()->orderBy('region')->pluck('region');
        $currencies = Country::select('currency_code', 'currency_name')->whereNotNull('currency_code')->distinct()->orderBy('currency_code')->get();

        return view('admin.countries.index', compact('countries', 'regions', 'currencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'country_name' => 'required|string|max:255',
            'country_code' => 'required|string|max:10|unique:countries,country_code',
            'region' => 'nullable|string|max:255',
            'currency_name' => 'nullable|string|max:255',
            'currency_code' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        Country::create($request->only([
            'country_name', 'country_code', 'region', 'currency_name', 'currency_code', 'latitude', 'longitude'
        ]));

        return redirect()->route('admin.countries')->with('success', 'Country created successfully.');
    }

    public function update(Request $request, Country $country)
    {
        $request->validate([
            'country_name' => 'required|string|max:255',
            'country_code' => ['required', 'string', 'max:10', Rule::unique('countries')->ignore($country->id)],
            'region' => 'nullable|string|max:255',
            'currency_name' => 'nullable|string|max:255',
            'currency_code' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $country->update($request->only([
            'country_name', 'country_code', 'region', 'currency_name', 'currency_code', 'latitude', 'longitude'
        ]));

        return redirect()->route('admin.countries')->with('success', 'Country updated successfully.');
    }

    public function destroy(Country $country)
    {
        // Safety Check: check if country has any linked relationships
        if (
            $country->ports()->exists() ||
            $country->riskScores()->exists() ||
            $country->weatherData()->exists() ||
            $country->currencyRates()->exists() ||
            $country->newsCache()->exists() ||
            $country->economicData()->exists() ||
            $country->recommendation()->exists()
        ) {
            return redirect()->route('admin.countries')->with('error', 'Cannot delete this country because it is linked to existing data (Ports, Risk Scores, Weather, etc).');
        }

        $country->delete();

        return redirect()->route('admin.countries')->with('success', 'Country deleted successfully.');
    }

    public function sync(CountryService $countryService)
    {
        $result = $countryService->syncCountries();

        if ($result) {
            return redirect()->route('admin.countries')->with('success', 'Countries data updated successfully from API.');
        }

        return redirect()->route('admin.countries')->with('error', 'Failed to update countries data from API.');
    }
}
