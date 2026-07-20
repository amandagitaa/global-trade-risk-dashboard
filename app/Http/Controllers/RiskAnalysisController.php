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
}