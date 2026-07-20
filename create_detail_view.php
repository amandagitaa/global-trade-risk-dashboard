<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\reports\country-comparison-detail.blade.php';

$bladeContent = <<<'EOD'
@extends('layouts.app')

@section('title', 'Country Comparison Report')

@section('content')
@php
    $res = $comp->comparison_result;
@endphp

<!-- Page Header -->
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <h2 class="fw-bold mb-1">Country Comparison Report</h2>
        <p class="text-muted mb-0">Detailed comparison analysis between two selected countries.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary rounded-pill px-4"><i class="bi bi-arrow-left me-2"></i> Back to Reports</a>
        <a href="{{ route('compare.pdf', $comp->id) }}" class="btn btn-danger rounded-pill px-4"><i class="bi bi-file-earmark-pdf me-2"></i> Export PDF</a>
        <a href="{{ route('compare.excel', $comp->id) }}" class="btn btn-success rounded-pill px-4"><i class="bi bi-file-earmark-excel me-2"></i> Export Excel</a>
    </div>
</div>

<!-- Summary Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="row text-center">
            <div class="col-md-3 border-end">
                <small class="text-muted fw-bold text-uppercase">Country A</small>
                <h4 class="fw-bold mt-2 text-primary">{{ $comp->countryA->country_name ?? 'N/A' }}</h4>
            </div>
            <div class="col-md-3 border-end">
                <small class="text-muted fw-bold text-uppercase">Country B</small>
                <h4 class="fw-bold mt-2 text-primary">{{ $comp->countryB->country_name ?? 'N/A' }}</h4>
            </div>
            <div class="col-md-3 border-end">
                <small class="text-muted fw-bold text-uppercase">Comparison Date</small>
                <h5 class="fw-bold mt-2 text-dark">{{ $comp->created_at->format('d M Y, H:i') }}</h5>
            </div>
            <div class="col-md-3">
                <small class="text-muted fw-bold text-uppercase">Trade Analyst</small>
                <h5 class="fw-bold mt-2 text-dark">{{ $comp->user->name ?? 'System' }}</h5>
            </div>
        </div>
    </div>
</div>

<!-- Comparison Sections -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <!-- Country A Column -->
        <h4 class="fw-bold text-center mb-4 text-primary">{{ $comp->countryA->country_name ?? 'N/A' }}</h4>
        
        <!-- Country Information -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 border-top border-4 border-primary">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-muted border-bottom pb-2">Country Information</h6>
                <div class="d-flex align-items-center mb-3">
                    <img src="https://flagcdn.com/w80/{{ $res['flag_a'] ?? 'xx' }}.png" width="40" class="shadow-sm rounded me-3" alt="Flag">
                    <h5 class="mb-0 fw-bold">{{ $comp->countryA->country_name ?? 'N/A' }}</h5>
                </div>
                <div class="row g-3">
                    <div class="col-6"><small class="text-muted">Capital</small><br><span class="fw-semibold">{{ $res['capital_a'] ?? '-' }}</span></div>
                    <div class="col-6"><small class="text-muted">Region</small><br><span class="fw-semibold">{{ $res['region_a'] ?? '-' }}</span></div>
                    <div class="col-6"><small class="text-muted">Population</small><br><span class="fw-semibold">{{ number_format($res['population_a'] ?? 0) }}</span></div>
                    <div class="col-6"><small class="text-muted">Currency</small><br><span class="fw-semibold">{{ $res['currency_code_a'] ?? '-' }}</span></div>
                    <div class="col-12"><small class="text-muted">Language</small><br><span class="fw-semibold">{{ $res['language_a'] ?? '-' }}</span></div>
                </div>
            </div>
        </div>

        <!-- Economy Comparison -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 border-top border-4 border-success">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-muted border-bottom pb-2">Economy Comparison</h6>
                <div class="row g-3">
                    <div class="col-6"><small class="text-muted">GDP</small><br><span class="fw-bold fs-5">${{ number_format(($res['gdp_a'] ?? 0) / 1000000000, 2) }}B</span></div>
                    <div class="col-6"><small class="text-muted">Inflation</small><br><span class="fw-bold fs-5">{{ number_format($res['inflation_a'] ?? 0, 1) }}%</span></div>
                    <div class="col-6"><small class="text-muted">Export</small><br><span class="fw-semibold">${{ number_format(($res['export_a'] ?? 0) / 1000000000, 2) }}B</span></div>
                    <div class="col-6"><small class="text-muted">Import</small><br><span class="fw-semibold">${{ number_format(($res['import_a'] ?? 0) / 1000000000, 2) }}B</span></div>
                    <div class="col-12"><small class="text-muted">GDP per Capita</small><br><span class="fw-semibold">${{ number_format($res['gdp_per_capita_a'] ?? 0, 2) }}</span></div>
                </div>
            </div>
        </div>

        <!-- Weather Comparison -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 border-top border-4 border-info">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-muted border-bottom pb-2">Weather Comparison</h6>
                <div class="row g-3">
                    <div class="col-6"><small class="text-muted">Temperature</small><br><span class="fw-semibold">{{ $res['temp_a'] ?? 0 }}°C</span></div>
                    <div class="col-6"><small class="text-muted">Rain</small><br><span class="fw-semibold">{{ $res['rain_a'] ?? 0 }} mm</span></div>
                    <div class="col-6"><small class="text-muted">Wind</small><br><span class="fw-semibold">{{ $res['wind_a'] ?? 0 }} km/h</span></div>
                    <div class="col-6"><small class="text-muted">Storm Risk</small><br><span class="fw-semibold">{{ $res['storm_a'] ?? 'None' }}</span></div>
                </div>
            </div>
        </div>

        <!-- Currency Comparison -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 border-top border-4 border-warning">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-muted border-bottom pb-2">Currency Comparison</h6>
                <div class="row g-3">
                    <div class="col-6"><small class="text-muted">Exchange Rate</small><br><span class="fw-semibold">{{ $res['exchange_a'] ?? 0 }}</span></div>
                    <div class="col-6"><small class="text-muted">Currency Status</small><br><span class="fw-semibold text-uppercase">{{ $res['currency_status_a'] ?? 'Unknown' }}</span></div>
                </div>
            </div>
        </div>

        <!-- Port Comparison -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 border-top border-4 border-secondary">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-muted border-bottom pb-2">Port Comparison</h6>
                <div class="row g-3">
                    <div class="col-6"><small class="text-muted">Major Ports</small><br><span class="fw-semibold">{{ $res['ports_a'] ?? 0 }}</span></div>
                    <div class="col-6"><small class="text-muted">Shipping Status</small><br><span class="fw-semibold">{{ $res['shipping_status_a'] ?? 'Normal' }}</span></div>
                    <div class="col-12"><small class="text-muted">Main Port</small><br><span class="fw-semibold">{{ $res['main_port_a'] ?? 'N/A' }}</span></div>
                </div>
            </div>
        </div>

        <!-- News Comparison -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 border-top border-4 border-dark">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-muted border-bottom pb-2">News Comparison</h6>
                <div class="row g-3">
                    <div class="col-12"><small class="text-muted">Latest Trade News</small><br><span class="fw-semibold">{{ $res['news_title_a'] ?? 'No recent news' }}</span></div>
                    <div class="col-12"><small class="text-muted">Sentiment</small><br><span class="badge bg-secondary">{{ number_format($res['news_sentiment_a'] ?? 0, 2) }}</span></div>
                </div>
            </div>
        </div>

        <!-- Risk Comparison -->
        <div class="card border-0 shadow-sm rounded-4 border-top border-4 border-danger">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-muted border-bottom pb-2">Risk Comparison</h6>
                <div class="row g-3">
                    <div class="col-6"><small class="text-muted">Weather Risk</small><br><span class="fw-semibold text-danger">{{ $res['risk_weather_a'] ?? 100 }}</span></div>
                    <div class="col-6"><small class="text-muted">Currency Risk</small><br><span class="fw-semibold text-danger">{{ $res['risk_currency_a'] ?? 100 }}</span></div>
                    <div class="col-6"><small class="text-muted">Inflation Risk</small><br><span class="fw-semibold text-danger">{{ $res['risk_inflation_a'] ?? 100 }}</span></div>
                    <div class="col-6"><small class="text-muted">Political Risk</small><br><span class="fw-semibold text-danger">{{ $res['risk_political_a'] ?? 100 }}</span></div>
                    <div class="col-12"><small class="text-muted">Overall Risk Score</small><br><span class="fw-bold fs-4 text-danger">{{ $comp->risk_score_a ?? 100 }}</span></div>
                </div>
            </div>
        </div>

    </div>

    <div class="col-md-6">
        <!-- Country B Column -->
        <h4 class="fw-bold text-center mb-4 text-primary">{{ $comp->countryB->country_name ?? 'N/A' }}</h4>

        <!-- Country Information B -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 border-top border-4 border-primary">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-muted border-bottom pb-2">Country Information</h6>
                <div class="d-flex align-items-center mb-3">
                    <img src="https://flagcdn.com/w80/{{ $res['flag_b'] ?? 'xx' }}.png" width="40" class="shadow-sm rounded me-3" alt="Flag">
                    <h5 class="mb-0 fw-bold">{{ $comp->countryB->country_name ?? 'N/A' }}</h5>
                </div>
                <div class="row g-3">
                    <div class="col-6"><small class="text-muted">Capital</small><br><span class="fw-semibold">{{ $res['capital_b'] ?? '-' }}</span></div>
                    <div class="col-6"><small class="text-muted">Region</small><br><span class="fw-semibold">{{ $res['region_b'] ?? '-' }}</span></div>
                    <div class="col-6"><small class="text-muted">Population</small><br><span class="fw-semibold">{{ number_format($res['population_b'] ?? 0) }}</span></div>
                    <div class="col-6"><small class="text-muted">Currency</small><br><span class="fw-semibold">{{ $res['currency_code_b'] ?? '-' }}</span></div>
                    <div class="col-12"><small class="text-muted">Language</small><br><span class="fw-semibold">{{ $res['language_b'] ?? '-' }}</span></div>
                </div>
            </div>
        </div>

        <!-- Economy Comparison B -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 border-top border-4 border-success">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-muted border-bottom pb-2">Economy Comparison</h6>
                <div class="row g-3">
                    <div class="col-6"><small class="text-muted">GDP</small><br><span class="fw-bold fs-5">${{ number_format(($res['gdp_b'] ?? 0) / 1000000000, 2) }}B</span></div>
                    <div class="col-6"><small class="text-muted">Inflation</small><br><span class="fw-bold fs-5">{{ number_format($res['inflation_b'] ?? 0, 1) }}%</span></div>
                    <div class="col-6"><small class="text-muted">Export</small><br><span class="fw-semibold">${{ number_format(($res['export_b'] ?? 0) / 1000000000, 2) }}B</span></div>
                    <div class="col-6"><small class="text-muted">Import</small><br><span class="fw-semibold">${{ number_format(($res['import_b'] ?? 0) / 1000000000, 2) }}B</span></div>
                    <div class="col-12"><small class="text-muted">GDP per Capita</small><br><span class="fw-semibold">${{ number_format($res['gdp_per_capita_b'] ?? 0, 2) }}</span></div>
                </div>
            </div>
        </div>

        <!-- Weather Comparison B -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 border-top border-4 border-info">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-muted border-bottom pb-2">Weather Comparison</h6>
                <div class="row g-3">
                    <div class="col-6"><small class="text-muted">Temperature</small><br><span class="fw-semibold">{{ $res['temp_b'] ?? 0 }}°C</span></div>
                    <div class="col-6"><small class="text-muted">Rain</small><br><span class="fw-semibold">{{ $res['rain_b'] ?? 0 }} mm</span></div>
                    <div class="col-6"><small class="text-muted">Wind</small><br><span class="fw-semibold">{{ $res['wind_b'] ?? 0 }} km/h</span></div>
                    <div class="col-6"><small class="text-muted">Storm Risk</small><br><span class="fw-semibold">{{ $res['storm_b'] ?? 'None' }}</span></div>
                </div>
            </div>
        </div>

        <!-- Currency Comparison B -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 border-top border-4 border-warning">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-muted border-bottom pb-2">Currency Comparison</h6>
                <div class="row g-3">
                    <div class="col-6"><small class="text-muted">Exchange Rate</small><br><span class="fw-semibold">{{ $res['exchange_b'] ?? 0 }}</span></div>
                    <div class="col-6"><small class="text-muted">Currency Status</small><br><span class="fw-semibold text-uppercase">{{ $res['currency_status_b'] ?? 'Unknown' }}</span></div>
                </div>
            </div>
        </div>

        <!-- Port Comparison B -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 border-top border-4 border-secondary">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-muted border-bottom pb-2">Port Comparison</h6>
                <div class="row g-3">
                    <div class="col-6"><small class="text-muted">Major Ports</small><br><span class="fw-semibold">{{ $res['ports_b'] ?? 0 }}</span></div>
                    <div class="col-6"><small class="text-muted">Shipping Status</small><br><span class="fw-semibold">{{ $res['shipping_status_b'] ?? 'Normal' }}</span></div>
                    <div class="col-12"><small class="text-muted">Main Port</small><br><span class="fw-semibold">{{ $res['main_port_b'] ?? 'N/A' }}</span></div>
                </div>
            </div>
        </div>

        <!-- News Comparison B -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 border-top border-4 border-dark">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-muted border-bottom pb-2">News Comparison</h6>
                <div class="row g-3">
                    <div class="col-12"><small class="text-muted">Latest Trade News</small><br><span class="fw-semibold">{{ $res['news_title_b'] ?? 'No recent news' }}</span></div>
                    <div class="col-12"><small class="text-muted">Sentiment</small><br><span class="badge bg-secondary">{{ number_format($res['news_sentiment_b'] ?? 0, 2) }}</span></div>
                </div>
            </div>
        </div>

        <!-- Risk Comparison B -->
        <div class="card border-0 shadow-sm rounded-4 border-top border-4 border-danger">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-muted border-bottom pb-2">Risk Comparison</h6>
                <div class="row g-3">
                    <div class="col-6"><small class="text-muted">Weather Risk</small><br><span class="fw-semibold text-danger">{{ $res['risk_weather_b'] ?? 100 }}</span></div>
                    <div class="col-6"><small class="text-muted">Currency Risk</small><br><span class="fw-semibold text-danger">{{ $res['risk_currency_b'] ?? 100 }}</span></div>
                    <div class="col-6"><small class="text-muted">Inflation Risk</small><br><span class="fw-semibold text-danger">{{ $res['risk_inflation_b'] ?? 100 }}</span></div>
                    <div class="col-6"><small class="text-muted">Political Risk</small><br><span class="fw-semibold text-danger">{{ $res['risk_political_b'] ?? 100 }}</span></div>
                    <div class="col-12"><small class="text-muted">Overall Risk Score</small><br><span class="fw-bold fs-4 text-danger">{{ $comp->risk_score_b ?? 100 }}</span></div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Trade Recommendation -->
<div class="card border-0 shadow-lg bg-orange text-white rounded-4 mb-5">
    <div class="card-body p-5 text-center">
        <h4 class="fw-bold mb-4">Final Trade Recommendation</h4>
        
        <h2 class="display-6 fw-bold mb-3">{{ $comp->recommended_country }}</h2>
        <p class="fs-5 mb-4">{{ $comp->recommendation }}</p>
        
        @if(isset($res['reasons']) && is_array($res['reasons']))
        <div class="row justify-content-center mt-4">
            @foreach($res['reasons'] as $reason)
                <div class="col-auto mb-2">
                    <span class="badge bg-white text-orange px-3 py-2 rounded-pill fs-6"><i class="bi bi-check-circle-fill me-2"></i>{{ $reason }}</span>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@endsection
EOD;

file_put_contents($file, $bladeContent);
echo "Blade template country-comparison-detail.blade.php created.\n";
