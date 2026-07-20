<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\dashboard\components\world-map.blade.php';
$content = <<<'EOD'
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-orange mb-1">
                    🌍 Global Trade Risk Map
                </h5>
                <small class="text-muted">
                    Real-time global trade monitoring
                </small>
            </div>
            <span class="badge bg-warning text-dark" id="map-country-count">
                {{ count($mapCountries) }} Countries
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        <div
            id="worldMap"
            style="height:380px;border-radius:0 0 20px 20px;">
        </div>
    </div>
</div>

@push('scripts')
<style>
/* CSS class for map marker pulse */
.marker-pulse {
    animation: pulse 1.5s infinite;
}
@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.5); opacity: 0.8; }
    100% { transform: scale(1); opacity: 1; }
}
</style>
<script>
let globalTradeMap;
let markerLayerGroup;

window.updateMapMarkers = function(countries, selectedCountryId = null) {
    if (!globalTradeMap) return;
    
    // Update badge count
    document.getElementById('map-country-count').textContent = countries.length + ' Countries';

    // Clear existing markers
    markerLayerGroup.clearLayers();

    let targetLat = null;
    let targetLng = null;

    countries.forEach(country => {
        if (!country.latitude || !country.longitude) return;

        let color = "#0d6efd";
        let risk = "Stable";
        let score = "-";
        
        if (country.latest_risk) {
            risk = country.latest_risk.risk_level;
            score = country.latest_risk.final_score;
            switch(risk) {
                case 'safe': color = "#28a745"; break;
                case 'stable': color = "#0d6efd"; break;
                case 'alert': color = "#ffc107"; break;
                case 'dangerous': color = "#ff8c00"; break;
                case 'critical': color = "#dc3545"; break;
            }
        }

        let recommendation = country.recommendation ? country.recommendation.trade_action : "-";
        let weather = country.latest_weather ? country.latest_weather.weather_status : "-";

        let isSelected = selectedCountryId && parseInt(country.id) === parseInt(selectedCountryId);
        let opacity = 1;
        let radius = 7;
        let className = "";

        if (selectedCountryId) {
            if (isSelected) {
                targetLat = country.latitude;
                targetLng = country.longitude;
                opacity = 1;
                radius = 10;
                className = "marker-pulse";
            } else {
                opacity = 0.2;
            }
        }

        L.circleMarker([country.latitude, country.longitude], {
            radius: radius,
            color: color,
            fillColor: color,
            fillOpacity: opacity,
            opacity: opacity,
            weight: 2,
            className: className
        })
        .bindPopup(`
            <div style="width:240px">
                <div class="text-center">
                    <img src="${country.flag}" width="50" class="mb-2">
                    <h6><strong>${country.country_name}</strong></h6>
                </div>
                <table class="table table-sm table-borderless">
                    <tr><td><strong>Region</strong></td><td>${country.region}</td></tr>
                    <tr><td><strong>Risk</strong></td><td>${risk}</td></tr>
                    <tr><td><strong>Score</strong></td><td>${score}</td></tr>
                    <tr><td><strong>Weather</strong></td><td>${weather}</td></tr>
                    <tr><td><strong>Currency</strong></td><td>${country.currency_code}</td></tr>
                    <tr><td><strong>Action</strong></td><td>${recommendation}</td></tr>
                </table>
            </div>
        `)
        .addTo(markerLayerGroup);
    });

    if (targetLat && targetLng) {
        globalTradeMap.setView([targetLat, targetLng], 5);
    } else {
        globalTradeMap.setView([20, 0], 2);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    globalTradeMap = L.map('worldMap', {
        zoomControl: true
    }).setView([20, 0], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(globalTradeMap);

    markerLayerGroup = L.layerGroup().addTo(globalTradeMap);

    const initialCountries = @json($mapCountries);
    // Determine selected country if page loaded with ?country_id=
    const urlParams = new URLSearchParams(window.location.search);
    const initialSelected = urlParams.get('country_id') || urlParams.get('search_country');
    
    window.updateMapMarkers(initialCountries, initialSelected);
});
</script>
@endpush
EOD;

file_put_contents($file, $content);
echo "world-map.blade.php updated.\n";
