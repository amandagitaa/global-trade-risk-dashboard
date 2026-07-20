@extends('layouts.app')

@section('title','Dashboard')

@section('content')

<div class="row mb-4">
    <div class="col-lg-12">
        <h2 class="fw-bold">
            Welcome 👋
        </h2>
        <p class="text-muted mb-0">
            Monitor global trade risks, weather, economy, logistics and international trade recommendations.
        </p>
    </div>
</div>

<div id="dashboard-summary-cards">
    @include('dashboard.components.summary-cards')
</div>

<div class="row mb-4">
    <div class="col-lg-8" id="dashboard-world-map">
        @include('dashboard.components.world-map')
    </div>
    <div class="col-lg-4 mt-4 mt-lg-0" id="dashboard-risk-chart">
        @include('dashboard.components.risk-chart')
    </div>
</div>

<div class="row">
    <div class="col-12" id="dashboard-highest-risk">
        @include('dashboard.components.highest-risk')
    </div>
</div>

<div class="row mt-4">
    <div class="col-12" id="dashboard-port-risk-panel">
        @include('dashboard.components.port-risk-panel')
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-6" id="dashboard-weather-panel">
        @include('dashboard.components.weather-panel')
    </div>
    <div class="col-lg-6 mt-4 mt-lg-0" id="dashboard-currency-panel">
        @include('dashboard.components.currency-panel')
    </div>
</div>

<div class="row mt-4">
    <div class="col-12" id="dashboard-news-panel">
        @include('dashboard.components.news-panel')
    </div>
</div>

@endsection

@push('scripts')
<script>
window.fetchDashboard = function(countryId) {
    let url = '{{ route("dashboard") }}';
    if (countryId) {
        url += '?country_id=' + countryId;
    }
    
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('dashboard-summary-cards').innerHTML = data.summary_html;
        document.getElementById('dashboard-highest-risk').innerHTML = data.highest_risk_html;
        document.getElementById('dashboard-port-risk-panel').innerHTML = data.port_risk_html;
        document.getElementById('dashboard-weather-panel').innerHTML = data.weather_html;
        document.getElementById('dashboard-currency-panel').innerHTML = data.currency_html;
        
        document.getElementById('dashboard-news-panel').innerHTML = data.news_html;
        
        // Update Map Markers (Assuming updateMapMarkers is globally available in world-map view)
        if (typeof window.updateMapMarkers === 'function' && data.map_data) {
            window.updateMapMarkers(data.map_data, data.selected_country_id);
        }

        // Update Chart Data (Assuming updateRiskChart is globally available in risk-chart view)
        if (typeof window.updateRiskChart === 'function' && data.chart_data) {
            window.updateRiskChart(data.chart_data);
        }
    })
    .catch(err => console.error("Error updating dashboard:", err));
};
</script>
@endpush