<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Country;
use App\Models\RiskScore;
use App\Models\ShippingRoute;
use App\Models\WatchList;
use App\Models\WeatherData;
use App\Models\CurrencyRate;
use App\Models\EconomicData;
use App\Models\Port;

class ReportExportController extends Controller
{
    private function generateRecommendationLive($a, $b)
    {
        $scoreA = 0; $scoreB = 0; $reasonsA = []; $reasonsB = [];

        $riskA = $a->latestRisk ? $a->latestRisk->final_score : 100;
        $riskB = $b->latestRisk ? $b->latestRisk->final_score : 100;
        if ($riskA < $riskB) { $scoreA++; $reasonsA[] = 'Lower Overall Risk'; }
        elseif ($riskB < $riskA) { $scoreB++; $reasonsB[] = 'Lower Overall Risk'; }

        $gdpA = $a->economicData ? (float) $a->economicData->gdp : 0;
        $gdpB = $b->economicData ? (float) $b->economicData->gdp : 0;
        if ($gdpA > $gdpB) { $scoreA++; $reasonsA[] = 'Stronger Economy (Higher GDP)'; }
        elseif ($gdpB > $gdpA) { $scoreB++; $reasonsB[] = 'Stronger Economy (Higher GDP)'; }

        $infA = $a->economicData ? (float) $a->economicData->inflation : 100;
        $infB = $b->economicData ? (float) $b->economicData->inflation : 100;
        if ($infA < $infB) { $scoreA++; $reasonsA[] = 'Lower Inflation Rate'; }
        elseif ($infB < $infA) { $scoreB++; $reasonsB[] = 'Lower Inflation Rate'; }

        $currA = $a->latestCurrency ? strtolower($a->latestCurrency->status) : '';
        $currB = $b->latestCurrency ? strtolower($b->latestCurrency->status) : '';
        if (in_array($currA, ['stable', 'strong']) && !in_array($currB, ['stable', 'strong'])) { $scoreA++; $reasonsA[] = 'More Stable Currency'; }
        elseif (in_array($currB, ['stable', 'strong']) && !in_array($currA, ['stable', 'strong'])) { $scoreB++; $reasonsB[] = 'More Stable Currency'; }

        $weathA = $a->latestRisk ? $a->latestRisk->weather_score : 100;
        $weathB = $b->latestRisk ? $b->latestRisk->weather_score : 100;
        if ($weathA < $weathB) { $scoreA++; $reasonsA[] = 'Better Weather Conditions'; }
        elseif ($weathB < $weathA) { $scoreB++; $reasonsB[] = 'Better Weather Conditions'; }

        $winner = null; $reasons = [];
        if ($scoreA >= $scoreB) {
            $winner = $a; $reasons = $reasonsA;
            if (empty($reasons)) $reasons[] = 'Better Trade Environment';
        } else {
            $winner = $b; $reasons = $reasonsB;
            if (empty($reasons)) $reasons[] = 'Better Trade Environment';
        }

        return [
            'winner' => $winner,
            'reasons' => $reasons,
            'conclusion' => "{$winner->country_name} is currently the recommended trading partner based on integrated supply chain risk analysis."
        ];
    }

    private function getLiveComparisonData(Request $request)
    {
        $countryAId = $request->get('country_a');
        $countryBId = $request->get('country_b');

        if (!$countryAId || !$countryBId) abort(404);

        $countryA = Country::with(['latestRisk', 'latestWeather', 'latestCurrency', 'economicData', 'ports', 'newsCache' => function($q) { $q->latest('published_at')->take(5); }])->find($countryAId);
        $countryB = Country::with(['latestRisk', 'latestWeather', 'latestCurrency', 'economicData', 'ports', 'newsCache' => function($q) { $q->latest('published_at')->take(5); }])->find($countryBId);

        if (!$countryA || !$countryB) abort(404);

        $recommendation = $this->generateRecommendationLive($countryA, $countryB);
        
        $newsListA = $countryA->newsCache;
        $newsListB = $countryB->newsCache;

        return [
            'country_name_a' => $countryA->country_name,
            'country_name_b' => $countryB->country_name,
            'country_code_a' => $countryA->country_code,
            'country_code_b' => $countryB->country_code,
            'flag_a' => $countryA->flag ?? 'https://flagcdn.com/w80/'.strtolower($countryA->country_code).'.png',
            'flag_b' => $countryB->flag ?? 'https://flagcdn.com/w80/'.strtolower($countryB->country_code).'.png',
            'capital_a' => $countryA->capital ?? '-',
            'capital_b' => $countryB->capital ?? '-',
            'region_a' => $countryA->region ?? '-',
            'region_b' => $countryB->region ?? '-',
            'population_a' => $countryA->population ?? 0,
            'population_b' => $countryB->population ?? 0,
            'currency_code_a' => $countryA->currency_code ?? '-',
            'currency_code_b' => $countryB->currency_code ?? '-',
            'currency_name_a' => $countryA->currency_name ?? '-',
            'currency_name_b' => $countryB->currency_name ?? '-',
            'language_a' => $countryA->language ?? '-',
            'language_b' => $countryB->language ?? '-',
            'gdp_a' => $countryA->economicData ? $countryA->economicData->gdp : 0,
            'gdp_b' => $countryB->economicData ? $countryB->economicData->gdp : 0,
            'formatted_gdp_a' => $countryA->economicData ? $countryA->economicData->formatted_gdp : '-',
            'formatted_gdp_b' => $countryB->economicData ? $countryB->economicData->formatted_gdp : '-',
            'formatted_exports_a' => $countryA->economicData ? $countryA->economicData->formatted_exports : '-',
            'formatted_exports_b' => $countryB->economicData ? $countryB->economicData->formatted_exports : '-',
            'formatted_imports_a' => $countryA->economicData ? $countryA->economicData->formatted_imports : '-',
            'formatted_imports_b' => $countryB->economicData ? $countryB->economicData->formatted_imports : '-',
            'inflation_a' => $countryA->economicData ? $countryA->economicData->inflation : 0,
            'inflation_b' => $countryB->economicData ? $countryB->economicData->inflation : 0,
            'temp_a' => $countryA->latestWeather ? $countryA->latestWeather->temperature : '-',
            'temp_b' => $countryB->latestWeather ? $countryB->latestWeather->temperature : '-',
            'rain_a' => $countryA->latestWeather ? $countryA->latestWeather->precipitation : '-',
            'rain_b' => $countryB->latestWeather ? $countryB->latestWeather->precipitation : '-',
            'wind_a' => $countryA->latestWeather ? $countryA->latestWeather->wind_speed : '-',
            'wind_b' => $countryB->latestWeather ? $countryB->latestWeather->wind_speed : '-',
            'weather_status_a' => $countryA->latestWeather ? $countryA->latestWeather->weather_status : '-',
            'weather_status_b' => $countryB->latestWeather ? $countryB->latestWeather->weather_status : '-',
            'exchange_a' => $countryA->latestCurrency ? $countryA->latestCurrency->exchange_rate : '-',
            'exchange_b' => $countryB->latestCurrency ? $countryB->latestCurrency->exchange_rate : '-',
            'currency_status_a' => $countryA->latestCurrency ? $countryA->latestCurrency->status : '-',
            'currency_status_b' => $countryB->latestCurrency ? $countryB->latestCurrency->status : '-',
            'ports_count_a' => $countryA->ports->count(),
            'ports_count_b' => $countryB->ports->count(),
            'main_port_a' => $countryA->ports->first() ? $countryA->ports->first()->port_name : '-',
            'main_port_b' => $countryB->ports->first() ? $countryB->ports->first()->port_name : '-',
            'news_sentiment_a' => $newsListA->count() > 0 ? $newsListA->avg('sentiment_score') : 0,
            'news_sentiment_b' => $newsListB->count() > 0 ? $newsListB->avg('sentiment_score') : 0,
            'risk_weather_a' => $countryA->latestRisk ? $countryA->latestRisk->weather_score : 100,
            'risk_weather_b' => $countryB->latestRisk ? $countryB->latestRisk->weather_score : 100,
            'risk_currency_a' => $countryA->latestRisk ? $countryA->latestRisk->currency_score : 100,
            'risk_currency_b' => $countryB->latestRisk ? $countryB->latestRisk->currency_score : 100,
            'risk_inflation_a' => $countryA->latestRisk ? $countryA->latestRisk->inflation_score : 100,
            'risk_inflation_b' => $countryB->latestRisk ? $countryB->latestRisk->inflation_score : 100,
            'risk_political_a' => $countryA->latestRisk ? $countryA->latestRisk->political_score : 100,
            'risk_political_b' => $countryB->latestRisk ? $countryB->latestRisk->political_score : 100,
            'risk_economic_a' => $countryA->latestRisk ? $countryA->latestRisk->economic_score : 100,
            'risk_economic_b' => $countryB->latestRisk ? $countryB->latestRisk->economic_score : 100,
            'risk_news_a' => $countryA->latestRisk ? $countryA->latestRisk->news_score : 100,
            'risk_news_b' => $countryB->latestRisk ? $countryB->latestRisk->news_score : 100,
            'risk_port_a' => $countryA->latestRisk ? $countryA->latestRisk->port_score : 100,
            'risk_port_b' => $countryB->latestRisk ? $countryB->latestRisk->port_score : 100,
            'risk_final_a' => $countryA->latestRisk ? $countryA->latestRisk->final_score : 100,
            'risk_final_b' => $countryB->latestRisk ? $countryB->latestRisk->final_score : 100,
            'winner_name' => $recommendation['winner'] ? $recommendation['winner']->country_name : '',
            'winner_flag' => $recommendation['winner'] ? $recommendation['winner']->flag : '',
            'reasons' => $recommendation['reasons'],
            'conclusion' => $recommendation['conclusion'] ?? ''
        ];
    }

    public function compareLivePdf(Request $request)
    {
        $res = $this->getLiveComparisonData($request);
        $pdf = Pdf::loadView('reports.exports.compare-live', compact('res'));
        $fileName = 'country-comparison-' . strtolower(str_replace(' ', '-', $res['country_name_a'])) . '-' . strtolower(str_replace(' ', '-', $res['country_name_b'])) . '.pdf';
        return $pdf->download($fileName);
    }

    public function compareLiveExcel(Request $request)
    {
        $res = $this->getLiveComparisonData($request);
        $fileName = 'country-comparison-' . strtolower(str_replace(' ', '-', $res['country_name_a'])) . '-' . strtolower(str_replace(' ', '-', $res['country_name_b'])) . '.xlsx';
        return Excel::download(new \App\Exports\CompareLiveExport($res), $fileName);
    }
    public function __construct()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(300);
    }
    // ==========================================
    // 1. Trade Planner Report
    // ==========================================
    private function getTradePlannerData($isPdf = false)
    {
        $query = \App\Models\TradeSimulation::with(['originCountry', 'destinationCountry', 'originPort', 'destinationPort'])
            ->where('user_id', auth()->id())
            ->latest();
            
        if ($isPdf) $query->take(300); // Prevent PDF Out Of Memory
        $routes = $query->get();
        
        $data = [];
        foreach($routes as $r) {
            $data[] = [
                'origin_country' => $r->originCountry->country_name ?? 'N/A',
                'destination_country' => $r->destinationCountry->country_name ?? 'N/A',
                'origin_port' => $r->originPort->port_name ?? 'N/A',
                'destination_port' => $r->destinationPort->port_name ?? 'N/A',
                'cargo_type' => $r->cargo_type ?? 'Container',
                'container_size' => $r->container_size ?? '40ft',
                'distance' => $r->estimated_distance . ' km',
                'eta' => $r->estimated_duration . ' Days',
                'weather_impact' => $r->weather_impact ?? 'N/A',
                'currency_impact' => $r->currency_impact ?? 'N/A',
                'trade_risk' => $r->risk_level ?? 'N/A',
                'ai_recommendation' => $r->ai_recommendation,
                'simulation_date' => $r->created_at ? $r->created_at->format('Y-m-d') : 'N/A'
            ];
        }
        return $data;
    }

    public function tradePlannerView() {
        $data = $this->getTradePlannerData(true);
        return view('reports.exports.trade-planner', compact('data'));
    }

    public function tradePlannerPdf() {
        $data = $this->getTradePlannerData(true);
        $pdf = Pdf::loadView('reports.exports.trade-planner', ['data' => $data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return $pdf->download('trade-planner-report.pdf');
    }

    public function tradePlannerExcel() {
        $data = $this->getTradePlannerData(false);
        return Excel::download(new \App\Exports\TradePlannerExport($data), 'trade-planner-report.xlsx');
    }

    public function singleSimulationPdf($id) {
        $r = \App\Models\TradeSimulation::with(['originCountry', 'destinationCountry', 'originPort', 'destinationPort'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $data = [[
            'origin_country' => $r->originCountry->country_name ?? 'N/A',
            'destination_country' => $r->destinationCountry->country_name ?? 'N/A',
            'origin_port' => $r->originPort->port_name ?? 'N/A',
            'destination_port' => $r->destinationPort->port_name ?? 'N/A',
            'cargo_type' => $r->cargo_type ?? 'Container',
            'container_size' => $r->container_size ?? '40ft',
            'distance' => $r->estimated_distance . ' km',
            'eta' => $r->estimated_duration . ' Days',
            'weather_impact' => $r->weather_impact ?? 'N/A',
            'currency_impact' => $r->currency_impact ?? 'N/A',
            'trade_risk' => $r->risk_level ?? 'N/A',
            'ai_recommendation' => $r->ai_recommendation,
            'simulation_date' => $r->created_at ? $r->created_at->format('Y-m-d') : 'N/A'
        ]];

        $pdf = Pdf::loadView('reports.exports.trade-planner', ['data' => $data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return $pdf->download('trade-simulation-' . $r->id . '.pdf');
    }
    // ==========================================
    // 2. Risk Analysis Report
    // ==========================================
    private function getRiskAnalysisData($isPdf = false)
    {
        $query = RiskScore::with('country');
        if ($isPdf) $query->take(300);
        $scores = $query->get();
        $data = [];
        foreach($scores as $s) {
            $data[] = [
                'country' => $s->country->country_name ?? 'N/A',
                'risk_score' => $s->final_score,
                'risk_level' => $s->final_score > 70 ? 'High' : ($s->final_score > 40 ? 'Medium' : 'Low'),
                'weather_risk' => 'Low',
                'currency_risk' => 'Medium',
                'economy_risk' => 'Low',
                'political_risk' => 'Medium',
                'overall_recommendation' => 'Proceed with Caution'
            ];
        }
        return $data;
    }

    public function riskAnalysisView() {
        $data = $this->getRiskAnalysisData(true);
        return view('reports.exports.risk-analysis', compact('data'));
    }

    public function riskAnalysisPdf() {
        $data = $this->getRiskAnalysisData(true);
        $pdf = Pdf::loadView('reports.exports.risk-analysis', ['data' => $data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return $pdf->download('risk-analysis-report.pdf');
    }

    public function riskAnalysisExcel() {
        $data = $this->getRiskAnalysisData(false);
        return Excel::download(new \App\Exports\RiskAnalysisExport($data), 'risk-analysis-report.xlsx');
    }
    // ==========================================
    // 3. Countries Report
    // ==========================================
    private function getCountriesData($isPdf = false)
    {
        $query = Country::with('economicData');
        if ($isPdf) $query->take(300);
        $countries = $query->get();
        $data = [];
        foreach($countries as $c) {
            $eco = $c->economicData->first();
            $data[] = [
                'country' => $c->country_name,
                'capital' => $c->capital ?? 'N/A',
                'region' => $c->region,
                'currency' => $c->currency_code,
                'population' => number_format($c->population),
                'gdp' => $eco ? '$' . number_format($eco->gdp_value) : 'N/A',
                'inflation' => $eco ? $eco->inflation_rate . '%' : 'N/A',
                'export_value' => $eco ? '$' . number_format($eco->export_value) : 'N/A',
                'import_value' => $eco ? '$' . number_format($eco->import_value) : 'N/A'
            ];
        }
        return $data;
    }

    public function countriesView() {
        $data = $this->getCountriesData(true);
        return view('reports.exports.countries', compact('data'));
    }

    public function countriesPdf() {
        $data = $this->getCountriesData(true);
        $pdf = Pdf::loadView('reports.exports.countries', ['data' => $data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return $pdf->download('countries-report.pdf');
    }

    public function countriesExcel() {
        $data = $this->getCountriesData(false);
        return Excel::download(new \App\Exports\CountriesExport($data), 'countries-report.xlsx');
    }
    // ==========================================
    // 4. Weather Report
    // ==========================================
    private function getWeatherData($isPdf = false)
    {
        $query = WeatherData::with('country');
        if ($isPdf) $query->take(300);
        $weathers = $query->get();
        $data = [];
        foreach($weathers as $w) {
            $data[] = [
                'country' => $w->country->country_name ?? 'N/A',
                'temperature' => $w->temperature_celsius . '°C',
                'humidity' => $w->humidity_percent . '%',
                'wind_speed' => $w->wind_speed_kmh . ' km/h',
                'weather_status' => $w->weather_condition,
                'storm_risk' => $w->is_storm_warning ? 'High' : 'Low',
                'updated_at' => $w->updated_at->format('Y-m-d H:i')
            ];
        }
        return $data;
    }

    public function weatherView() {
        $data = $this->getWeatherData(true);
        return view('reports.exports.weather', compact('data'));
    }

    public function weatherPdf() {
        $data = $this->getWeatherData(true);
        $pdf = Pdf::loadView('reports.exports.weather', ['data' => $data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return $pdf->download('weather-report.pdf');
    }

    public function weatherExcel() {
        $data = $this->getWeatherData(false);
        return Excel::download(new \App\Exports\WeatherExport($data), 'weather-report.xlsx');
    }
    // ==========================================
    // 5. Currency Report
    // ==========================================
    private function getCurrencyData($isPdf = false)
    {
        $query = CurrencyRate::with('country');
        if ($isPdf) $query->take(300);
        $currencies = $query->get();
        $data = [];
        foreach($currencies as $c) {
            $data[] = [
                'country' => $c->country->country_name ?? 'N/A',
                'currency' => $c->currency_code,
                'exchange_rate' => $c->exchange_rate_to_usd,
                'change_pct' => $c->trend_percentage,
                'status' => $c->trend_percentage < -2 ? 'Volatile' : ($c->trend_percentage > 2 ? 'Warning' : 'Stable'),
                'updated_at' => $c->updated_at->format('Y-m-d H:i')
            ];
        }
        return $data;
    }

    public function currencyView() {
        $data = $this->getCurrencyData(true);
        return view('reports.exports.currency', compact('data'));
    }

    public function currencyPdf() {
        $data = $this->getCurrencyData(true);
        $pdf = Pdf::loadView('reports.exports.currency', ['data' => $data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return $pdf->download('currency-report.pdf');
    }

    public function currencyExcel() {
        $data = $this->getCurrencyData(false);
        return Excel::download(new \App\Exports\CurrencyExport($data), 'currency-report.xlsx');
    }
    // ==========================================
    // 6. Economy Report
    // ==========================================
    private function getEconomyData($isPdf = false)
    {
        $query = EconomicData::with('country');
        if ($isPdf) $query->take(300);
        $economies = $query->get();
        $data = [];
        foreach($economies as $e) {
            $data[] = [
                'country' => $e->country->country_name ?? 'N/A',
                'gdp' => '$' . number_format($e->gdp_value),
                'inflation' => $e->inflation_rate . '%',
                'unemployment' => $e->unemployment_rate . '%',
                'export' => '$' . number_format($e->export_value),
                'import' => '$' . number_format($e->import_value),
                'economic_status' => $e->inflation_rate > 10 ? 'Crisis' : ($e->inflation_rate > 5 ? 'Warning' : 'Stable')
            ];
        }
        return $data;
    }

    public function economyView() {
        $data = $this->getEconomyData(true);
        return view('reports.exports.economy', compact('data'));
    }

    public function economyPdf() {
        $data = $this->getEconomyData(true);
        $pdf = Pdf::loadView('reports.exports.economy', ['data' => $data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return $pdf->download('economy-report.pdf');
    }

    public function economyExcel() {
        $data = $this->getEconomyData(false);
        return Excel::download(new \App\Exports\EconomyExport($data), 'economy-report.xlsx');
    }
    // ==========================================
    // 7. Ports Report
    // ==========================================
    private function getPortsData($isPdf = false)
    {
        $query = Port::with('country');
        if ($isPdf) $query->take(300);
        $ports = $query->get();
        $data = [];
        foreach($ports as $p) {
            $data[] = [
                'port_name' => $p->port_name,
                'country' => $p->country->country_name ?? 'N/A',
                'capacity' => number_format($p->capacity_teu) . ' TEU',
                'active_ships' => $p->active_ships,
                'congestion' => $p->congestion_level,
                'operational_status' => $p->operational_status,
                'risk' => $p->congestion_level == 'High' ? 'High' : 'Low'
            ];
        }
        return $data;
    }

    public function portsView() {
        $data = $this->getPortsData(true);
        return view('reports.exports.ports', compact('data'));
    }

    public function portsPdf() {
        $data = $this->getPortsData(true);
        $pdf = Pdf::loadView('reports.exports.ports', ['data' => $data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return $pdf->download('ports-report.pdf');
    }

    public function portsExcel() {
        $data = $this->getPortsData(false);
        return Excel::download(new \App\Exports\PortsExport($data), 'ports-report.xlsx');
    }
    // ==========================================
    // 8. Watch List Report
    // ==========================================
    private function getWatchListData($isPdf = false)
    {
        $query = WatchList::with('watchable');
        if ($isPdf) $query->take(300);
        $watchlist = $query->get();
        $data = [];
        foreach($watchlist as $w) {
            $name = 'Unknown';
            if ($w->watchable_type == \App\Models\Country::class) {
                $name = $w->watchable->country_name ?? 'Country';
            } elseif ($w->watchable_type == \App\Models\Port::class) {
                $name = $w->watchable->port_name ?? 'Port';
            }
            $data[] = [
                'watch_type' => class_basename($w->watchable_type),
                'name' => $name,
                'current_risk' => 'Medium',
                'weather' => 'Clear',
                'currency' => 'Stable',
                'monitoring_status' => 'Active',
                'added_date' => $w->created_at->format('Y-m-d')
            ];
        }
        return $data;
    }

    public function watchListView() {
        $data = $this->getWatchListData(true);
        return view('reports.exports.watch-list', compact('data'));
    }

    public function watchListPdf() {
        $data = $this->getWatchListData(true);
        $pdf = Pdf::loadView('reports.exports.watch-list', ['data' => $data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return $pdf->download('watch-list-report.pdf');
    }

    public function watchListExcel() {
        $data = $this->getWatchListData(false);
        return Excel::download(new \App\Exports\WatchListExport($data), 'watch-list-report.xlsx');
    }


    // ==========================================
    // Country Comparison Report
    // ==========================================
    private function getComparisonData($id, $isPdf = false)
    {
        $comp = \App\Models\CountryComparison::with(['countryA', 'countryB', 'user'])
            ->where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        return [[
            'country_a' => $comp->countryA ? $comp->countryA->country_name : '-',
            'country_b' => $comp->countryB ? $comp->countryB->country_name : '-',
            'gdp_a' => $comp->comparison_result['gdp_a'] ?? '-',
            'gdp_b' => $comp->comparison_result['gdp_b'] ?? '-',
            'inflation_a' => $comp->comparison_result['inflation_a'] ?? '-',
            'inflation_b' => $comp->comparison_result['inflation_b'] ?? '-',
            'currency_a' => $comp->comparison_result['currency_a'] ?? '-',
            'currency_b' => $comp->comparison_result['currency_b'] ?? '-',
            'weather_a' => $comp->comparison_result['weather_a'] ?? '-',
            'weather_b' => $comp->comparison_result['weather_b'] ?? '-',
            'ports_a' => $comp->comparison_result['ports_a'] ?? '-',
            'ports_b' => $comp->comparison_result['ports_b'] ?? '-',
            'news_sentiment_a' => $comp->comparison_result['news_sentiment_a'] ?? '-',
            'news_sentiment_b' => $comp->comparison_result['news_sentiment_b'] ?? '-',
            'risk_score_a' => $comp->risk_score_a ?? '-',
            'risk_score_b' => $comp->risk_score_b ?? '-',
            'recommendation' => $comp->recommended_country,
            'summary' => $comp->recommendation,
            'comparison_date' => $comp->created_at->format('Y-m-d H:i'),
            'created_by' => $comp->user->name ?? 'User'
        ]];
    }

    public function compareHistory() {
        $countryComparisonsList = \App\Models\CountryComparison::with(['countryA', 'countryB'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
            
        return view('reports.compare-history', compact('countryComparisonsList'));
    }

        private function getComparisonDataAll($isPdf = false)
    {
        $query = \App\Models\CountryComparison::with(['countryA', 'countryB', 'user'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [];
        foreach ($query as $comp) {
            $data[] = [
                'country_a' => $comp->countryA ? $comp->countryA->country_name : '-',
                'country_b' => $comp->countryB ? $comp->countryB->country_name : '-',
                'gdp_a' => $comp->comparison_result['gdp_a'] ?? '-',
                'gdp_b' => $comp->comparison_result['gdp_b'] ?? '-',
                'inflation_a' => $comp->comparison_result['inflation_a'] ?? '-',
                'inflation_b' => $comp->comparison_result['inflation_b'] ?? '-',
                'currency_a' => $comp->comparison_result['currency_a'] ?? '-',
                'currency_b' => $comp->comparison_result['currency_b'] ?? '-',
                'weather_a' => $comp->comparison_result['weather_a'] ?? '-',
                'weather_b' => $comp->comparison_result['weather_b'] ?? '-',
                'ports_a' => $comp->comparison_result['ports_a'] ?? '-',
                'ports_b' => $comp->comparison_result['ports_b'] ?? '-',
                'news_sentiment_a' => $comp->comparison_result['news_sentiment_a'] ?? '-',
                'news_sentiment_b' => $comp->comparison_result['news_sentiment_b'] ?? '-',
                'risk_score_a' => $comp->risk_score_a ?? '-',
                'risk_score_b' => $comp->risk_score_b ?? '-',
                'recommendation' => $comp->recommended_country,
                'summary' => $comp->recommendation,
                'comparison_date' => $comp->created_at->format('Y-m-d H:i'),
                'created_by' => $comp->user->name ?? 'User'
            ];
        }
        return $data;
    }

    public function comparisonPdfAll() {
        $data = $this->getComparisonDataAll(true);
        $pdf = Pdf::loadView('reports.exports.country-comparison', ['data' => $data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return $pdf->download('country-comparison-history.pdf');
    }

    public function comparisonExcelAll() {
        $data = $this->getComparisonDataAll(false);
        
        $excelData = [];
        $no = 1;
        foreach ($data as $row) {
            $excelData[] = [
                'No' => $no++,
                'Country A' => $row['country_a'],
                'Country B' => $row['country_b'],
                'GDP A' => $row['gdp_a'],
                'GDP B' => $row['gdp_b'],
                'Inflation A' => $row['inflation_a'] . '%',
                'Inflation B' => $row['inflation_b'] . '%',
                'Risk Score A' => $row['risk_score_a'],
                'Risk Score B' => $row['risk_score_b'],
                'Recommended Country' => $row['recommendation'],
                'Recommendation' => $row['summary'],
                'Comparison Date' => $row['comparison_date'],
                'Trade Analyst' => $row['created_by']
            ];
        }

        return Excel::download(new \App\Exports\CountryComparisonExport($excelData), 'country-comparison-history.xlsx');
    }

    public function comparisonDetail($id) {
        $comp = \App\Models\CountryComparison::with(['countryA', 'countryB', 'user'])
            ->where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        return view('reports.country-comparison-detail', compact('comp'));
    }

    public function comparisonPdf($id) {
        $data = $this->getComparisonData($id, true);
        $pdf = Pdf::loadView('reports.exports.country-comparison', ['data' => $data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return $pdf->download('country-comparison-report.pdf');
    }

    public function comparisonExcel($id) {
        $data = $this->getComparisonData($id, false);
        return Excel::download(new \App\Exports\CountryComparisonExport($data), 'country-comparison-report.xlsx');
    }

}