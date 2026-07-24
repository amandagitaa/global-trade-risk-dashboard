@extends('layouts.app')

@section('title', 'Port Detail - ' . $port->name)

@section('content')

<div class="container-fluid mb-5">

    {{-- ==========================================
        SECTION 1: PORT HERO
    ========================================== --}}
    <div class="card enterprise-card mb-5">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4">
                <div class="d-flex align-items-center">
                    <div class="hero-icon-box bg-soft-primary me-4 d-none d-md-flex">
                        <i class="bi bi-anchor fs-1 text-primary"></i>
                    </div>
                    <div>
                        <h1 class="fw-bold mb-2 enterprise-title">
                            {{ $port->name }}
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-3">
                                <li class="breadcrumb-item"><a href="{{ route('ports.index') }}" class="text-decoration-none text-muted">Ports</a></li>
                                <li class="breadcrumb-item text-muted">{{ $port->country->country_name }}</li>
                                <li class="breadcrumb-item active fw-semibold" aria-current="page">{{ $port->name }}</li>
                            </ol>
                        </nav>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="enterprise-badge bg-light text-dark shadow-sm">
                                <i class="bi bi-geo-alt me-1"></i> {{ $port->region }}
                            </span>
                            @if($port->status == 'Active')
                                <span class="enterprise-badge bg-soft-success text-success shadow-sm">
                                    <i class="bi bi-check-circle me-1"></i> Operational
                                </span>
                            @else
                                <span class="enterprise-badge bg-soft-secondary text-secondary shadow-sm">
                                    <i class="bi bi-dash-circle me-1"></i> {{ $port->status }}
                                </span>
                            @endif
                            @if($port->risk_level == 'Low')
                                <span class="enterprise-badge bg-soft-success text-success shadow-sm"><i class="bi bi-shield-check me-1"></i> Low Risk</span>
                            @elseif($port->risk_level == 'Medium')
                                <span class="enterprise-badge bg-soft-warning text-dark shadow-sm"><i class="bi bi-exclamation-triangle me-1"></i> Medium Risk</span>
                            @else
                                <span class="enterprise-badge bg-soft-danger text-danger shadow-sm"><i class="bi bi-shield-exclamation me-1"></i> High Risk</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <form action="{{ route('watch-list.store') }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="watch_type" value="port">
                        <input type="hidden" name="port_id" value="{{ $port->id }}">
                        <button type="submit" class="btn btn-warning enterprise-btn text-dark shadow-sm">
                            <i class="bi bi-star-fill me-2"></i> Add to Watch List
                        </button>
                    </form>
                    <a href="{{ route('ports.index') }}" class="btn btn-light enterprise-btn">
                        <i class="bi bi-arrow-left me-2"></i> Back to Ports
                    </a>
                    @if($port->country)
                        <a href="{{ route('countries.show', $port->country) }}"
                        class="btn btn-primary enterprise-btn">
                            Open Country
                            <i class="bi bi-box-arrow-up-right ms-2"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ==========================================
        SECTION 2: SUMMARY KPI
    ========================================== --}}
    <div class="row g-4 mb-5">
        {{-- Overall Risk --}}
        <div class="col-lg-3 col-md-6">
            <div class="card enterprise-card premium-kpi-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="kpi-icon-box bg-soft-dark">
                            <i class="bi bi-shield-shaded fs-4 text-dark"></i>
                        </div>
                        @if($port->risk_level == 'Low')
                            <span class="badge bg-success rounded-pill px-3 py-2">Low</span>
                        @elseif($port->risk_level == 'Medium')
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Medium</span>
                        @else
                            <span class="badge bg-danger rounded-pill px-3 py-2">High</span>
                        @endif
                    </div>
                    <h6 class="text-muted fw-bold text-uppercase mb-1">Overall Risk Score</h6>
                    <h2 class="fw-bold mb-0 kpi-number">{{ $port->risk_score }}</h2>
                    <small class="text-muted mt-2 d-block">Composite risk index (0-100)</small>
                </div>
            </div>
        </div>

        {{-- Annual Capacity --}}
        <div class="col-lg-3 col-md-6">
            <div class="card enterprise-card premium-kpi-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="kpi-icon-box bg-soft-primary">
                            <i class="bi bi-building fs-4 text-primary"></i>
                        </div>
                    </div>
                    <h6 class="text-muted fw-bold text-uppercase mb-1">Annual Capacity</h6>
                    <h2 class="fw-bold mb-0 kpi-number">{{ number_format($port->annual_capacity) }}</h2>
                    <small class="text-muted mt-2 d-block">Total tons per year</small>
                </div>
            </div>
        </div>

        {{-- TEU Capacity --}}
        <div class="col-lg-3 col-md-6">
            <div class="card enterprise-card premium-kpi-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="kpi-icon-box bg-soft-info">
                            <i class="bi bi-box fs-4 text-info"></i>
                        </div>
                    </div>
                    <h6 class="text-muted fw-bold text-uppercase mb-1">TEU Capacity</h6>
                    <h2 class="fw-bold mb-0 kpi-number">{{ number_format($port->teu_capacity) }}</h2>
                    <small class="text-muted mt-2 d-block">Twenty-foot equivalent units</small>
                </div>
            </div>
        </div>

        {{-- Trade Volume --}}
        <div class="col-lg-3 col-md-6">
            <div class="card enterprise-card premium-kpi-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="kpi-icon-box bg-soft-success">
                            <i class="bi bi-graph-up-arrow fs-4 text-success"></i>
                        </div>
                    </div>
                    <h6 class="text-muted fw-bold text-uppercase mb-1">Trade Volume</h6>
                    <h2 class="fw-bold mb-0 kpi-number">{{ number_format($port->trade_volume) }}</h2>
                    <small class="text-muted mt-2 d-block">Recorded trade activity</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ==========================================
        SECTION 3: MAP AND PORT INFORMATION (7:5)
    ========================================== --}}
    <div class="row g-4 mb-5">
        {{-- Map --}}
        <div class="col-lg-7">
            <div class="card enterprise-card h-100 overflow-hidden p-0">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-0"><i class="bi bi-map text-primary me-2"></i> Port Location</h5>
                </div>
                <div class="card-body p-4">
                    <div id="map" class="enterprise-map rounded-4" style="height: 500px; z-index: 1;"></div>
                </div>
            </div>
        </div>
        {{-- Port Information --}}
        <div class="col-lg-5">
            <div class="card enterprise-card h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-2">
                    <h5 class="fw-bold mb-0"><i class="bi bi-info-square text-primary me-2"></i> Port Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="info-list">
                        <div class="info-item d-flex align-items-center justify-content-between py-3 border-bottom border-light">
                            <div class="text-muted d-flex align-items-center"><i class="bi bi-upc-scan me-3 fs-5"></i> <span>Code</span></div>
                            <div class="fw-bold">{{ $port->code }}</div>
                        </div>
                        <div class="info-item d-flex align-items-center justify-content-between py-3 border-bottom border-light">
                            <div class="text-muted d-flex align-items-center"><i class="bi bi-flag me-3 fs-5"></i> <span>Country</span></div>
                            <div class="fw-bold">{{ $port->country->country_name }}</div>
                        </div>
                        <div class="info-item d-flex align-items-center justify-content-between py-3 border-bottom border-light">
                            <div class="text-muted d-flex align-items-center"><i class="bi bi-building me-3 fs-5"></i> <span>City</span></div>
                            <div class="fw-bold">{{ $port->city }}</div>
                        </div>
                        <div class="info-item d-flex align-items-center justify-content-between py-3 border-bottom border-light">
                            <div class="text-muted d-flex align-items-center"><i class="bi bi-globe-americas me-3 fs-5"></i> <span>Region</span></div>
                            <div class="fw-bold">{{ $port->region }}</div>
                        </div>
                        <div class="info-item d-flex align-items-center justify-content-between py-3 border-bottom border-light">
                            <div class="text-muted d-flex align-items-center"><i class="bi bi-water me-3 fs-5"></i> <span>Type</span></div>
                            <div class="fw-bold">{{ $port->port_type }}</div>
                        </div>
                        <div class="info-item d-flex align-items-center justify-content-between py-3 border-bottom border-light">
                            <div class="text-muted d-flex align-items-center"><i class="bi bi-stoplights me-3 fs-5"></i> <span>Traffic</span></div>
                            <div class="fw-bold">{{ $port->traffic_level }}</div>
                        </div>
                        <div class="info-item d-flex align-items-center justify-content-between py-3">
                            <div class="text-muted d-flex align-items-center"><i class="bi bi-compass me-3 fs-5"></i> <span>Coordinates</span></div>
                            <div class="fw-bold text-end">
                                {{ number_format($port->latitude, 4) }},<br>
                                {{ number_format($port->longitude, 4) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==========================================
        SECTION 4 & 5: RISK DASHBOARD & AI
    ========================================== --}}
    <div class="row g-4 mb-5">
        {{-- Risk Dashboard --}}
        <div class="col-12">
            <div class="card enterprise-card h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-2">
                    <h5 class="fw-bold mb-0"><i class="bi bi-activity text-primary me-2"></i> Risk Analysis Dashboard</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        {{-- Weather --}}
                        <div class="col-md-6">
                            <div class="risk-block p-3 rounded-4 bg-light border border-white shadow-sm">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-muted"><i class="bi bi-cloud-lightning-rain me-2 text-info"></i> Weather Risk</span>
                                    <span class="badge bg-soft-info text-info rounded-pill px-2">{{ $port->weather_risk }}%</span>
                                </div>
                                <div class="progress rounded-pill bg-white" style="height: 8px;">
                                    <div class="progress-bar bg-info rounded-pill" style="width: {{ $port->weather_risk }}%;"></div>
                                </div>
                            </div>
                        </div>
                        {{-- Political --}}
                        <div class="col-md-6">
                            <div class="risk-block p-3 rounded-4 bg-light border border-white shadow-sm">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-muted"><i class="bi bi-bank me-2 text-warning"></i> Political Risk</span>
                                    <span class="badge bg-soft-warning text-dark rounded-pill px-2">{{ $port->political_risk }}%</span>
                                </div>
                                <div class="progress rounded-pill bg-white" style="height: 8px;">
                                    <div class="progress-bar bg-warning rounded-pill" style="width: {{ $port->political_risk }}%;"></div>
                                </div>
                            </div>
                        </div>
                        {{-- Logistic --}}
                        <div class="col-md-6">
                            <div class="risk-block p-3 rounded-4 bg-light border border-white shadow-sm">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-muted"><i class="bi bi-truck me-2 text-danger"></i> Logistic Risk</span>
                                    <span class="badge bg-soft-danger text-danger rounded-pill px-2">{{ $port->logistic_risk }}%</span>
                                </div>
                                <div class="progress rounded-pill bg-white" style="height: 8px;">
                                    <div class="progress-bar bg-danger rounded-pill" style="width: {{ $port->logistic_risk }}%;"></div>
                                </div>
                            </div>
                        </div>
                        {{-- Security (Calculated visually from average of political & logistic) --}}
                        <div class="col-md-6">
                            <div class="risk-block p-3 rounded-4 bg-light border border-white shadow-sm">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-muted"><i class="bi bi-shield-lock me-2 text-primary"></i> Security Risk</span>
                                    @php $sec_risk = round(($port->political_risk + $port->logistic_risk) / 2); @endphp
                                    <span class="badge bg-soft-primary text-primary rounded-pill px-2">{{ $sec_risk }}%</span>
                                </div>
                                <div class="progress rounded-pill bg-white" style="height: 8px;">
                                    <div class="progress-bar bg-primary rounded-pill" style="width: {{ $sec_risk }}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

    {{-- ==========================================
        SECTION 6: ACTIVE SHIPS
    ========================================== --}}
    <div class="card enterprise-card mb-5 p-0 overflow-hidden">
        <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-tsunami text-primary me-2"></i> Active Ships Overview</h5>
            <div class="d-flex gap-3 flex-wrap">
                <div class="px-3 py-2 bg-light rounded-pill border border-white shadow-sm d-flex align-items-center">
                    <span class="fw-bold fs-5 me-2">{{ $port->ships->count() }}</span> <small class="text-muted fw-semibold">Total Ships</small>
                </div>
                <div class="px-3 py-2 bg-soft-success rounded-pill border border-white shadow-sm d-flex align-items-center">
                    <span class="fw-bold text-success fs-5 me-2">{{ $port->ships->where('status','Sailing')->count() }}</span> <small class="text-success fw-semibold">Sailing</small>
                </div>
                <div class="px-3 py-2 bg-soft-danger rounded-pill border border-white shadow-sm d-flex align-items-center">
                    <span class="fw-bold text-danger fs-5 me-2">{{ $port->ships->where('status','Delayed')->count() }}</span> <small class="text-danger fw-semibold">Delayed</small>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            @if($port->ships->count() > 0)
                <div class="table-responsive px-4 pb-4">
                    <table class="table enterprise-table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Vessel</th>
                                <th>Status</th>
                                <th>Destination</th>
                                <th>Cargo Load</th>
                                <th>Speed</th>
                                <th>ETA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($port->ships as $ship)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle p-2 me-3 d-flex align-items-center justify-content-center shadow-sm border border-white" style="width:40px; height:40px;">
                                                <i class="bi bi-ship text-secondary"></i>
                                            </div>
                                            <div>
                                                <strong class="d-block text-dark">{{ $ship->ship_name }}</strong>
                                                <small class="text-muted">{{ $ship->ship_type }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @switch($ship->status)
                                            @case('Docked')
                                                <span class="enterprise-badge bg-soft-secondary text-secondary">Docked</span>
                                            @break
                                            @case('Loading')
                                                <span class="enterprise-badge bg-soft-warning text-dark">Loading</span>
                                            @break
                                            @case('Delayed')
                                                <span class="enterprise-badge bg-soft-danger text-danger">Delayed</span>
                                            @break
                                            @default
                                                <span class="enterprise-badge bg-soft-success text-success">Sailing</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><i class="bi bi-geo-alt text-primary me-1"></i> {{ $ship->destinationPort->name ?? 'Unknown' }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2 rounded-pill bg-light" style="height: 6px; width: 60px;">
                                                <div class="progress-bar bg-primary rounded-pill" style="width: {{ $ship->cargo_percentage }}%;"></div>
                                            </div>
                                            <small class="fw-bold text-muted">{{ $ship->cargo_percentage }}%</small>
                                        </div>
                                    </td>
                                    <td><span class="fw-semibold">{{ $ship->speed_knots }} kn</span></td>
                                    <td><span class="fw-semibold">{{ $ship->eta_days }} Days</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state text-center py-5">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm border border-white" style="width: 80px; height: 80px;">
                        <i class="bi bi-ship fs-1 text-muted"></i>
                    </div>
                    <h5 class="fw-bold text-dark">No Active Ships</h5>
                    <p class="text-muted mb-0">There are currently no vessels actively reporting from this port.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ==========================================
        SECTION 7: SHIPPING ROUTES
    ========================================== --}}
    <div class="card enterprise-card mb-5 p-0 overflow-hidden">
        <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-signpost-split text-primary me-2"></i> Shipping Routes Network</h5>
            <div class="d-flex gap-3 flex-wrap">
                <div class="px-3 py-2 bg-light rounded-pill border border-white shadow-sm d-flex align-items-center">
                    <span class="fw-bold fs-5 me-2">{{ $port->routes->count() }}</span> <small class="text-muted fw-semibold">Established Routes</small>
                </div>
                @if($port->routes->count() > 0)
                <div class="px-3 py-2 bg-light rounded-pill border border-white shadow-sm d-flex align-items-center">
                    <span class="fw-bold fs-5 me-2">{{ number_format($port->routes->avg('distance_km')) }}</span> <small class="text-muted fw-semibold">Avg Distance (km)</small>
                </div>
                <div class="px-3 py-2 bg-light rounded-pill border border-white shadow-sm d-flex align-items-center">
                    <span class="fw-bold fs-5 me-2">{{ number_format($port->routes->avg('estimated_days')) }}</span> <small class="text-muted fw-semibold">Avg ETA (Days)</small>
                </div>
                @endif
            </div>
        </div>

        <div class="card-body p-0">
            @if($port->routes->count() > 0)
                <div class="table-responsive px-4 pb-4">
                    <table class="table enterprise-table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Destination Port</th>
                                <th>Distance</th>
                                <th>Est. Transit Time</th>
                                <th>Route Risk</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($port->routes as $route)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark"><i class="bi bi-geo-alt-fill text-primary me-2"></i>{{ $route->destinationPort->name }}</div>
                                    </td>
                                    <td><span class="fw-semibold text-muted">{{ number_format($route->distance_km) }} km</span></td>
                                    <td><span class="fw-semibold text-muted">{{ $route->estimated_days }} Days</span></td>
                                    <td>
                                        @if($route->risk_level=="Low")
                                            <span class="enterprise-badge bg-soft-success text-success">Low Risk</span>
                                        @elseif($route->risk_level=="Medium")
                                            <span class="enterprise-badge bg-soft-warning text-dark">Medium Risk</span>
                                        @else
                                            <span class="enterprise-badge bg-soft-danger text-danger">High Risk</span>
                                        @endif
                                    </td>
                                    <td><span class="fw-semibold">{{ $route->status }}</span></td>
                                    <td class="text-end">
                                        <a href="{{ route('ports.show', $route->destination_port_id) }}" class="btn btn-sm btn-light rounded-pill px-3 fw-bold shadow-sm">View <i class="bi bi-arrow-right ms-1"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state text-center py-5">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm border border-white" style="width: 80px; height: 80px;">
                        <i class="bi bi-signpost-split fs-1 text-muted"></i>
                    </div>
                    <h5 class="fw-bold text-dark">No Routes Found</h5>
                    <p class="text-muted mb-0">This port does not have any established shipping routes in the database.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ==========================================
        SECTION 8: NEARBY PORTS GRID
    ========================================== --}}
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h3 class="fw-bold mb-0 text-dark"><i class="bi bi-geo text-primary me-2"></i> Nearby Ports</h3>
    </div>
    
    <div class="row g-4 mb-5">
        @forelse($nearbyPorts as $nearby)
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card enterprise-card h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="bg-light rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm border border-white" style="width:48px; height:48px;">
                                    <i class="bi bi-anchor fs-4 text-primary"></i>
                                </div>
                                @if($nearby->risk_level=="Low")
                                    <span class="enterprise-badge bg-soft-success text-success">Low Risk</span>
                                @elseif($nearby->risk_level=="Medium")
                                    <span class="enterprise-badge bg-soft-warning text-dark">Medium Risk</span>
                                @else
                                    <span class="enterprise-badge bg-soft-danger text-danger">High Risk</span>
                                @endif
                            </div>
                            <h5 class="fw-bold text-dark mb-1">{{ $nearby->name }}</h5>
                            <p class="text-muted small mb-3"><i class="bi bi-pin-map me-1"></i> {{ $nearby->city }}, {{ $nearby->country->country_name }}</p>
                            
                            <div class="d-flex justify-content-between mb-4 border-top border-light pt-3 mt-3">
                                <div>
                                    <small class="text-muted d-block">Capacity (Tons)</small>
                                    <span class="fw-bold">{{ number_format($nearby->annual_capacity) }}</span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">Traffic</small>
                                    <span class="fw-bold">{{ $nearby->traffic_level }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <a href="{{ route('ports.show', $nearby->id) }}" class="btn btn-outline-primary w-100 enterprise-btn rounded-pill fw-bold">
                            View Details <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card enterprise-card">
                    <div class="card-body text-center py-5">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm border border-white" style="width: 80px; height: 80px;">
                            <i class="bi bi-pin-map fs-1 text-muted"></i>
                        </div>
                        <h5 class="fw-bold text-dark">No Nearby Ports</h5>
                        <p class="text-muted mb-0">No nearby ports found in the vicinity of this port.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

</div>

@endsection

@push('scripts')
<style>
/* ==========================================
   ENTERPRISE DASHBOARD STYLES
========================================== */

/* Typography */
.enterprise-title {
    font-size: 32px;
    letter-spacing: -0.5px;
}

/* Premium Cards */
.enterprise-card {
    border: none;
    border-radius: 20px;
    background: #fff;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.enterprise-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
}

/* Badges */
.enterprise-badge {
    padding: 6px 14px;
    border-radius: 50rem;
    font-size: 13px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
}

/* KPI Components */
.premium-kpi-card {
    position: relative;
    overflow: hidden;
}
.kpi-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.kpi-number {
    font-size: 40px;
    letter-spacing: -1px;
}
.hero-icon-box {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Colors */
.bg-soft-primary { background-color: rgba(253, 126, 20, 0.1); }
.bg-soft-success { background-color: rgba(25, 135, 84, 0.1); }
.bg-soft-warning { background-color: rgba(255, 193, 7, 0.2); }
.bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
.bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }
.bg-soft-dark { background-color: rgba(33, 37, 41, 0.05); }
.bg-soft-secondary { background-color: rgba(108, 117, 125, 0.1); }

.text-primary { color: #fd7e14 !important; }

/* Buttons */
.enterprise-btn {
    border-radius: 50rem;
    padding: 10px 24px;
    font-weight: 600;
    transition: all 0.3s ease;
}
.enterprise-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

/* Tables */
.enterprise-table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #e9ecef;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
    padding: 16px;
    position: sticky;
    top: 0;
    z-index: 10;
}
.enterprise-table td {
    padding: 16px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f5;
}
.enterprise-table tbody tr {
    transition: background-color 0.2s ease;
}
.enterprise-table tbody tr:hover {
    background-color: #f8f9fa;
}

/* Map specific styling */
.enterprise-map {
    box-shadow: inset 0 0 10px rgba(0,0,0,0.05);
    border: 1px solid #f1f3f5;
}

/* Map Marker */
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
document.addEventListener("DOMContentLoaded", function() {
    const map = L.map('map').setView([{{ $port->latitude }}, {{ $port->longitude }}], 8);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    map.on('popupopen', function(e) {
        if(e.popup._source && e.popup._source.options.icon && e.popup._source.options.icon.options.className === 'port-marker-animated') {
            var iconEl = e.popup._source._icon;
            if(iconEl) {
                iconEl.classList.add('port-marker-bounce');
                setTimeout(() => iconEl.classList.remove('port-marker-bounce'), 300);
            }
        }
    });

    const icon = L.icon({
        iconUrl: '/images/map/container-ship.svg',
        iconSize: [40, 40],
        iconAnchor: [20, 34],
        popupAnchor: [0, -28],
        className: 'port-marker-animated'
    });

    L.marker([{{ $port->latitude }}, {{ $port->longitude }}], { icon: icon })
    .addTo(map)
    .bindPopup(`
        <div class="text-center p-2">
            <h6 class="fw-bold mb-1">{{ $port->name }}</h6>
            <small class="text-muted d-block mb-2">{{ $port->country->country_name }}</small>
            <span class="badge bg-primary rounded-pill px-2 py-1">Risk: {{ $port->risk_level }}</span>
        </div>
    `);
});
</script>
@endpush