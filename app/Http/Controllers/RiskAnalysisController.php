<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\RiskScore;

class RiskAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $query = RiskScore::with('country');

        if ($request->filled('country')) {
            $query->whereHas('country', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->country . '%');
            });
        }

        if ($request->filled('level')) {
            $query->where('risk_level', $request->level);
        }

        switch ($request->get('sort')) {

            case 'lowest':
                $query->orderBy('final_score', 'asc');
                break;

            case 'country':
                $query->join('countries', 'countries.id', '=', 'risk_scores.country_id')
                      ->select('risk_scores.*')
                      ->orderBy('countries.name');
                break;

            default:
                $query->orderByDesc('final_score');
        }

        $riskScores = $query->paginate(20);

        $summary = [

            'total' => Country::count(),

            'average' => round(RiskScore::avg('final_score'), 2),

            'safe' => RiskScore::where('risk_level', 'safe')->count(),

            'stable' => RiskScore::where('risk_level', 'stable')->count(),

            'alert' => RiskScore::where('risk_level', 'alert')->count(),

            'dangerous' => RiskScore::where('risk_level', 'dangerous')->count(),

            'critical' => RiskScore::where('risk_level', 'critical')->count(),

        ];

        return view('risk-analysis.index', compact(
            'summary',
            'riskScores'
        ));
    }

    public function show(Country $country)
    {
        $country->load([
            'latestWeather',
            'latestCurrency',
            'latestNews',
            'ports' => function($q) {
                $q->orderByDesc('logistic_risk')->limit(1);
            },
            'economicData'
        ]);

        $riskScore = RiskScore::where('country_id', $country->id)->first();
        $recommendation = \App\Models\TradeRecommendation::where('country_id', $country->id)->latest('generated_at')->first();
        $riskHistories = \App\Models\RiskHistory::where('country_id', $country->id)
            ->orderBy('record_date', 'asc')
            ->take(30)
            ->get();

        // Calculate Key Risk Drivers dynamically based on available scores
        $drivers = collect();
        $impacts = collect();

        if ($riskScore) {
            if ($riskScore->port_score >= 60) {
                $drivers->push(['factor' => 'Port Congestion', 'impact' => 'HIGH IMPACT']);
                $impacts->push(['risk' => 'Port Disruption', 'level' => 'HIGH', 'reason' => 'Severe port congestion increases logistics risk.']);
            } elseif ($riskScore->port_score >= 40) {
                $drivers->push(['factor' => 'Port Operations', 'impact' => 'MEDIUM IMPACT']);
                $impacts->push(['risk' => 'Port Disruption', 'level' => 'MEDIUM', 'reason' => 'Moderate port delays detected.']);
            }

            if ($riskScore->currency_score >= 60) {
                $drivers->push(['factor' => 'Currency Volatility', 'impact' => 'HIGH IMPACT']);
                $impacts->push(['risk' => 'Import Cost Pressure', 'level' => 'HIGH', 'reason' => 'Extreme exchange rate fluctuations.']);
            } elseif ($riskScore->currency_score >= 35) {
                $drivers->push(['factor' => 'Currency Depreciation', 'impact' => 'MEDIUM IMPACT']);
                $impacts->push(['risk' => 'Import Cost Pressure', 'level' => 'MEDIUM', 'reason' => 'Noticeable currency changes may affect pricing.']);
            }

            if ($riskScore->economic_score >= 60) {
                $drivers->push(['factor' => 'Economic Instability', 'impact' => 'HIGH IMPACT']);
                $impacts->push(['risk' => 'Market Demand', 'level' => 'HIGH', 'reason' => 'High economic risk threatens purchasing power.']);
            }

            if ($riskScore->weather_score >= 60) {
                $drivers->push(['factor' => 'Extreme Weather', 'impact' => 'HIGH IMPACT']);
                $impacts->push(['risk' => 'Supply Disruption', 'level' => 'HIGH', 'reason' => 'Severe weather conditions threaten infrastructure.']);
            }

            if ($riskScore->news_score >= 60) {
                $drivers->push(['factor' => 'Geopolitical Tension', 'impact' => 'HIGH IMPACT']);
                $impacts->push(['risk' => 'Trade Restrictions', 'level' => 'HIGH', 'reason' => 'Negative news sentiment indicates potential policy shifts or instability.']);
            }
        }

        // Sort drivers to prioritize HIGH IMPACT
        $drivers = $drivers->sortByDesc('impact')->take(4)->values();
        $impacts = $impacts->sortByDesc('level')->take(5)->values();

        return view('risk-analysis.show', compact(
            'country',
            'riskScore',
            'recommendation',
            'riskHistories',
            'drivers',
            'impacts'
        ));
    }
}