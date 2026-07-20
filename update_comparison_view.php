<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\country-comparison\index.blade.php';

$content = <<<'EOD'
@extends('layouts.app')

@section('title', 'Country Comparison')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold">Country Comparison</h2>
        <p class="text-muted">Compare two countries for better global trade decisions.</p>
    </div>
</div>

<!-- Section 1: Selector -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('country-comparison.index') }}" class="row align-items-center justify-content-center">
            <div class="col-md-4">
                <label class="form-label fw-bold">Country A</label>
                <select name="country_a" class="form-select form-select-lg shadow-sm" required>
                    <option value="">-- Select Country A --</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->id }}" {{ request('country_a') == $c->id ? 'selected' : '' }}>
                            {{ $c->country_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-1 text-center mt-4 mb-2 mb-md-0">
                <span class="badge bg-secondary rounded-pill px-3 py-2 fs-6">VS</span>
            </div>
            
            <div class="col-md-4">
                <label class="form-label fw-bold">Country B</label>
                <select name="country_b" class="form-select form-select-lg shadow-sm" required>
                    <option value="">-- Select Country B --</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->id }}" {{ request('country_b') == $c->id ? 'selected' : '' }}>
                            {{ $c->country_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3 mt-4 text-md-end text-center">
                <button type="submit" class="btn btn-orange text-white btn-lg px-4 shadow-sm w-100">
                    <i class="bi bi-arrow-left-right me-2"></i> Compare
                </button>
            </div>
        </form>
    </div>
</div>

@if($countryA && $countryB)
<!-- Section 2: Summary Cards -->
<div class="row mb-4">
    <!-- Country A Summary -->
    <div class="col-md-6 mb-4 mb-md-0">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-top border-4 border-primary">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ $countryA->flag }}" width="60" class="rounded shadow-sm me-3">
                    <div>
                        <h3 class="fw-bold mb-0">{{ $countryA->country_name }}</h3>
                        <span class="text-muted">{{ $countryA->region }}</span>
                    </div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-6"><small class="text-muted d-block">Capital</small><strong>{{ $countryA->capital ?? '-' }}</strong></div>
                    <div class="col-6"><small class="text-muted d-block">Currency</small><strong>{{ $countryA->currency_name ?? '-' }} ({{ $countryA->currency_code }})</strong></div>
                </div>
                <div class="row mb-3">
                    <div class="col-6"><small class="text-muted d-block">Population</small><strong>{{ number_format($countryA->population) }}</strong></div>
                    <div class="col-6"><small class="text-muted d-block">GDP</small><strong>{{ $countryA->economicData ? $countryA->economicData->formatted_gdp : '-' }}</strong></div>
                </div>
                <div class="row">
                    <div class="col-6"><small class="text-muted d-block">Inflation</small><strong>{{ $countryA->economicData ? number_format($countryA->economicData->inflation, 1) . '%' : '-' }}</strong></div>
                    <div class="col-6">
                        <small class="text-muted d-block">Overall Risk</small>
                        @php
                            $riskA = $countryA->latestRisk ? $countryA->latestRisk->risk_level : 'Unknown';
                            $badgeClass = match(strtolower($riskA)) {
                                'safe' => 'bg-success',
                                'stable' => 'bg-primary',
                                'alert' => 'bg-warning text-dark',
                                'dangerous' => 'bg-orange',
                                'critical' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($riskA) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Country B Summary -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-top border-4 border-warning">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ $countryB->flag }}" width="60" class="rounded shadow-sm me-3">
                    <div>
                        <h3 class="fw-bold mb-0">{{ $countryB->country_name }}</h3>
                        <span class="text-muted">{{ $countryB->region }}</span>
                    </div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-6"><small class="text-muted d-block">Capital</small><strong>{{ $countryB->capital ?? '-' }}</strong></div>
                    <div class="col-6"><small class="text-muted d-block">Currency</small><strong>{{ $countryB->currency_name ?? '-' }} ({{ $countryB->currency_code }})</strong></div>
                </div>
                <div class="row mb-3">
                    <div class="col-6"><small class="text-muted d-block">Population</small><strong>{{ number_format($countryB->population) }}</strong></div>
                    <div class="col-6"><small class="text-muted d-block">GDP</small><strong>{{ $countryB->economicData ? $countryB->economicData->formatted_gdp : '-' }}</strong></div>
                </div>
                <div class="row">
                    <div class="col-6"><small class="text-muted d-block">Inflation</small><strong>{{ $countryB->economicData ? number_format($countryB->economicData->inflation, 1) . '%' : '-' }}</strong></div>
                    <div class="col-6">
                        <small class="text-muted d-block">Overall Risk</small>
                        @php
                            $riskB = $countryB->latestRisk ? $countryB->latestRisk->risk_level : 'Unknown';
                            $badgeClassB = match(strtolower($riskB)) {
                                'safe' => 'bg-success',
                                'stable' => 'bg-primary',
                                'alert' => 'bg-warning text-dark',
                                'dangerous' => 'bg-orange',
                                'critical' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $badgeClassB }}">{{ ucfirst($riskB) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section 3 & 4: KPI and Radar Chart -->
<div class="row mb-4">
    <!-- KPI Progress -->
    <div class="col-lg-6 mb-4 mb-lg-0">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold mb-0">KPI Comparison</h5>
            </div>
            <div class="card-body">
                @php
                    $valGdpA = $countryA->economicData ? $countryA->economicData->gdp : 0;
                    $valGdpB = $countryB->economicData ? $countryB->economicData->gdp : 0;
                    $maxGdp = max($valGdpA, $valGdpB, 1);
                    $pctGdpA = ($valGdpA / $maxGdp) * 100;
                    $pctGdpB = ($valGdpB / $maxGdp) * 100;

                    $valInfA = $countryA->economicData ? $countryA->economicData->inflation : 0;
                    $valInfB = $countryB->economicData ? $countryB->economicData->inflation : 0;
                    $maxInf = max($valInfA, $valInfB, 1);
                    $pctInfA = ($valInfA / $maxInf) * 100;
                    $pctInfB = ($valInfB / $maxInf) * 100;

                    $valRiskA = $countryA->latestRisk ? $countryA->latestRisk->final_score : 100;
                    $valRiskB = $countryB->latestRisk ? $countryB->latestRisk->final_score : 100;
                @endphp

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="fw-bold text-primary">{{ $countryA->country_code }} GDP</small>
                        <small class="fw-bold text-warning">{{ $countryB->country_code }} GDP</small>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar bg-primary" style="width: {{ $pctGdpA }}%"></div>
                        <div class="progress-bar bg-warning" style="width: {{ $pctGdpB }}%"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="fw-bold text-primary">{{ $countryA->country_code }} Inflation</small>
                        <small class="fw-bold text-warning">{{ $countryB->country_code }} Inflation</small>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar bg-primary" style="width: {{ $pctInfA }}%"></div>
                        <div class="progress-bar bg-warning" style="width: {{ $pctInfB }}%"></div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="fw-bold text-primary">{{ $countryA->country_code }} Risk Score ({{ $valRiskA }})</small>
                        <small class="fw-bold text-warning">{{ $countryB->country_code }} Risk Score ({{ $valRiskB }})</small>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar bg-primary" style="width: {{ $valRiskA }}%"></div>
                        <div class="progress-bar bg-warning" style="width: {{ $valRiskB }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Radar Chart -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold mb-0">Radar Analysis</h5>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <canvas id="comparisonRadarChart" style="max-height:300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Section 5: Weather -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4">
        <h5 class="fw-bold mb-0"><i class="bi bi-cloud-sun text-orange me-2"></i> Weather Comparison</h5>
    </div>
    <div class="card-body">
        <div class="row text-center">
            <div class="col-6 border-end">
                <h6 class="text-primary">{{ $countryA->country_name }}</h6>
                <h2 class="fw-bold mb-0">{{ $countryA->latestWeather ? $countryA->latestWeather->temperature : '-' }}°C</h2>
                <p class="text-muted">{{ $countryA->latestWeather ? $countryA->latestWeather->weather_status : '-' }}</p>
                <div class="d-flex justify-content-center gap-4 mt-2">
                    <div><small class="text-muted d-block">Wind</small><strong>{{ $countryA->latestWeather ? $countryA->latestWeather->wind_speed . ' km/h' : '-' }}</strong></div>
                    <div><small class="text-muted d-block">Precip</small><strong>{{ $countryA->latestWeather ? $countryA->latestWeather->precipitation . '%' : '-' }}</strong></div>
                </div>
            </div>
            <div class="col-6">
                <h6 class="text-warning">{{ $countryB->country_name }}</h6>
                <h2 class="fw-bold mb-0">{{ $countryB->latestWeather ? $countryB->latestWeather->temperature : '-' }}°C</h2>
                <p class="text-muted">{{ $countryB->latestWeather ? $countryB->latestWeather->weather_status : '-' }}</p>
                <div class="d-flex justify-content-center gap-4 mt-2">
                    <div><small class="text-muted d-block">Wind</small><strong>{{ $countryB->latestWeather ? $countryB->latestWeather->wind_speed . ' km/h' : '-' }}</strong></div>
                    <div><small class="text-muted d-block">Precip</small><strong>{{ $countryB->latestWeather ? $countryB->latestWeather->precipitation . '%' : '-' }}</strong></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section 6 & 7: Currency & Economy -->
<div class="row mb-4">
    <div class="col-md-6 mb-4 mb-md-0">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-currency-exchange text-orange me-2"></i> Currency</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <thead>
                        <tr class="text-muted small">
                            <th>Indicator</th>
                            <th class="text-primary">{{ $countryA->country_code }}</th>
                            <th class="text-warning">{{ $countryB->country_code }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Currency</td>
                            <td>{{ $countryA->currency_code }}</td>
                            <td>{{ $countryB->currency_code }}</td>
                        </tr>
                        <tr>
                            <td>Exchange Rate (USD)</td>
                            <td>{{ $countryA->latestCurrency ? $countryA->latestCurrency->exchange_rate : '-' }}</td>
                            <td>{{ $countryB->latestCurrency ? $countryB->latestCurrency->exchange_rate : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>
                                @if($countryA->latestCurrency)
                                <span class="badge {{ strtolower($countryA->latestCurrency->status) == 'stable' || strtolower($countryA->latestCurrency->status) == 'strong' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $countryA->latestCurrency->status }}</span>
                                @else - @endif
                            </td>
                            <td>
                                @if($countryB->latestCurrency)
                                <span class="badge {{ strtolower($countryB->latestCurrency->status) == 'stable' || strtolower($countryB->latestCurrency->status) == 'strong' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $countryB->latestCurrency->status }}</span>
                                @else - @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-graph-up text-orange me-2"></i> Economy</h5>
            </div>
            <div class="card-body p-0 px-3">
                <table class="table table-borderless">
                    <thead>
                        <tr class="text-muted small">
                            <th>Indicator</th>
                            <th class="text-primary">{{ $countryA->country_code }}</th>
                            <th class="text-warning">{{ $countryB->country_code }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>GDP</td>
                            <td>{{ $countryA->economicData ? $countryA->economicData->formatted_gdp : '-' }}</td>
                            <td>{{ $countryB->economicData ? $countryB->economicData->formatted_gdp : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Inflation</td>
                            <td>{{ $countryA->economicData ? number_format($countryA->economicData->inflation, 1) . '%' : '-' }}</td>
                            <td>{{ $countryB->economicData ? number_format($countryB->economicData->inflation, 1) . '%' : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Exports</td>
                            <td>{{ $countryA->economicData ? $countryA->economicData->formatted_exports : '-' }}</td>
                            <td>{{ $countryB->economicData ? $countryB->economicData->formatted_exports : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Imports</td>
                            <td>{{ $countryA->economicData ? $countryA->economicData->formatted_imports : '-' }}</td>
                            <td>{{ $countryB->economicData ? $countryB->economicData->formatted_imports : '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Section 8 & 9: News and Ports -->
<div class="row mb-4">
    <!-- Ports -->
    <div class="col-md-5 mb-4 mb-md-0">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-geo-alt text-orange me-2"></i> Port Comparison</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <thead>
                        <tr class="text-muted small">
                            <th>Indicator</th>
                            <th class="text-primary">{{ $countryA->country_code }}</th>
                            <th class="text-warning">{{ $countryB->country_code }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Major Ports</td>
                            <td><strong>{{ $countryA->ports->count() }}</strong></td>
                            <td><strong>{{ $countryB->ports->count() }}</strong></td>
                        </tr>
                        <tr>
                            <td>Main Port</td>
                            <td>{{ $countryA->ports->first() ? $countryA->ports->first()->name : '-' }}</td>
                            <td>{{ $countryB->ports->first() ? $countryB->ports->first()->name : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Avg Port Risk</td>
                            <td>
                                @if($countryA->ports->count())
                                    {{ number_format($countryA->ports->avg('risk_score'), 0) }}
                                @else - @endif
                            </td>
                            <td>
                                @if($countryB->ports->count())
                                    {{ number_format($countryB->ports->avg('risk_score'), 0) }}
                                @else - @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- News -->
    <div class="col-md-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-newspaper text-orange me-2"></i> Latest Trade News</h5>
            </div>
            <div class="card-body p-0">
                <div class="row m-0">
                    <div class="col-6 p-3 border-end">
                        <h6 class="text-primary mb-3">{{ $countryA->country_name }}</h6>
                        @forelse($countryA->latestNews as $news)
                            <div class="mb-2 pb-2 border-bottom">
                                <p class="mb-1 fw-bold small text-truncate" title="{{ $news->title }}">{{ $news->title }}</p>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($news->published_at)->format('d M') }} &bull; <span class="{{ strtolower($news->sentiment) == 'positive' ? 'text-success' : (strtolower($news->sentiment) == 'negative' ? 'text-danger' : 'text-muted') }}">{{ $news->sentiment }}</span></small>
                            </div>
                        @empty
                            <small class="text-muted">No news available.</small>
                        @endforelse
                    </div>
                    <div class="col-6 p-3">
                        <h6 class="text-warning mb-3">{{ $countryB->country_name }}</h6>
                        @forelse($countryB->latestNews as $news)
                            <div class="mb-2 pb-2 border-bottom">
                                <p class="mb-1 fw-bold small text-truncate" title="{{ $news->title }}">{{ $news->title }}</p>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($news->published_at)->format('d M') }} &bull; <span class="{{ strtolower($news->sentiment) == 'positive' ? 'text-success' : (strtolower($news->sentiment) == 'negative' ? 'text-danger' : 'text-muted') }}">{{ $news->sentiment }}</span></small>
                            </div>
                        @empty
                            <small class="text-muted">No news available.</small>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section 10: Risk Comparison -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4">
        <h5 class="fw-bold mb-0"><i class="bi bi-shield-exclamation text-orange me-2"></i> Risk Comparison</h5>
    </div>
    <div class="card-body p-0 px-3 pb-3">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Risk Indicator</th>
                    <th class="text-primary">{{ $countryA->country_name }}</th>
                    <th class="text-warning">{{ $countryB->country_name }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $indicators = [
                        ['Weather Risk', 'weather_score'],
                        ['Currency Risk', 'currency_score'],
                        ['Economic Risk', 'economic_score'],
                        ['News Risk', 'news_score'],
                        ['Overall Risk', 'final_score']
                    ];
                    
                    function getRiskBadge($val) {
                        if ($val < 40) return '<span class="badge bg-success">'.$val.' (Low)</span>';
                        if ($val < 70) return '<span class="badge bg-warning text-dark">'.$val.' (Med)</span>';
                        return '<span class="badge bg-danger">'.$val.' (High)</span>';
                    }
                @endphp
                
                @foreach($indicators as $ind)
                <tr>
                    <td>{{ $ind[0] }}</td>
                    <td>
                        @if($countryA->latestRisk)
                            {!! getRiskBadge($countryA->latestRisk->{$ind[1]}) !!}
                        @else - @endif
                    </td>
                    <td>
                        @if($countryB->latestRisk)
                            {!! getRiskBadge($countryB->latestRisk->{$ind[1]}) !!}
                        @else - @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Section 11: Trade Recommendation -->
<div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-4 border-orange">
    <div class="card-body p-5 bg-soft-orange text-center rounded-4">
        <h3 class="fw-bold text-orange mb-2">Trade Recommendation</h3>
        <p class="text-muted mb-4">Based on Automated Decision Support System</p>
        
        @if($recommendation['winner'])
            <h1 class="display-5 fw-bold text-dark mb-4">
                <img src="{{ $recommendation['winner']->flag }}" width="50" class="me-2 rounded shadow-sm">
                {{ $recommendation['winner']->country_name }}
            </h1>
            
            <div class="d-flex justify-content-center flex-wrap gap-3 mb-4">
                @foreach($recommendation['reasons'] as $reason)
                    <span class="badge bg-white text-orange border border-orange p-2 px-3 rounded-pill">
                        <i class="bi bi-check-circle-fill me-1"></i> {{ $reason }}
                    </span>
                @endforeach
            </div>
            
            <p class="fs-5 text-dark">{{ $recommendation['conclusion'] }}</p>
        @else
            <h4 class="text-muted">Insufficient data for automated recommendation.</h4>
        @endif
    </div>
</div>

<!-- Section 12: Action Button -->
<div class="d-flex gap-2 justify-content-end mb-5">
    <button class="btn btn-outline-secondary px-4"><i class="bi bi-bookmark"></i> Save Comparison</button>
    <button class="btn btn-outline-danger px-4"><i class="bi bi-file-pdf"></i> Export PDF</button>
    <button class="btn btn-outline-success px-4"><i class="bi bi-file-excel"></i> Export Excel</button>
</div>

@push('scripts')
<style>
.bg-soft-orange { background-color: rgba(253, 126, 20, 0.05); }
.border-orange { border-color: #fd7e14 !important; }
.text-orange { color: #fd7e14 !important; }
.bg-orange { background-color: #fd7e14 !important; color: white; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('comparisonRadarChart');
    if(ctx) {
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['Economy', 'Weather', 'Currency', 'News', 'Ports', 'Overall Risk'],
                datasets: [
                    {
                        label: '{{ $countryA->country_name }}',
                        data: [
                            {{ $countryA->latestRisk ? 100 - $countryA->latestRisk->economic_score : 50 }},
                            {{ $countryA->latestRisk ? 100 - $countryA->latestRisk->weather_score : 50 }},
                            {{ $countryA->latestRisk ? 100 - $countryA->latestRisk->currency_score : 50 }},
                            {{ $countryA->latestRisk ? 100 - $countryA->latestRisk->news_score : 50 }},
                            {{ $countryA->latestRisk ? 100 - $countryA->latestRisk->port_score : 50 }},
                            {{ $countryA->latestRisk ? 100 - $countryA->latestRisk->final_score : 50 }}
                        ],
                        backgroundColor: 'rgba(13, 110, 253, 0.2)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        pointBackgroundColor: 'rgba(13, 110, 253, 1)'
                    },
                    {
                        label: '{{ $countryB->country_name }}',
                        data: [
                            {{ $countryB->latestRisk ? 100 - $countryB->latestRisk->economic_score : 50 }},
                            {{ $countryB->latestRisk ? 100 - $countryB->latestRisk->weather_score : 50 }},
                            {{ $countryB->latestRisk ? 100 - $countryB->latestRisk->currency_score : 50 }},
                            {{ $countryB->latestRisk ? 100 - $countryB->latestRisk->news_score : 50 }},
                            {{ $countryB->latestRisk ? 100 - $countryB->latestRisk->port_score : 50 }},
                            {{ $countryB->latestRisk ? 100 - $countryB->latestRisk->final_score : 50 }}
                        ],
                        backgroundColor: 'rgba(255, 193, 7, 0.2)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        pointBackgroundColor: 'rgba(255, 193, 7, 1)'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: { display: true },
                        suggestedMin: 0,
                        suggestedMax: 100
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endif
@endsection
EOD;

file_put_contents($file, $content);
echo "Country Comparison View Created.\n";
