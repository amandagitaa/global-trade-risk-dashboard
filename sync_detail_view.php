<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\reports\country-comparison-detail.blade.php';

$bladeContent = <<<'EOD'
@extends('layouts.app')

@section('title', 'Country Comparison Detail')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <h2 class="fw-bold">Country Comparison Report</h2>
        <p class="text-muted">Historical snapshot of country comparison analysis.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
        <a href="{{ route('compare.history') }}" class="btn btn-outline-secondary rounded-pill px-4"><i class="bi bi-arrow-left me-2"></i> Back to History</a>
    </div>
</div>

@php
    $res = $comp->comparison_result;
@endphp

<!-- Section 2: Summary Cards -->
<div class="row mb-4">
    <!-- Country A Summary -->
    <div class="col-md-6 mb-4 mb-md-0">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-top border-4 border-primary">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ $res['flag_a'] ?? '' }}" width="60" class="rounded shadow-sm me-3" onerror="this.src='https://flagcdn.com/w80/{{ strtolower($res['country_code_a'] ?? 'xx') }}.png'">
                    <div>
                        <h3 class="fw-bold mb-0">{{ $res['country_name_a'] ?? '-' }}</h3>
                        <span class="text-muted">{{ $res['region_a'] ?? '-' }}</span>
                    </div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-6"><small class="text-muted d-block">Capital</small><strong>{{ $res['capital_a'] ?? '-' }}</strong></div>
                    <div class="col-6"><small class="text-muted d-block">Currency</small><strong>{{ $res['currency_name_a'] ?? '-' }} ({{ $res['currency_code_a'] ?? '-' }})</strong></div>
                </div>
                <div class="row mb-3">
                    <div class="col-6"><small class="text-muted d-block">Population</small><strong>{{ number_format((float)($res['population_a'] ?? 0)) }}</strong></div>
                    <div class="col-6"><small class="text-muted d-block">GDP</small><strong>{{ $res['formatted_gdp_a'] ?? '-' }}</strong></div>
                </div>
                <div class="row">
                    <div class="col-6"><small class="text-muted d-block">Inflation</small><strong>{{ number_format((float)($res['inflation_a'] ?? 0), 1) }}%</strong></div>
                    <div class="col-6">
                        <small class="text-muted d-block">Overall Risk</small>
                        @php
                            $riskA = $res['risk_level_a'] ?? 'Unknown';
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
                    <img src="{{ $res['flag_b'] ?? '' }}" width="60" class="rounded shadow-sm me-3" onerror="this.src='https://flagcdn.com/w80/{{ strtolower($res['country_code_b'] ?? 'xx') }}.png'">
                    <div>
                        <h3 class="fw-bold mb-0">{{ $res['country_name_b'] ?? '-' }}</h3>
                        <span class="text-muted">{{ $res['region_b'] ?? '-' }}</span>
                    </div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-6"><small class="text-muted d-block">Capital</small><strong>{{ $res['capital_b'] ?? '-' }}</strong></div>
                    <div class="col-6"><small class="text-muted d-block">Currency</small><strong>{{ $res['currency_name_b'] ?? '-' }} ({{ $res['currency_code_b'] ?? '-' }})</strong></div>
                </div>
                <div class="row mb-3">
                    <div class="col-6"><small class="text-muted d-block">Population</small><strong>{{ number_format((float)($res['population_b'] ?? 0)) }}</strong></div>
                    <div class="col-6"><small class="text-muted d-block">GDP</small><strong>{{ $res['formatted_gdp_b'] ?? '-' }}</strong></div>
                </div>
                <div class="row">
                    <div class="col-6"><small class="text-muted d-block">Inflation</small><strong>{{ number_format((float)($res['inflation_b'] ?? 0), 1) }}%</strong></div>
                    <div class="col-6">
                        <small class="text-muted d-block">Overall Risk</small>
                        @php
                            $riskB = $res['risk_level_b'] ?? 'Unknown';
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
                    $valGdpA = $res['gdp_a'] ?? 0;
                    $valGdpB = $res['gdp_b'] ?? 0;
                    $maxGdp = max($valGdpA, $valGdpB, 1);
                    $pctGdpA = ($valGdpA / $maxGdp) * 100;
                    $pctGdpB = ($valGdpB / $maxGdp) * 100;

                    $valInfA = $res['inflation_a'] ?? 0;
                    $valInfB = $res['inflation_b'] ?? 0;
                    $maxInf = max($valInfA, $valInfB, 1);
                    $pctInfA = ($valInfA / $maxInf) * 100;
                    $pctInfB = ($valInfB / $maxInf) * 100;

                    $valRiskA = $res['risk_final_a'] ?? 100;
                    $valRiskB = $res['risk_final_b'] ?? 100;
                @endphp

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="fw-bold text-primary">{{ $res['country_code_a'] ?? '-' }} GDP</small>
                        <small class="fw-bold text-warning">{{ $res['country_code_b'] ?? '-' }} GDP</small>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar bg-primary" style="width: {{ $pctGdpA }}%"></div>
                        <div class="progress-bar bg-warning" style="width: {{ $pctGdpB }}%"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="fw-bold text-primary">{{ $res['country_code_a'] ?? '-' }} Inflation</small>
                        <small class="fw-bold text-warning">{{ $res['country_code_b'] ?? '-' }} Inflation</small>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar bg-primary" style="width: {{ $pctInfA }}%"></div>
                        <div class="progress-bar bg-warning" style="width: {{ $pctInfB }}%"></div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="fw-bold text-primary">{{ $res['country_code_a'] ?? '-' }} Risk Score ({{ $valRiskA }})</small>
                        <small class="fw-bold text-warning">{{ $res['country_code_b'] ?? '-' }} Risk Score ({{ $valRiskB }})</small>
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
                <h6 class="text-primary">{{ $res['country_name_a'] ?? '-' }}</h6>
                <h2 class="fw-bold mb-0">{{ $res['temp_a'] ?? '-' }}°C</h2>
                <p class="text-muted">{{ $res['weather_status_a'] ?? '-' }}</p>
                <div class="d-flex justify-content-center gap-4 mt-2">
                    <div><small class="text-muted d-block">Wind</small><strong>{{ $res['wind_a'] ?? '-' }} km/h</strong></div>
                    <div><small class="text-muted d-block">Precip</small><strong>{{ $res['rain_a'] ?? '-' }}%</strong></div>
                </div>
            </div>
            <div class="col-6">
                <h6 class="text-warning">{{ $res['country_name_b'] ?? '-' }}</h6>
                <h2 class="fw-bold mb-0">{{ $res['temp_b'] ?? '-' }}°C</h2>
                <p class="text-muted">{{ $res['weather_status_b'] ?? '-' }}</p>
                <div class="d-flex justify-content-center gap-4 mt-2">
                    <div><small class="text-muted d-block">Wind</small><strong>{{ $res['wind_b'] ?? '-' }} km/h</strong></div>
                    <div><small class="text-muted d-block">Precip</small><strong>{{ $res['rain_b'] ?? '-' }}%</strong></div>
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
                            <th class="text-primary">{{ $res['country_code_a'] ?? '-' }}</th>
                            <th class="text-warning">{{ $res['country_code_b'] ?? '-' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Currency</td>
                            <td>{{ $res['currency_code_a'] ?? '-' }}</td>
                            <td>{{ $res['currency_code_b'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Exchange Rate (USD)</td>
                            <td>{{ $res['exchange_a'] ?? '-' }}</td>
                            <td>{{ $res['exchange_b'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>
                                <span class="badge {{ strtolower($res['currency_status_a'] ?? '') == 'stable' || strtolower($res['currency_status_a'] ?? '') == 'strong' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $res['currency_status_a'] ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="badge {{ strtolower($res['currency_status_b'] ?? '') == 'stable' || strtolower($res['currency_status_b'] ?? '') == 'strong' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $res['currency_status_b'] ?? '-' }}</span>
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
                            <th class="text-primary">{{ $res['country_code_a'] ?? '-' }}</th>
                            <th class="text-warning">{{ $res['country_code_b'] ?? '-' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>GDP</td>
                            <td>{{ $res['formatted_gdp_a'] ?? '-' }}</td>
                            <td>{{ $res['formatted_gdp_b'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Inflation</td>
                            <td>{{ number_format((float)($res['inflation_a'] ?? 0), 1) }}%</td>
                            <td>{{ number_format((float)($res['inflation_b'] ?? 0), 1) }}%</td>
                        </tr>
                        <tr>
                            <td>Exports</td>
                            <td>{{ $res['formatted_exports_a'] ?? '-' }}</td>
                            <td>{{ $res['formatted_exports_b'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Imports</td>
                            <td>{{ $res['formatted_imports_a'] ?? '-' }}</td>
                            <td>{{ $res['formatted_imports_b'] ?? '-' }}</td>
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
                            <th class="text-primary">{{ $res['country_code_a'] ?? '-' }}</th>
                            <th class="text-warning">{{ $res['country_code_b'] ?? '-' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Major Ports</td>
                            <td><strong>{{ $res['ports_count_a'] ?? 0 }}</strong></td>
                            <td><strong>{{ $res['ports_count_b'] ?? 0 }}</strong></td>
                        </tr>
                        <tr>
                            <td>Main Port</td>
                            <td>{{ $res['main_port_a'] ?? '-' }}</td>
                            <td>{{ $res['main_port_b'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Avg Port Risk</td>
                            <td>{{ $res['ports_avg_risk_a'] ?? '-' }}</td>
                            <td>{{ $res['ports_avg_risk_b'] ?? '-' }}</td>
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
                        <h6 class="text-primary mb-3">{{ $res['country_name_a'] ?? '-' }}</h6>
                        @forelse($res['news_list_a'] ?? [] as $news)
                            <div class="mb-2 pb-2 border-bottom">
                                <p class="mb-1 fw-bold small text-truncate" title="{{ $news['title'] }}">{{ $news['title'] }}</p>
                                <small class="text-muted">{{ isset($news['published_at']) ? \Carbon\Carbon::parse($news['published_at'])->format('d M') : '-' }} &bull; <span class="{{ isset($news['sentiment']) && strtolower($news['sentiment']) == 'positive' ? 'text-success' : (isset($news['sentiment']) && strtolower($news['sentiment']) == 'negative' ? 'text-danger' : 'text-muted') }}">{{ $news['sentiment'] ?? 'Unknown' }}</span></small>
                            </div>
                        @empty
                            <small class="text-muted">No trade news available.</small>
                        @endforelse
                    </div>
                    <div class="col-6 p-3">
                        <h6 class="text-warning mb-3">{{ $res['country_name_b'] ?? '-' }}</h6>
                        @forelse($res['news_list_b'] ?? [] as $news)
                            <div class="mb-2 pb-2 border-bottom">
                                <p class="mb-1 fw-bold small text-truncate" title="{{ $news['title'] }}">{{ $news['title'] }}</p>
                                <small class="text-muted">{{ isset($news['published_at']) ? \Carbon\Carbon::parse($news['published_at'])->format('d M') : '-' }} &bull; <span class="{{ isset($news['sentiment']) && strtolower($news['sentiment']) == 'positive' ? 'text-success' : (isset($news['sentiment']) && strtolower($news['sentiment']) == 'negative' ? 'text-danger' : 'text-muted') }}">{{ $news['sentiment'] ?? 'Unknown' }}</span></small>
                            </div>
                        @empty
                            <small class="text-muted">No trade news available.</small>
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
                    <th class="text-primary">{{ $res['country_name_a'] ?? '-' }}</th>
                    <th class="text-warning">{{ $res['country_name_b'] ?? '-' }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $indicators = [
                        ['Weather Risk', 'risk_weather_a', 'risk_weather_b'],
                        ['Currency Risk', 'risk_currency_a', 'risk_currency_b'],
                        ['Economic Risk', 'risk_economic_a', 'risk_economic_b'],
                        ['News Risk', 'risk_news_a', 'risk_news_b'],
                        ['Overall Risk', 'risk_final_a', 'risk_final_b']
                    ];
                    
                    function getRiskBadge($val) {
                        if ($val === '-' || $val === null) return '-';
                        if ($val < 40) return '<span class="badge bg-success">'.$val.' (Low)</span>';
                        if ($val < 70) return '<span class="badge bg-warning text-dark">'.$val.' (Med)</span>';
                        return '<span class="badge bg-danger">'.$val.' (High)</span>';
                    }
                @endphp
                
                @foreach($indicators as $ind)
                <tr>
                    <td>{{ $ind[0] }}</td>
                    <td>{!! getRiskBadge($res[$ind[1]] ?? '-') !!}</td>
                    <td>{!! getRiskBadge($res[$ind[2]] ?? '-') !!}</td>
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
        
        @if(!empty($res['winner_name']))
            <h1 class="display-5 fw-bold text-dark mb-4">
                <img src="{{ $res['winner_flag'] ?? '' }}" width="50" class="me-2 rounded shadow-sm" onerror="this.style.display='none'">
                {{ $res['winner_name'] }}
            </h1>
            
            <div class="d-flex justify-content-center flex-wrap gap-3 mb-4">
                @foreach($res['reasons'] ?? [] as $reason)
                    <span class="badge bg-white text-orange border border-orange p-2 px-3 rounded-pill">
                        <i class="bi bi-check-circle-fill me-1"></i> {{ $reason }}
                    </span>
                @endforeach
            </div>
            
            <p class="fs-5 text-dark">{{ $res['conclusion'] ?? '' }}</p>
        @else
            <h4 class="text-muted">Insufficient data for automated recommendation.</h4>
        @endif
    </div>
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
                        label: '{{ $res["country_name_a"] ?? "Country A" }}',
                        data: [
                            100 - {{ $res['risk_economic_a'] ?? 50 }},
                            100 - {{ $res['risk_weather_a'] ?? 50 }},
                            100 - {{ $res['risk_currency_a'] ?? 50 }},
                            100 - {{ $res['risk_news_a'] ?? 50 }},
                            100 - {{ $res['risk_port_a'] ?? 50 }},
                            100 - {{ $res['risk_final_a'] ?? 50 }}
                        ],
                        backgroundColor: 'rgba(13, 110, 253, 0.2)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        pointBackgroundColor: 'rgba(13, 110, 253, 1)'
                    },
                    {
                        label: '{{ $res["country_name_b"] ?? "Country B" }}',
                        data: [
                            100 - {{ $res['risk_economic_b'] ?? 50 }},
                            100 - {{ $res['risk_weather_b'] ?? 50 }},
                            100 - {{ $res['risk_currency_b'] ?? 50 }},
                            100 - {{ $res['risk_news_b'] ?? 50 }},
                            100 - {{ $res['risk_port_b'] ?? 50 }},
                            100 - {{ $res['risk_final_b'] ?? 50 }}
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
@endsection
EOD;

file_put_contents($file, $bladeContent);
echo "Detail view perfectly synchronized with Compare view.\n";
