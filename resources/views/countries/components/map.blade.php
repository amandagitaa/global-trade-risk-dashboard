<style>
.port-marker-animated {
    transition: width 0.2s ease, height 0.2s ease, margin 0.2s ease !important;
    filter: drop-shadow(0 6px 12px rgba(0,0,0,0.35));
}
.port-marker-animated:hover {
    width: 46px !important;
    height: 46px !important;
    margin-left: -23px !important;
    margin-top: -39px !important;
    z-index: 1000 !important;
}
@keyframes popupBounce {
    0% { margin-top: -34px; }
    50% { margin-top: -49px; }
    100% { margin-top: -34px; }
}
.port-marker-bounce {
    animation: popupBounce 0.3s ease-out 1 !important;
}
</style>
<div class="card border-0 shadow-sm rounded-4 w-100 d-flex flex-column">
    <div class="card-header bg-white border-0 pb-0">
        <h5 class="fw-bold text-orange">
            📍 Country Location
        </h5>
        <p class="text-muted small mb-0">Geographical location and logistics infrastructure.</p>
    </div>
    <div class="card-body d-flex flex-column">
        <div id="countryMap" class="flex-grow-1" style="min-height: 420px; width: 100%; border-radius: 8px; z-index: 1;"></div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof L === 'undefined') {
            console.error('Leaflet is not loaded!');
            return;
        }

        var map = L.map('countryMap');
        var bounds = [];

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var blueIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
        });
        
        var greenIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
        });

        var portsLayer = L.layerGroup().addTo(map);
        var shipsLayer = L.layerGroup().addTo(map);

        var portIcon = L.icon({
            iconUrl: '/images/map/port-marker.svg',
            iconSize: [40, 40],
            iconAnchor: [20, 34],
            popupAnchor: [0, -28],
            className: 'port-marker-animated'
        });

        // Custom Ship Icon using L.icon
        var shipIcon = L.icon({
            iconUrl: '/images/map/container-ship.svg',
            iconSize: [40, 40],
            iconAnchor: [20, 34],
            popupAnchor: [0, -28],
            className: 'port-marker-animated'
        });

        map.on('popupopen', function(e) {
            if(e.popup._source && e.popup._source.options.icon === shipIcon) {
                var iconEl = e.popup._source._icon;
                if(iconEl) {
                    iconEl.classList.add('port-marker-bounce');
                    setTimeout(() => iconEl.classList.remove('port-marker-bounce'), 300);
                }
            }
        });

        @if($country->latitude && $country->longitude)
            var latlng = [{{ $country->latitude }}, {{ $country->longitude }}];
            L.marker(latlng, {icon: blueIcon}).addTo(map)
                .bindPopup("<b>{{ addslashes($country->country_name) }}</b><br>Capital: {{ addslashes($country->capital) }}<br>Region: {{ addslashes($country->region) }}<br>Lat: {{ $country->latitude }}<br>Lng: {{ $country->longitude }}<br>Pop: {{ number_format($country->population) }}");
            bounds.push(latlng);
            
            // Render Capital marker slightly offset if they share the same coords
            var capLatlng = [{{ (float)$country->latitude + 0.1 }}, {{ (float)$country->longitude + 0.1 }}];
            L.marker(capLatlng, {icon: greenIcon}).addTo(map)
                .bindPopup("<b>Capital: {{ addslashes($country->capital) }}</b>");
            bounds.push(capLatlng);
        @endif

        @foreach($ports as $port)
            @if($port->latitude && $port->longitude)
                @php
                    // Dynamic query to count ships and routes safely without breaking the controller
                    $activeShipsCount = \App\Models\Ship::where('current_port_id', $port->id)->count();
                    $routesCount = \App\Models\ShippingRoute::where('origin_port_id', $port->id)->orWhere('destination_port_id', $port->id)->count();
                    $capacityDisplay = $port->teu_capacity ? number_format((float)$port->teu_capacity) . ' TEU' : 'N/A';
                @endphp
                var portLatlng = [{{ $port->latitude }}, {{ $port->longitude }}];
                var popupContent = "<b>{{ addslashes($port->name) }}</b><hr style='margin:5px 0'>" +
                                   "<b>Type:</b> {{ addslashes($port->port_type ?? 'Unknown') }}<br>" +
                                   "<b>Status:</b> {{ addslashes($port->status ?? 'Unknown') }}<br>" +
                                   "<b>Capacity:</b> {{ $capacityDisplay }}<br>" +
                                   "<b>Routes:</b> {{ $routesCount }}<br>" +
                                   "<b>Active Ships:</b> {{ $activeShipsCount }}";
                
                L.marker(portLatlng, {icon: portIcon, zIndexOffset: 100}).addTo(portsLayer)
                    .bindPopup(popupContent);
                bounds.push(portLatlng);
            @endif
        @endforeach

        @if(isset($activeShipModels))
            @foreach($activeShipModels as $ship)
                @php
                    $shipLat = $ship->latitude;
                    $shipLng = $ship->longitude;
                    if (!$shipLat || !$shipLng) {
                        $port = $ports->firstWhere('id', $ship->current_port_id);
                        if ($port) {
                            $shipLat = $port->latitude + 0.01 + (rand(-5,5) / 1000);
                            $shipLng = $port->longitude + 0.01 + (rand(-5,5) / 1000);
                        }
                    }
                @endphp
                @if($shipLat && $shipLng)
                    var shipLatlng = [{{ $shipLat }}, {{ $shipLng }}];
                    var shipPopup = "<b>{{ addslashes($ship->ship_name) }}</b><hr style='margin:5px 0'>" +
                                       "<b>IMO Number:</b> {{ addslashes($ship->imo_number ?? 'N/A') }}<br>" +
                                       "<b>Type:</b> {{ addslashes($ship->ship_type ?? 'Unknown') }}<br>" +
                                       "<b>Current Port:</b> {{ addslashes($ship->currentPort?->name ?? 'Unknown') }}<br>" +
                                       "<b>Destination:</b> {{ addslashes($ship->destinationPort?->name ?? 'Unknown') }}<br>" +
                                       "<b>Cargo:</b> {{ $ship->cargo_percentage ?? 0 }}%<br>" +
                                       "<b>ETA:</b> {{ $ship->eta_days ?? 'Unknown' }} Days<br>" +
                                       "<b>Status:</b> {{ addslashes($ship->status ?? 'Unknown') }}";
                    L.marker(shipLatlng, {icon: shipIcon, zIndexOffset: 1000}).addTo(shipsLayer)
                        .bindPopup(shipPopup);
                    bounds.push(shipLatlng);
                @endif
            @endforeach
        @endif

        var overlayMaps = {
            "⚓ Ports": portsLayer,
            "🚢 Active Ships": shipsLayer
        };
        L.control.layers(null, overlayMaps, {position: 'topright'}).addTo(map);

        if (bounds.length > 0) {
            map.fitBounds(bounds, {padding: [50, 50]});
        } else {
            map.setView([0,0], 2);
        }
        
        var legend = L.control({position: 'bottomleft'});
        legend.onAdd = function (map) {
            var div = L.DomUtil.create('div', 'info legend');
            div.style.backgroundColor = 'white';
            div.style.padding = '8px 12px';
            div.style.borderRadius = '8px';
            div.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
            div.style.fontSize = '12px';
            div.style.lineHeight = '1.8';
            div.innerHTML = `
                <div style="font-weight:bold; margin-bottom:4px; border-bottom:1px solid #eee;">Map Legend</div>
                <div><img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png" style="height:14px; margin-right:4px; vertical-align:middle;"> Country</div>
                <div><img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png" style="height:14px; margin-right:4px; vertical-align:middle;"> Capital</div>
                <div><img src="/images/map/port-marker.svg" style="height:18px; margin-right:4px; vertical-align:middle; filter: drop-shadow(0px 2px 3px rgba(0,0,0,0.3));"> Port</div>
                <div><img src="/images/map/container-ship.svg" style="height:18px; margin-right:4px; vertical-align:middle; filter: drop-shadow(0px 2px 3px rgba(0,0,0,0.3));"> Ship</div>
            `;
            return div;
        };
        legend.addTo(map);

        // Ensure map resizes correctly when flexbox changes height
        if (typeof ResizeObserver !== 'undefined') {
            new ResizeObserver(function() {
                map.invalidateSize();
            }).observe(document.getElementById('countryMap'));
        } else {
            window.addEventListener('resize', function() {
                map.invalidateSize();
            });
        }
    });
</script>
