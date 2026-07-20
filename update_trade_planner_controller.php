<?php

$content = file_get_contents('C:\laragon\www\global-trade-risk-dashboard\app\Http\Controllers\TradePlannerController.php');

$useStatements = "use App\Models\TradeSimulation;\nuse Illuminate\Support\Facades\Auth;\n";
$content = str_replace("use App\Models\WeatherData;\n", "use App\Models\WeatherData;\n" . $useStatements, $content);

$saveLogic = <<<PHP
            // Save to TradeSimulation if authenticated
            if (Auth::check()) {
                TradeSimulation::create([
                    'user_id' => Auth::id(),
                    'origin_country_id' => \$originPort->country_id,
                    'destination_country_id' => \$destinationPort->country_id,
                    'origin_port_id' => \$originPort->id,
                    'destination_port_id' => \$destinationPort->id,
                    'cargo_type' => \$request->input('cargo_type', 'General'),
                    'container_size' => \$request->input('container_size', '20 FT'),
                    'departure_date' => \$request->input('departure_date', date('Y-m-d')),
                    'estimated_distance' => round(\$distance),
                    'estimated_duration' => \$finalEta,
                    'weather_impact' => \$weatherStatus,
                    'currency_impact' => \$costIndicator,
                    'risk_score' => \$totalRiskScore,
                    'risk_level' => \$tradeRisk,
                    'ai_recommendation' => \$recommendation['status'] . ' - ' . \$recommendation['reason']
                ]);
            }
PHP;

$content = str_replace("\$simulation = [", $saveLogic . "\n\n            \$simulation = [", $content);

$historyLogic = <<<PHP
        \$history = collect();
        if (Auth::check()) {
            \$history = TradeSimulation::with(['originPort.country', 'destinationPort.country'])
                ->where('user_id', Auth::id())
                ->latest()
                ->get();
        }

        return view('trade-planner.index', compact('countries', 'simulation', 'history'));
PHP;

$content = str_replace("return view('trade-planner.index', compact('countries', 'simulation'));", $historyLogic, $content);

file_put_contents('C:\laragon\www\global-trade-risk-dashboard\app\Http\Controllers\TradePlannerController.php', $content);
echo "TradePlannerController updated.\n";
