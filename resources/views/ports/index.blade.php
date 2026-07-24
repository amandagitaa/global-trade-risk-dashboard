@extends('layouts.app')

@section('title', 'Ports')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold text-dark mb-1">
                🌍 Global Ports Monitoring
            </h2>

            <p class="text-muted mb-0">
                Monitor worldwide container ports and logistics risks.
            </p>

        </div>

    </div>

    {{-- Statistics --}}
    <div class="row g-4 mb-4">

        {{-- Total Ports --}}
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card premium-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-muted fw-bold mb-1">Total Ports</h6>
                            <h2 class="fw-bold mb-0 premium-number text-dark">{{ $ports->count() }}</h2>
                        </div>
                        <div class="premium-icon-box bg-soft-primary">
                            <i class="bi bi-globe fs-3"></i>
                        </div>
                    </div>
                    <small class="text-muted d-block mb-3">Global Container Ports</small>
                    <span class="badge bg-soft-primary px-3 py-2 text-primary rounded-pill">Database Connected</span>
                </div>
            </div>
        </div>

        {{-- Low Risk --}}
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card premium-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-muted fw-bold mb-1">Low Risk Ports</h6>
                            <h2 class="fw-bold mb-0 premium-number text-success">{{ $ports->where('risk_level','Low')->count() }}</h2>
                        </div>
                        <div class="premium-icon-box bg-soft-success">
                            <i class="bi bi-shield-check fs-3"></i>
                        </div>
                    </div>
                    <small class="text-muted d-block mb-3">Stable Port Operation</small>
                    <span class="badge bg-soft-success px-3 py-2 text-success rounded-pill">Risk Level: Safe</span>
                </div>
            </div>
        </div>

        {{-- Medium Risk --}}
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card premium-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-muted fw-bold mb-1">Medium Risk Ports</h6>
                            <h2 class="fw-bold mb-0 premium-number text-warning">{{ $ports->where('risk_level','Medium')->count() }}</h2>
                        </div>
                        <div class="premium-icon-box bg-soft-warning">
                            <i class="bi bi-exclamation-triangle fs-3"></i>
                        </div>
                    </div>
                    <small class="text-muted d-block mb-3">Monitoring Required</small>
                    <span class="badge bg-soft-warning px-3 py-2 text-dark rounded-pill">Risk Level: Moderate</span>
                </div>
            </div>
        </div>

        {{-- High Risk --}}
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card premium-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-muted fw-bold mb-1">High Risk Ports</h6>
                            <h2 class="fw-bold mb-0 premium-number text-danger">{{ $ports->where('risk_level','High')->count() }}</h2>
                        </div>
                        <div class="premium-icon-box bg-soft-danger">
                            <i class="bi bi-exclamation-circle fs-3"></i>
                        </div>
                    </div>
                    <small class="text-muted d-block mb-3">Critical Attention Needed</small>
                    <span class="badge bg-soft-danger px-3 py-2 text-danger rounded-pill">Risk Level: High</span>
                </div>
            </div>
        </div>

    </div>

    {{-- Filter --}}
    <div class="card premium-card mb-4 p-2">

        <div class="card-body">

            <div class="row g-4">

                {{-- Search --}}
                <div class="col-lg-4">

                    <label class="form-label fw-bold text-muted small text-uppercase">
                        <i class="bi bi-search me-1"></i> Search Port
                    </label>

                    <input
                        id="searchPort"
                        type="text"
                        class="form-control premium-input form-control-lg"
                        placeholder="Search by port name...">

                </div>

                {{-- Region --}}
                <div class="col-lg-4">

                    <label class="form-label fw-bold text-muted small text-uppercase">
                        <i class="bi bi-geo-alt me-1"></i> Region
                    </label>

                    <select
                        id="filterRegion"
                        class="form-select premium-input form-select-lg">

                        <option value="">All Regions</option>

                        @foreach($ports->pluck('region')->unique()->sort() as $region)

                            <option value="{{ $region }}">
                                {{ $region }}
                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- Risk --}}
                <div class="col-lg-4">

                    <label class="form-label fw-bold text-muted small text-uppercase">
                        <i class="bi bi-shield-exclamation me-1"></i> Risk Level
                    </label>

                    <select
                        id="filterRisk"
                        class="form-select premium-input form-select-lg">

                        <option value="">All Risk Levels</option>

                        <option value="Low">🟢 Low</option>
                        <option value="Medium">🟡 Medium</option>
                        <option value="High">🔴 High</option>

                    </select>

                </div>

            </div>

        </div>

    </div>

    {{-- World Map --}}
    <div class="card premium-card overflow-hidden mb-4 p-0">

        <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4">

            <h5 class="mb-0 fw-bold d-flex align-items-center">

                <i class="bi bi-globe-americas text-primary me-2 fs-4"></i> World Port Map

            </h5>

        </div>

        <div class="card-body p-0">

            <div
                id="map"
                style="height:600px;">
            </div>

        </div>

    </div>

        {{-- Port Table --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white d-flex justify-content-between align-items-center">

            <h5 class="mb-0 fw-bold">
                ⚓ Port List
            </h5>

            <span class="badge bg-primary fs-6">
                {{ $ports->count() }} Ports
            </span>

        </div>

        <div class="table-responsive">

            <table
                class="table table-hover align-middle mb-0"
                id="portsTable">

                <thead class="table-light">

                <tr>

                    <th width="50">
                        #
                    </th>

                    <th>
                        Port
                    </th>

                    <th>
                        Country
                    </th>

                    <th>
                        Region
                    </th>

                    <th class="text-center">
                        Risk
                    </th>

                    <th class="text-center">
                        Status
                    </th>

                    <th class="text-center">
                        Action
                    </th>

                </tr>

                </thead>

                <tbody>

                @forelse($ports as $port)

                    <tr
                        data-region="{{ $port->region }}"
                        data-risk="{{ $port->risk_level }}"
                        data-name="{{ strtolower($port->name) }}">

                        <td>

                            {{ $loop->iteration }}

                        </td>

                        <td>

                            <div class="fw-bold">

                                ⚓ {{ $port->name }}

                            </div>

                            <small class="text-muted">

                                {{ $port->code }}

                            </small>

                        </td>

                        <td>

                            {{ $port->country->country_name }}

                        </td>

                        <td>

                            {{ $port->region }}

                        </td>

                        <td class="text-center">

                            @switch($port->risk_level)

                                @case('Low')

                                    <span class="badge bg-success">

                                        🟢 Low

                                    </span>

                                @break

                                @case('Medium')

                                    <span class="badge bg-warning text-dark">

                                        🟡 Medium

                                    </span>

                                @break

                                @default

                                    <span class="badge bg-danger">

                                        🔴 High

                                    </span>

                            @endswitch

                        </td>

                        <td class="text-center">

                            @if($port->status=="Active")

                                <span class="badge bg-success">

                                    Active

                                </span>

                            @else

                                <span class="badge bg-secondary">

                                    {{ $port->status }}

                                </span>

                            @endif

                        </td>

                        <td class="text-center">

                            <a
                                href="{{ route('ports.show',$port->id) }}"
                                class="btn btn-primary btn-sm mb-1">

                                <i class="bi bi-eye"></i>

                                View

                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7" class="text-center py-5">

                            No ports available.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection

@push('scripts')
<style>
/* PREMIUM ENTERPRISE STYLES */
.premium-card {
    border: none;
    border-radius: 20px;
    background: #fff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.premium-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.08);
}
.premium-icon-box {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.premium-number {
    font-size: 48px;
    letter-spacing: -1px;
}
.bg-soft-primary { background-color: rgba(253, 126, 20, 0.1); color: #fd7e14; }
.bg-soft-success { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
.bg-soft-warning { background-color: rgba(255, 193, 7, 0.2); color: #d39e00; }
.bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
.text-primary { color: #fd7e14 !important; }

.premium-input {
    border: none;
    background-color: #f8f9fa;
    border-radius: 12px;
    box-shadow: none !important;
}
.premium-input:focus {
    background-color: #fff;
    box-shadow: 0 0 0 4px rgba(253, 126, 20, 0.1) !important;
    border: 1px solid rgba(253, 126, 20, 0.3);
}

/* LEAFLET MAP MARKER STYLES */
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

<script>

const ports = @json($ports);

const map = L.map('map').setView([20,0],2);

L.tileLayer(
    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    {
        attribution:'© OpenStreetMap'
    }
).addTo(map);

map.on('popupopen', function(e) {
    if(e.popup._source && e.popup._source.options.icon && e.popup._source.options.icon.options.className === 'port-marker-animated') {
        var iconEl = e.popup._source._icon;
        if(iconEl) {
            iconEl.classList.add('port-marker-bounce');
            setTimeout(() => iconEl.classList.remove('port-marker-bounce'), 300);
        }
    }
});

let markers = [];

function markerColor(risk){

    switch(risk){

        case 'High':
            return '#dc3545';

        case 'Medium':
            return '#ffc107';

        default:
            return '#198754';

    }

}

function loadMarkers(data){

    markers.forEach(marker=>{

        map.removeLayer(marker);

    });

    markers=[];

    data.forEach(function(port){

        let icon = L.icon({
            iconUrl: '/images/map/container-ship.svg',
            iconSize: [40, 40],
            iconAnchor: [20, 34],
            popupAnchor: [0, -28],
            className: 'port-marker-animated'
        });

        let marker=L.marker(
            [
                port.latitude,
                port.longitude
            ],
            {
                icon:icon
            }
        )
        .addTo(map)
        .bindPopup(`

            <div style="min-width:220px">

                <h6 class="mb-2">

                    ⚓ ${port.name}

                </h6>

                <table class="table table-sm mb-2">

                    <tr>

                        <th>Country</th>

                        <td>${port.country.country_name}</td>

                    </tr>

                    <tr>

                        <th>Region</th>

                        <td>${port.region}</td>

                    </tr>

                    <tr>

                        <th>Status</th>

                        <td>${port.status}</td>

                    </tr>

                    <tr>

                        <th>Risk</th>

                        <td>${port.risk_level}</td>

                    </tr>

                    <tr>

                        <th>Traffic</th>

                        <td>${port.traffic_level}</td>

                    </tr>

                </table>

                <hr>

                <strong>AI Recommendation</strong>

                <br>

                ${port.ai_recommendation}

            </div>

        `);

        marker.portData=port;

        markers.push(marker);

    });

}

loadMarkers(ports);

function focusPort(lat,lng){

    map.flyTo(
        [lat,lng],
        6,
        {
            animate:true,
            duration:1.5
        }
    );

}

const search=document.getElementById('searchPort');

const region=document.getElementById('filterRegion');

const risk=document.getElementById('filterRisk');

function filterPorts(){

    let keyword=search.value.toLowerCase();

    let regionValue=region.value;

    let riskValue=risk.value;

    const filtered=ports.filter(function(port){

        let matchName=
            port.name.toLowerCase().includes(keyword);

        let matchRegion=
            regionValue=='' ||
            port.region===regionValue;

        let matchRisk=
            riskValue=='' ||
            port.risk_level===riskValue;

        return matchName &&
               matchRegion &&
               matchRisk;

    });

    loadMarkers(filtered);

    const rows=document.querySelectorAll('#portsTable tbody tr');

    rows.forEach(function(row){

        let name=row.dataset.name;

        let reg=row.dataset.region;

        let rk=row.dataset.risk;

        let show=true;

        if(
            keyword &&
            !name.includes(keyword)
        ){
            show=false;
        }

        if(
            regionValue &&
            reg!==regionValue
        ){
            show=false;
        }

        if(
            riskValue &&
            rk!==riskValue
        ){
            show=false;
        }

        row.style.display=
            show ? '' : 'none';

    });

}

search.addEventListener(
    'keyup',
    filterPorts
);

region.addEventListener(
    'change',
    filterPorts
);

risk.addEventListener(
    'change',
    filterPorts
);

</script>

@endpush