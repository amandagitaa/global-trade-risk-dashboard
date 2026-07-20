<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, DashboardService $dashboardService)
    {
        $countryId = $request->get('country_id');
        $data = $dashboardService->buildDashboard($countryId);

        if ($request->ajax()) {
            return response()->json([
                'summary_html' => view('dashboard.components.summary-cards', $data)->render(),
                'map_data' => $data['mapCountries'],
                'selected_country_id' => $countryId,
                'chart_data' => $data['riskDistribution'],
                'highest_risk_html' => view('dashboard.components.highest-risk', $data)->render(),
                'port_risk_html' => view('dashboard.components.port-risk-panel', $data)->render(),
                'weather_html' => view('dashboard.components.weather-panel', $data)->render(),
                'currency_html' => view('dashboard.components.currency-panel', $data)->render(),
                
                'news_html' => view('dashboard.components.news-panel', $data)->render(),
            ]);
        }

        return view('dashboard.index', $data);
    }
}