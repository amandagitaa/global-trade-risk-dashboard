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
.marker-highlight {
    background-color: #dc3545;
    border: 2px solid white;
    border-radius: 50%;
    animation: bounce 1s infinite alternate;
}
@keyframes bounce {
    0% { transform: translateY(0); }
    100% { transform: translateY(-10px); }
}
.marker-dimmed {
    opacity: 0.3 !important;
}
.marker-safe {
    filter: hue-rotate(287deg) saturate(2);
}
.marker-alert {
    filter: hue-rotate(200deg) saturate(3) brightness(1.3);
}
.marker-dangerous {
    filter: hue-rotate(160deg) saturate(3) brightness(1.2);
}
.marker-critical {
    filter: hue-rotate(146deg) saturate(3) brightness(0.9);
}
</style>
<script>
let globalTradeMap;
let markerLayerGroup;

window.updateMapMarkers = function(countries, selectedCountryId = null) {
    if (!globalTradeMap) return;
    
    document.getElementById('map-country-count').textContent = countries.length + ' Countries';

    markerLayerGroup.clearLayers();

    let targetLat = null;
    let targetLng = null;

    countries.forEach(country => {
        // Must use exact database coordinates
        if (!country.latitude || !country.longitude) return;
        
        let lat = parseFloat(country.latitude);
        let lng = parseFloat(country.longitude);

        let risk = "Stable";
        let score = "-";
        
        if (country.latest_risk) {
            risk = country.latest_risk.risk_level;
            score = country.latest_risk.final_score;
        }

        let recommendation = country.recommendation ? country.recommendation.trade_action : "-";
        let weather = country.latest_weather ? country.latest_weather.weather_status : "-";

        let isSelected = selectedCountryId && parseInt(country.id) === parseInt(selectedCountryId);
        
        // Exact coordinate without offset or jitter
        let coordinate = [lat, lng];

        if (isSelected) {
            targetLat = lat;
            targetLng = lng;
            
            // Highlighted bouncing marker using exact coordinates
            let highlightIcon = L.divIcon({
                className: 'marker-highlight',
                iconSize: [20, 20],
                iconAnchor: [10, 10], // Centered exactly at coordinate
                popupAnchor: [0, -10]
            });

            L.marker(coordinate, {
                icon: highlightIcon,
                zIndexOffset: 1000 // bring to front
            })
            .bindPopup(buildPopup(country, risk, score, weather, recommendation))
            .addTo(markerLayerGroup);

        } else {
            // Standard marker using exact coordinates
            let defaultIcon = new L.Icon.Default();
            let markerClass = '';
            
            let riskLower = risk.toLowerCase();
            if(riskLower === 'safe') markerClass = 'marker-safe';
            else if(riskLower === 'alert') markerClass = 'marker-alert';
            else if(riskLower === 'dangerous') markerClass = 'marker-dangerous';
            else if(riskLower === 'critical') markerClass = 'marker-critical';
            
            if (selectedCountryId) {
                markerClass += (markerClass ? ' ' : '') + 'marker-dimmed';
            }
            
            if (markerClass) {
                defaultIcon.options.className = markerClass;
            }
            
            L.marker(coordinate, {
                icon: defaultIcon
            })
            .bindPopup(buildPopup(country, risk, score, weather, recommendation))
            .addTo(markerLayerGroup);
        }
    });

    if (targetLat !== null && targetLng !== null) {
        // Use flyTo for exact positioning
        globalTradeMap.flyTo([targetLat, targetLng], 5, {
            animate: true,
            duration: 1.5
        });
    } else {
        globalTradeMap.flyTo([20, 0], 2);
    }
};

function buildPopup(country, risk, score, weather, recommendation) {
    return `
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
    `;
}

document.addEventListener('DOMContentLoaded', () => {
    globalTradeMap = L.map('worldMap', {
        zoomControl: true
    }).setView([20, 0], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(globalTradeMap);

    markerLayerGroup = L.layerGroup().addTo(globalTradeMap);

    const initialCountries = @json($mapCountries);
    const urlParams = new URLSearchParams(window.location.search);
    const initialSelected = urlParams.get('country_id') || urlParams.get('search_country');
    
    window.updateMapMarkers(initialCountries, initialSelected);
});
</script>
@endpush