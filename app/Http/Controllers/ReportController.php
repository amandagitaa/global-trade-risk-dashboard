<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\ShippingRoute;
use App\Models\WatchList;

class ReportController extends Controller
{
    public function index()
    {
        $totalCountries = Country::count();
        
        $averageRisk = RiskScore::avg('final_score') ?? 0;
        
        $tradeSimulations = \App\Models\TradeSimulation::where('user_id', auth()->id())->count();
        
        $watchListItems = WatchList::count();
        $countryComparisonsCount = \App\Models\CountryComparison::where('user_id', auth()->id())->count();
        $countryComparisonsList = \App\Models\CountryComparison::with(['countryA', 'countryB'])->where('user_id', auth()->id())->latest()->get();

        return view('reports.index', compact(
            'totalCountries',
            'averageRisk',
            'tradeSimulations',
            'watchListItems',
            'countryComparisonsCount',
            'countryComparisonsList'
        ));
    }
}