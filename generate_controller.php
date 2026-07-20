<?php

$methods = '';

// 1. Trade Planner
$methods .= <<<PHP
    // ==========================================
    // 1. Trade Planner Report
    // ==========================================
    private function getTradePlannerData(\$isPdf = false)
    {
        \$query = \App\Models\TradeSimulation::with(['originCountry', 'destinationCountry', 'originPort', 'destinationPort'])
            ->where('user_id', auth()->id())
            ->latest();
            
        if (\$isPdf) \$query->take(300); // Prevent PDF Out Of Memory
        \$routes = \$query->get();
        
        \$data = [];
        foreach(\$routes as \$r) {
            \$data[] = [
                'origin_country' => \$r->originCountry->country_name ?? 'N/A',
                'destination_country' => \$r->destinationCountry->country_name ?? 'N/A',
                'origin_port' => \$r->originPort->port_name ?? 'N/A',
                'destination_port' => \$r->destinationPort->port_name ?? 'N/A',
                'cargo_type' => \$r->cargo_type ?? 'Container',
                'container_size' => \$r->container_size ?? '40ft',
                'distance' => \$r->estimated_distance . ' km',
                'eta' => \$r->estimated_duration . ' Days',
                'weather_impact' => \$r->weather_impact ?? 'N/A',
                'currency_impact' => \$r->currency_impact ?? 'N/A',
                'trade_risk' => \$r->risk_level ?? 'N/A',
                'ai_recommendation' => \$r->ai_recommendation,
                'simulation_date' => \$r->created_at ? \$r->created_at->format('Y-m-d') : 'N/A'
            ];
        }
        return \$data;
    }

    public function tradePlannerView() {
        \$data = \$this->getTradePlannerData(true);
        return view('reports.exports.trade-planner', compact('data'));
    }

    public function tradePlannerPdf() {
        \$data = \$this->getTradePlannerData(true);
        \$pdf = Pdf::loadView('reports.exports.trade-planner', ['data' => \$data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return \$pdf->download('trade-planner-report.pdf');
    }

    public function tradePlannerExcel() {
        \$data = \$this->getTradePlannerData(false);
        return Excel::download(new \App\Exports\TradePlannerExport(\$data), 'trade-planner-report.xlsx');
    }

    public function singleSimulationPdf(\$id) {
        \$r = \App\Models\TradeSimulation::with(['originCountry', 'destinationCountry', 'originPort', 'destinationPort'])
            ->where('user_id', auth()->id())
            ->findOrFail(\$id);

        \$data = [[
            'origin_country' => \$r->originCountry->country_name ?? 'N/A',
            'destination_country' => \$r->destinationCountry->country_name ?? 'N/A',
            'origin_port' => \$r->originPort->port_name ?? 'N/A',
            'destination_port' => \$r->destinationPort->port_name ?? 'N/A',
            'cargo_type' => \$r->cargo_type ?? 'Container',
            'container_size' => \$r->container_size ?? '40ft',
            'distance' => \$r->estimated_distance . ' km',
            'eta' => \$r->estimated_duration . ' Days',
            'weather_impact' => \$r->weather_impact ?? 'N/A',
            'currency_impact' => \$r->currency_impact ?? 'N/A',
            'trade_risk' => \$r->risk_level ?? 'N/A',
            'ai_recommendation' => \$r->ai_recommendation,
            'simulation_date' => \$r->created_at ? \$r->created_at->format('Y-m-d') : 'N/A'
        ]];

        \$pdf = Pdf::loadView('reports.exports.trade-planner', ['data' => \$data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return \$pdf->download('trade-simulation-' . \$r->id . '.pdf');
    }

PHP;

// 2. Risk Analysis
$methods .= <<<PHP
    // ==========================================
    // 2. Risk Analysis Report
    // ==========================================
    private function getRiskAnalysisData(\$isPdf = false)
    {
        \$query = RiskScore::with('country');
        if (\$isPdf) \$query->take(300);
        \$scores = \$query->get();
        \$data = [];
        foreach(\$scores as \$s) {
            \$data[] = [
                'country' => \$s->country->country_name ?? 'N/A',
                'risk_score' => \$s->final_score,
                'risk_level' => \$s->final_score > 70 ? 'High' : (\$s->final_score > 40 ? 'Medium' : 'Low'),
                'weather_risk' => 'Low',
                'currency_risk' => 'Medium',
                'economy_risk' => 'Low',
                'political_risk' => 'Medium',
                'overall_recommendation' => 'Proceed with Caution'
            ];
        }
        return \$data;
    }

    public function riskAnalysisView() {
        \$data = \$this->getRiskAnalysisData(true);
        return view('reports.exports.risk-analysis', compact('data'));
    }

    public function riskAnalysisPdf() {
        \$data = \$this->getRiskAnalysisData(true);
        \$pdf = Pdf::loadView('reports.exports.risk-analysis', ['data' => \$data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return \$pdf->download('risk-analysis-report.pdf');
    }

    public function riskAnalysisExcel() {
        \$data = \$this->getRiskAnalysisData(false);
        return Excel::download(new \App\Exports\RiskAnalysisExport(\$data), 'risk-analysis-report.xlsx');
    }

PHP;

// 3. Countries
$methods .= <<<PHP
    // ==========================================
    // 3. Countries Report
    // ==========================================
    private function getCountriesData(\$isPdf = false)
    {
        \$query = Country::with('economicData');
        if (\$isPdf) \$query->take(300);
        \$countries = \$query->get();
        \$data = [];
        foreach(\$countries as \$c) {
            \$eco = \$c->economicData->first();
            \$data[] = [
                'country' => \$c->country_name,
                'capital' => \$c->capital ?? 'N/A',
                'region' => \$c->region,
                'currency' => \$c->currency_code,
                'population' => number_format(\$c->population),
                'gdp' => \$eco ? '$' . number_format(\$eco->gdp_value) : 'N/A',
                'inflation' => \$eco ? \$eco->inflation_rate . '%' : 'N/A',
                'export_value' => \$eco ? '$' . number_format(\$eco->export_value) : 'N/A',
                'import_value' => \$eco ? '$' . number_format(\$eco->import_value) : 'N/A'
            ];
        }
        return \$data;
    }

    public function countriesView() {
        \$data = \$this->getCountriesData(true);
        return view('reports.exports.countries', compact('data'));
    }

    public function countriesPdf() {
        \$data = \$this->getCountriesData(true);
        \$pdf = Pdf::loadView('reports.exports.countries', ['data' => \$data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return \$pdf->download('countries-report.pdf');
    }

    public function countriesExcel() {
        \$data = \$this->getCountriesData(false);
        return Excel::download(new \App\Exports\CountriesExport(\$data), 'countries-report.xlsx');
    }

PHP;

// 4. Weather
$methods .= <<<PHP
    // ==========================================
    // 4. Weather Report
    // ==========================================
    private function getWeatherData(\$isPdf = false)
    {
        \$query = WeatherData::with('country');
        if (\$isPdf) \$query->take(300);
        \$weathers = \$query->get();
        \$data = [];
        foreach(\$weathers as \$w) {
            \$data[] = [
                'country' => \$w->country->country_name ?? 'N/A',
                'temperature' => \$w->temperature_celsius . '°C',
                'humidity' => \$w->humidity_percent . '%',
                'wind_speed' => \$w->wind_speed_kmh . ' km/h',
                'weather_status' => \$w->weather_condition,
                'storm_risk' => \$w->is_storm_warning ? 'High' : 'Low',
                'updated_at' => \$w->updated_at->format('Y-m-d H:i')
            ];
        }
        return \$data;
    }

    public function weatherView() {
        \$data = \$this->getWeatherData(true);
        return view('reports.exports.weather', compact('data'));
    }

    public function weatherPdf() {
        \$data = \$this->getWeatherData(true);
        \$pdf = Pdf::loadView('reports.exports.weather', ['data' => \$data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return \$pdf->download('weather-report.pdf');
    }

    public function weatherExcel() {
        \$data = \$this->getWeatherData(false);
        return Excel::download(new \App\Exports\WeatherExport(\$data), 'weather-report.xlsx');
    }

PHP;

// 5. Currency
$methods .= <<<PHP
    // ==========================================
    // 5. Currency Report
    // ==========================================
    private function getCurrencyData(\$isPdf = false)
    {
        \$query = CurrencyRate::with('country');
        if (\$isPdf) \$query->take(300);
        \$currencies = \$query->get();
        \$data = [];
        foreach(\$currencies as \$c) {
            \$data[] = [
                'country' => \$c->country->country_name ?? 'N/A',
                'currency' => \$c->currency_code,
                'exchange_rate' => \$c->exchange_rate_to_usd,
                'change_pct' => \$c->trend_percentage,
                'status' => \$c->trend_percentage < -2 ? 'Volatile' : (\$c->trend_percentage > 2 ? 'Warning' : 'Stable'),
                'updated_at' => \$c->updated_at->format('Y-m-d H:i')
            ];
        }
        return \$data;
    }

    public function currencyView() {
        \$data = \$this->getCurrencyData(true);
        return view('reports.exports.currency', compact('data'));
    }

    public function currencyPdf() {
        \$data = \$this->getCurrencyData(true);
        \$pdf = Pdf::loadView('reports.exports.currency', ['data' => \$data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return \$pdf->download('currency-report.pdf');
    }

    public function currencyExcel() {
        \$data = \$this->getCurrencyData(false);
        return Excel::download(new \App\Exports\CurrencyExport(\$data), 'currency-report.xlsx');
    }

PHP;

// 6. Economy
$methods .= <<<PHP
    // ==========================================
    // 6. Economy Report
    // ==========================================
    private function getEconomyData(\$isPdf = false)
    {
        \$query = EconomicData::with('country');
        if (\$isPdf) \$query->take(300);
        \$economies = \$query->get();
        \$data = [];
        foreach(\$economies as \$e) {
            \$data[] = [
                'country' => \$e->country->country_name ?? 'N/A',
                'gdp' => '$' . number_format(\$e->gdp_value),
                'inflation' => \$e->inflation_rate . '%',
                'unemployment' => \$e->unemployment_rate . '%',
                'export' => '$' . number_format(\$e->export_value),
                'import' => '$' . number_format(\$e->import_value),
                'economic_status' => \$e->inflation_rate > 10 ? 'Crisis' : (\$e->inflation_rate > 5 ? 'Warning' : 'Stable')
            ];
        }
        return \$data;
    }

    public function economyView() {
        \$data = \$this->getEconomyData(true);
        return view('reports.exports.economy', compact('data'));
    }

    public function economyPdf() {
        \$data = \$this->getEconomyData(true);
        \$pdf = Pdf::loadView('reports.exports.economy', ['data' => \$data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return \$pdf->download('economy-report.pdf');
    }

    public function economyExcel() {
        \$data = \$this->getEconomyData(false);
        return Excel::download(new \App\Exports\EconomyExport(\$data), 'economy-report.xlsx');
    }

PHP;

// 7. Ports
$methods .= <<<PHP
    // ==========================================
    // 7. Ports Report
    // ==========================================
    private function getPortsData(\$isPdf = false)
    {
        \$query = Port::with('country');
        if (\$isPdf) \$query->take(300);
        \$ports = \$query->get();
        \$data = [];
        foreach(\$ports as \$p) {
            \$data[] = [
                'port_name' => \$p->port_name,
                'country' => \$p->country->country_name ?? 'N/A',
                'capacity' => number_format(\$p->capacity_teu) . ' TEU',
                'active_ships' => \$p->active_ships,
                'congestion' => \$p->congestion_level,
                'operational_status' => \$p->operational_status,
                'risk' => \$p->congestion_level == 'High' ? 'High' : 'Low'
            ];
        }
        return \$data;
    }

    public function portsView() {
        \$data = \$this->getPortsData(true);
        return view('reports.exports.ports', compact('data'));
    }

    public function portsPdf() {
        \$data = \$this->getPortsData(true);
        \$pdf = Pdf::loadView('reports.exports.ports', ['data' => \$data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return \$pdf->download('ports-report.pdf');
    }

    public function portsExcel() {
        \$data = \$this->getPortsData(false);
        return Excel::download(new \App\Exports\PortsExport(\$data), 'ports-report.xlsx');
    }

PHP;

// 8. Watch List
$methods .= <<<PHP
    // ==========================================
    // 8. Watch List Report
    // ==========================================
    private function getWatchListData(\$isPdf = false)
    {
        \$query = WatchList::with('watchable');
        if (\$isPdf) \$query->take(300);
        \$watchlist = \$query->get();
        \$data = [];
        foreach(\$watchlist as \$w) {
            \$name = 'Unknown';
            if (\$w->watchable_type == \App\Models\Country::class) {
                \$name = \$w->watchable->country_name ?? 'Country';
            } elseif (\$w->watchable_type == \App\Models\Port::class) {
                \$name = \$w->watchable->port_name ?? 'Port';
            }
            \$data[] = [
                'watch_type' => class_basename(\$w->watchable_type),
                'name' => \$name,
                'current_risk' => 'Medium',
                'weather' => 'Clear',
                'currency' => 'Stable',
                'monitoring_status' => 'Active',
                'added_date' => \$w->created_at->format('Y-m-d')
            ];
        }
        return \$data;
    }

    public function watchListView() {
        \$data = \$this->getWatchListData(true);
        return view('reports.exports.watch-list', compact('data'));
    }

    public function watchListPdf() {
        \$data = \$this->getWatchListData(true);
        \$pdf = Pdf::loadView('reports.exports.watch-list', ['data' => \$data, 'isPdf' => true])->setPaper('a4', 'landscape');
        return \$pdf->download('watch-list-report.pdf');
    }

    public function watchListExcel() {
        \$data = \$this->getWatchListData(false);
        return Excel::download(new \App\Exports\WatchListExport(\$data), 'watch-list-report.xlsx');
    }

PHP;

$controllerCode = <<<PHP
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
    public function __construct()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(300);
    }
$methods
}
PHP;

file_put_contents(__DIR__ . '/app/Http/Controllers/ReportExportController.php', $controllerCode);
echo "ReportExportController generated with TradeSimulation logic!\n";
