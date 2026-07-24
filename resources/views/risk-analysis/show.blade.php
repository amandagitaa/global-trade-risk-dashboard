@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('risk-analysis.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Back to Risk Analysis
        </a>
        <h1 class="fw-bold mb-0">Risk Analysis: {{ $country->country_name }}</h1>
    </div>

    <!-- 1. RISK OVERVIEW -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <h5 class="text-muted">Country</h5>
                    <h3 class="fw-bold mt-3">{{ $country->country_name }}</h3>
                    @if($country->flag_url)
                        <img src="{{ $country->flag_url }}" alt="Flag" width="60" class="mt-2 border">
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <h5 class="text-muted">Overall Risk</h5>
                    <h2 class="fw-bold mt-3 
                        @if(($riskScore->final_score ?? 0) <= 20) text-success 
                        @elseif(($riskScore->final_score ?? 0) <= 40) text-primary 
                        @elseif(($riskScore->final_score ?? 0) <= 60) text-warning 
                        @elseif(($riskScore->final_score ?? 0) <= 80) text-orange 
                        @else text-danger @endif">
                        {{ $riskScore->final_score ?? 'N/A' }} <span class="fs-6 text-muted">/ 100</span>
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <h5 class="text-muted">Risk Level</h5>
                    <h3 class="fw-bold mt-3">
                        <span class="badge 
                            @if(($riskScore->risk_level ?? '') == 'safe') bg-success 
                            @elseif(($riskScore->risk_level ?? '') == 'stable') bg-primary 
                            @elseif(($riskScore->risk_level ?? '') == 'alert') bg-warning 
                            @elseif(($riskScore->risk_level ?? '') == 'dangerous') bg-orange 
                            @elseif(($riskScore->risk_level ?? '') == 'critical') bg-danger 
                            @else bg-secondary @endif">
                            {{ strtoupper($riskScore->risk_level ?? 'N/A') }}
                        </span>
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <h5 class="text-muted">Last Updated</h5>
                    <h4 class="fw-bold mt-3">{{ $riskScore ? $riskScore->updated_at->format('d M Y') : 'N/A' }}</h4>
                    <small class="text-muted">
                        @if(count($riskHistories) > 1)
                            @php
                                $latest = $riskHistories->last()->risk_score;
                                $previous = $riskHistories->reverse()->skip(1)->first()->risk_score ?? $latest;
                            @endphp
                            @if($latest > $previous)
                                <span class="text-danger"><i class="bi bi-arrow-up"></i> Increasing Trend</span>
                            @elseif($latest < $previous)
                                <span class="text-success"><i class="bi bi-arrow-down"></i> Decreasing Trend</span>
                            @else
                                <span class="text-secondary"><i class="bi bi-dash"></i> Stable Trend</span>
                            @endif
                        @else
                            No trend available
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- 2. RISK BREAKDOWN -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-header bg-white pb-0 border-0">
                    <h5 class="fw-bold mb-0">Risk Breakdown</h5>
                </div>
                <div class="card-body">
                    @if($riskScore)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Weather / Climate Risk</span>
                                <strong>{{ $riskScore->weather_score }}/100</strong>
                            </div>
                            <div class="progress mt-1" style="height: 10px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $riskScore->weather_score }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Logistics / Port Risk</span>
                                <strong>{{ $riskScore->port_score }}/100</strong>
                            </div>
                            <div class="progress mt-1" style="height: 10px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $riskScore->port_score }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Currency Risk</span>
                                <strong>{{ $riskScore->currency_score }}/100</strong>
                            </div>
                            <div class="progress mt-1" style="height: 10px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $riskScore->currency_score }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Economic Risk</span>
                                <strong>{{ $riskScore->economic_score }}/100</strong>
                            </div>
                            <div class="progress mt-1" style="height: 10px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $riskScore->economic_score }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>News Sentiment Risk</span>
                                <strong>{{ $riskScore->news_score }}/100</strong>
                            </div>
                            <div class="progress mt-1" style="height: 10px;">
                                <div class="progress-bar bg-secondary" role="progressbar" style="width: {{ $riskScore->news_score }}%"></div>
                            </div>
                        </div>
                    @else
                        <p class="text-muted">Data unavailable</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="row g-4 h-100">
                <!-- 3. KEY RISK DRIVERS -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-header bg-white pb-0 border-0">
                            <h5 class="fw-bold mb-0">Key Risk Drivers</h5>
                        </div>
                        <div class="card-body">
                            @if(count($drivers) > 0)
                                <ol class="ps-3 mb-0">
                                    @foreach($drivers as $driver)
                                        <li class="mb-3">
                                            <strong>{{ $driver['factor'] }}</strong><br>
                                            <span class="badge {{ $driver['impact'] == 'HIGH IMPACT' ? 'bg-danger' : 'bg-warning' }}">{{ $driver['impact'] }}</span>
                                        </li>
                                    @endforeach
                                </ol>
                            @else
                                <p class="text-muted">No significant risk drivers identified.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 4. TRADE IMPACT ANALYSIS -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-header bg-white pb-0 border-0">
                            <h5 class="fw-bold mb-0">Trade Impact Analysis</h5>
                        </div>
                        <div class="card-body">
                            @if(count($impacts) > 0)
                                @foreach($impacts as $impact)
                                    <div class="mb-3 border-bottom pb-2">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong>{{ $impact['risk'] }}</strong>
                                            <span class="badge {{ $impact['level'] == 'HIGH' ? 'bg-danger' : 'bg-warning' }}">{{ $impact['level'] }}</span>
                                        </div>
                                        <small class="text-muted">{{ $impact['reason'] }}</small>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No significant trade impact identified.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. RISK TREND & 7. RECOMMENDATION -->
    <div class="row g-4 mb-4">
        <!-- RISK TREND -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white pb-0 border-0">
                    <h5 class="fw-bold mb-0">Risk Trend (Last 30 Days)</h5>
                </div>
                <div class="card-body">
                    @if(count($riskHistories) > 0)
                        <div style="height: 300px; width: 100%;">
                            <canvas id="riskTrendChart"></canvas>
                        </div>
                    @else
                        <p class="text-muted">Historical data unavailable</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- 7. RECOMMENDATION -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white pb-0 border-0">
                    <h5 class="fw-bold mb-0">Recommendation</h5>
                </div>
                <div class="card-body">
                    @if($recommendation)
                        <div class="text-center mb-4">
                            <h4 class="fw-bold 
                                @if($recommendation->priority == 'Low') text-success 
                                @elseif($recommendation->priority == 'Medium') text-warning 
                                @else text-danger @endif">
                                {{ strtoupper($recommendation->trade_action) }}
                            </h4>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="fw-bold text-muted">Reason:</h6>
                            <p class="mb-0">{{ implode(', ', explode("\n", $recommendation->business_reason)) }}</p>
                        </div>

                        <div>
                            <h6 class="fw-bold text-muted">Suggested Actions:</h6>
                            <ul class="ps-3 mb-0">
                                @foreach(explode("\n", $recommendation->recommendation) as $rec)
                                    @if(trim($rec))
                                        <li>{{ trim($rec) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="text-muted">Recommendation unavailable</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 6. RELATED INTELLIGENCE -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white pb-0 border-0">
            <h5 class="fw-bold mb-0">Related Intelligence</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                
                <div class="col-md-3">
                    <div class="card border border-light bg-light h-100">
                        <div class="card-body">
                            <h6 class="fw-bold"><i class="bi bi-cloud-sun"></i> Weather Condition</h6>
                            @if($country->latestWeather)
                                <p class="mb-2">{{ $country->latestWeather->weather }} ({{ $country->latestWeather->temperature }}°C)</p>
                            @else
                                <p class="text-muted mb-2">Data unavailable</p>
                            @endif
                            <a href="{{ route('weather.show', $country->id) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border border-light bg-light h-100">
                        <div class="card-body">
                            <h6 class="fw-bold"><i class="bi bi-currency-exchange"></i> Currency Movement</h6>
                            @if($country->latestCurrency)
                                <p class="mb-2">1 USD = {{ $country->latestCurrency->rate }} ({{ $country->latestCurrency->change_percentage }}%)</p>
                            @else
                                <p class="text-muted mb-2">Data unavailable</p>
                            @endif
                            <a href="{{ route('currency.show', $country->id) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border border-light bg-light h-100">
                        <div class="card-body">
                            <h6 class="fw-bold"><i class="bi bi-graph-up"></i> Economic Indicators</h6>
                            @if($country->economicData)
                                <p class="mb-2">Inflation: {{ $country->economicData->inflation }}%</p>
                            @else
                                <p class="text-muted mb-2">Data unavailable</p>
                            @endif
                            <a href="{{ route('economy.show', $country->id) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border border-light bg-light h-100">
                        <div class="card-body">
                            <h6 class="fw-bold"><i class="bi bi-ship"></i> Port Condition</h6>
                            @if($country->ports->first())
                                <p class="mb-2">Logistics Risk: {{ $country->ports->first()->logistic_risk }} / 100</p>
                            @else
                                <p class="text-muted mb-2">Data unavailable</p>
                            @endif
                            <a href="{{ route('ports.index', ['country' => $country->name]) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
@if(count($riskHistories) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('riskTrendChart').getContext('2d');
        const labels = {!! json_encode($riskHistories->pluck('record_date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('M d'); })) !!};
        const data = {!! json_encode($riskHistories->pluck('risk_score')) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Overall Risk Score',
                    data: data,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endif
@endpush
