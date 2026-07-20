<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\EconomicData;
use Illuminate\Http\Request;

class EconomyController extends Controller
{
    public function index(Request $request)
    {
        $query = EconomicData::with('country');

        if ($request->filled('country')) {
            $query->where('country_id', $request->country);
        }

        if ($request->filled('year')) {
            $query->where('data_year', $request->year);
        }

        $statistics = [
            'total'           => (clone $query)->count(),
            'avg_gdp'         => (clone $query)->avg('gdp'),
            'highest_gdp'     => (clone $query)->max('gdp'),
            'avg_inflation'   => (clone $query)->avg('inflation'),
            'total_exports'   => (clone $query)->sum('exports'),
            'total_imports'   => (clone $query)->sum('imports'),
        ];

        $economies = $query->latest()->paginate(20)->appends($request->all());

        $countries = Country::orderBy('country_name')->get();

        return view('economy.index', compact(
            'economies',
            'countries',
            'statistics'
        ));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(EconomicData $economy)
    {
        $economy->load('country');

        return view('economy.show', compact('economy'));
    }

    public function edit(EconomicData $economy)
    {
        //
    }

    public function update(Request $request, EconomicData $economy)
    {
        //
    }

    public function destroy(EconomicData $economy)
    {
        //
    }
}