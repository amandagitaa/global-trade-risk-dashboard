@extends('layouts.app')

@section('title', 'Trade Planner - Global Trade Intelligence')

@section('content')

<div class="container-fluid mb-5">

    {{-- ==========================================
        HEADER
    ========================================== --}}
    <div class="d-flex align-items-center mb-4">
        <div class="hero-icon-container me-4 d-flex align-items-center justify-content-center">
            <i class="bi bi-signpost-2 text-white hero-icon"></i>
        </div>
        <div>
            <h2 class="fw-bold mb-1 enterprise-title text-dark">Trade Planner</h2>
            <p class="text-muted mb-0">Plan international shipments with AI-powered route estimation and risk assessment.</p>
        </div>
    </div>

    {{-- ==========================================
        SECTION 1: SHIPMENT PLANNER FORM
    ========================================== --}}
    <div class="card enterprise-card mb-5">
        <div class="card-body p-4 p-md-5">
            <h5 class="fw-bold mb-4"><i class="bi bi-ui-checks-grid text-primary me-2"></i> Shipment Parameters</h5>
            <form action="{{ route('trade-planner.index') }}" method="GET" id="plannerForm">
                <div class="row g-4">
                    {{-- Origin Column --}}
                    <div class="col-md-6 border-end border-light">
                        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-box-arrow-up me-2"></i> Origin Details</h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Origin Country</label>
                            <select class="form-select enterprise-select" id="origin_country">
                                <option value="">Select Country...</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Origin Port</label>
                            <select class="form-select enterprise-select" name="origin_port" id="origin_port" required>
                                <option value="">Select Port...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Cargo Type</label>
                            <select class="form-select enterprise-select" name="cargo_type">
                                <option value="General" {{ request('cargo_type') == 'General' ? 'selected' : '' }}>General Cargo</option>
                                <option value="Liquid" {{ request('cargo_type') == 'Liquid' ? 'selected' : '' }}>Liquid / Chemical</option>
                                <option value="Perishable" {{ request('cargo_type') == 'Perishable' ? 'selected' : '' }}>Perishable</option>
                                <option value="Hazardous" {{ request('cargo_type') == 'Hazardous' ? 'selected' : '' }}>Hazardous Material</option>
                            </select>
                        </div>
                    </div>

                    {{-- Destination Column --}}
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-box-arrow-in-down me-2"></i> Destination Details</h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Destination Country</label>
                            <select class="form-select enterprise-select" id="destination_country">
                                <option value="">Select Country...</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Destination Port</label>
                            <select class="form-select enterprise-select" name="destination_port" id="destination_port" required>
                                <option value="">Select Port...</option>
                            </select>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Container Size</label>
                                <select class="form-select enterprise-select" name="container_size">
                                    <option value="20" {{ request('container_size') == '20' ? 'selected' : '' }}>20 FT</option>
                                    <option value="40" {{ request('container_size') == '40' ? 'selected' : '' }}>40 FT</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Departure Date</label>
                                <input type="date" class="form-control enterprise-input" name="departure_date" value="{{ request('departure_date', date('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top border-light">
                    <a href="{{ route('trade-planner.index') }}" class="btn btn-light enterprise-btn fw-bold px-4">Reset</a>
                    <button type="submit" class="btn btn-primary enterprise-btn fw-bold px-5"><i class="bi bi-calculator me-2"></i> Calculate Simulation</button>
                </div>
            </form>
        </div>
    </div>

    @if($simulation)
        {{-- ==========================================
            ADD TO WATCH LIST
        ========================================== --}}
        @if($simulation['routes'] && $simulation['routes']->count() > 0)
        <div class="d-flex justify-content-end mb-4 fade-in">
            <form action="{{ route('watch-list.store') }}" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="watch_type" value="route">
                <input type="hidden" name="route_id" value="{{ $simulation['routes']->first()->id }}">
                <button type="submit" class="btn btn-warning enterprise-btn text-dark fw-bold shadow-sm">
                    <i class="bi bi-star-fill me-2"></i> Add Route to Watch List
                </button>
            </form>
        </div>
        @endif

        {{-- ==========================================
            SECTION 2: SHIPMENT SUMMARY (6 KPI CARDS)
        ========================================== --}}
        <div class="row g-4 mb-5 fade-in">
            {{-- Card 1: Estimated Transit Time --}}
            <div class="col-lg-4 col-md-6">
                <div class="card enterprise-card h-100">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-soft-primary rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width:56px; height:56px;">
                            <i class="bi bi-clock-history fs-3 text-primary"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Estimated Transit Time</small>
                            <h3 class="fw-bold mb-0 text-dark">{{ $simulation['finalEta'] }} Days</h3>
                            @if($simulation['totalDelay'] > 0)
                                <span class="badge bg-soft-danger text-danger mt-1 rounded-pill">+{{ $simulation['totalDelay'] }} Days Delay</span>
                            @else
                                <span class="badge bg-soft-success text-success mt-1 rounded-pill">On Schedule</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Distance --}}
            <div class="col-lg-4 col-md-6">
                <div class="card enterprise-card h-100">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-soft-info rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width:56px; height:56px;">
                            <i class="bi bi-signpost-split fs-3 text-info"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Distance</small>
                            <h3 class="fw-bold mb-0 text-dark">{{ number_format($simulation['distance']) }} km</h3>
                            <span class="badge bg-light text-muted mt-1 rounded-pill">Sea Route</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 3: Estimated Shipping Cost --}}
            <div class="col-lg-4 col-md-6">
                <div class="card enterprise-card h-100">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-soft-success rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width:56px; height:56px;">
                            <i class="bi bi-currency-dollar fs-3 text-success"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Estimated Shipping Cost</small>
                            <h3 class="fw-bold mb-0 text-dark">USD {{ number_format($simulation['cost']) }}</h3>
                            @if($simulation['costIndicator'] == 'Stable')
                                <span class="badge bg-soft-success text-success mt-1 rounded-pill">Stable</span>
                            @elseif($simulation['costIndicator'] == 'Increase')
                                <span class="badge bg-soft-warning text-dark mt-1 rounded-pill">Increase</span>
                            @else
                                <span class="badge bg-soft-danger text-danger mt-1 rounded-pill">Critical</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 4: Trade Risk --}}
            <div class="col-lg-4 col-md-6">
                <div class="card enterprise-card h-100">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-soft-dark rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width:56px; height:56px;">
                            <i class="bi bi-shield-exclamation fs-3 text-dark"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Trade Risk</small>
                            <h3 class="fw-bold mb-0 text-dark">{{ $simulation['tradeRisk'] }}</h3>
                            @if($simulation['tradeRisk'] == 'Low')
                                <span class="badge bg-success rounded-pill mt-1">Safe</span>
                            @elseif($simulation['tradeRisk'] == 'Medium')
                                <span class="badge bg-warning text-dark rounded-pill mt-1">Monitor</span>
                            @else
                                <span class="badge bg-danger rounded-pill mt-1">High Alert</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 5: Weather Impact --}}
            <div class="col-lg-4 col-md-6">
                <div class="card enterprise-card h-100">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-soft-secondary rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width:56px; height:56px;">
                            <i class="bi bi-cloud-sun fs-3 text-secondary"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Weather Impact</small>
                            <h3 class="fw-bold mb-0 text-dark">{{ $simulation['weatherStatus'] }}</h3>
                            @if($simulation['weatherStatus'] == 'Clear')
                                <span class="badge bg-soft-success text-success mt-1 rounded-pill">Favorable</span>
                            @else
                                <span class="badge bg-soft-danger text-danger mt-1 rounded-pill">Adverse</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 6: Political Status --}}
            <div class="col-lg-4 col-md-6">
                <div class="card enterprise-card h-100">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-soft-warning rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width:56px; height:56px;">
                            <i class="bi bi-bank fs-3 text-warning"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Political Status</small>
                            <h3 class="fw-bold mb-0 text-dark">{{ $simulation['politicalStatus'] }}</h3>
                            @if($simulation['politicalStatus'] == 'Normal')
                                <span class="badge bg-soft-success text-success mt-1 rounded-pill">Stable</span>
                            @else
                                <span class="badge bg-soft-danger text-danger mt-1 rounded-pill">Unstable</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==========================================
            SECTION 3: ROUTE VISUALIZATION (MAP)
        ========================================== --}}
        <div class="card enterprise-card mb-5 overflow-hidden fade-in">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-2">
                <h5 class="fw-bold mb-0"><i class="bi bi-map text-primary me-2"></i> Route Visualization</h5>
            </div>
            <div class="card-body p-4">
                <div id="planner-map" class="enterprise-map rounded-4 shadow-sm" style="height: 500px; z-index: 1;"></div>
            </div>
        </div>

        <div class="row g-4 mb-5 fade-in">
            {{-- ==========================================
                SECTION 4: JOURNEY TIMELINE
            ========================================== --}}
            <div class="col-lg-4">
                <div class="card enterprise-card h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold mb-0"><i class="bi bi-calendar-event text-primary me-2"></i> Journey Timeline</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="timeline-container ps-3 pt-2">
                            <div class="timeline-item position-relative pb-4 border-start border-2 border-primary ps-4">
                                <div class="timeline-dot bg-primary position-absolute rounded-circle" style="left: -9px; top: 0; width: 16px; height: 16px; border: 3px solid #fff; box-shadow: 0 0 0 3px #fd7e14;"></div>
                                <h6 class="fw-bold text-dark mb-1">Departure</h6>
                                <p class="text-muted small mb-0">{{ \Carbon\Carbon::parse(request('departure_date', date('Y-m-d')))->format('M d, Y') }}</p>
                            </div>
                            
                            <div class="timeline-item position-relative pb-4 border-start border-2 border-primary ps-4">
                                <div class="timeline-dot bg-primary position-absolute rounded-circle" style="left: -9px; top: 0; width: 16px; height: 16px; border: 3px solid #fff;"></div>
                                <h6 class="fw-bold text-dark mb-1">Port Loading</h6>
                                <p class="text-muted small mb-0">{{ $simulation['origin']->name }}</p>
                            </div>
                            
                            <div class="timeline-item position-relative pb-4 border-start border-2 border-primary ps-4">
                                <div class="timeline-dot bg-primary position-absolute rounded-circle" style="left: -9px; top: 0; width: 16px; height: 16px; border: 3px solid #fff;"></div>
                                <h6 class="fw-bold text-dark mb-1">International Shipping</h6>
                                <p class="text-muted small mb-0">Est. {{ $simulation['baseEta'] }} Days</p>
                            </div>

                            @if($simulation['totalDelay'] > 0)
                            <div class="timeline-item position-relative pb-4 border-start border-2 border-danger ps-4">
                                <div class="timeline-dot bg-danger position-absolute rounded-circle" style="left: -9px; top: 0; width: 16px; height: 16px; border: 3px solid #fff;"></div>
                                <h6 class="fw-bold text-danger mb-1">Simulated Delays</h6>
                                <p class="text-danger small mb-0">+{{ $simulation['totalDelay'] }} Days Penalty</p>
                            </div>
                            @endif

                            <div class="timeline-item position-relative pb-4 border-start border-2 border-light ps-4">
                                <div class="timeline-dot bg-secondary position-absolute rounded-circle" style="left: -9px; top: 0; width: 16px; height: 16px; border: 3px solid #fff;"></div>
                                <h6 class="fw-bold text-dark mb-1">Custom Clearance</h6>
                                <p class="text-muted small mb-0">At Destination</p>
                            </div>

                            <div class="timeline-item position-relative ps-4">
                                <div class="timeline-dot bg-success position-absolute rounded-circle" style="left: -9px; top: 0; width: 16px; height: 16px; border: 3px solid #fff;"></div>
                                <h6 class="fw-bold text-dark mb-1">Delivery / Arrival</h6>
                                <p class="text-muted small mb-0">{{ $simulation['destination']->name }}</p>
                                <span class="badge bg-success rounded-pill mt-2">
                                    {{ \Carbon\Carbon::parse(request('departure_date', date('Y-m-d')))->addDays($simulation['finalEta'])->format('M d, Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="row g-4 h-100">
                    {{-- ==========================================
                        SECTION 5: RISK ANALYSIS
                    ========================================== --}}
                    <div class="col-md-6">
                        <div class="card enterprise-card h-100">
                            <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                                <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart-steps text-primary me-2"></i> Risk Analysis</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex flex-column gap-3">
                                    <div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted fw-bold small">Weather Risk</span>
                                            <span class="fw-bold small">{{ $simulation['destination']->weather_risk }}%</span>
                                        </div>
                                        <div class="progress rounded-pill bg-light" style="height: 8px;">
                                            <div class="progress-bar bg-info rounded-pill" style="width: {{ $simulation['destination']->weather_risk }}%;"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted fw-bold small">Political Risk</span>
                                            <span class="fw-bold small">{{ $simulation['destination']->political_risk }}%</span>
                                        </div>
                                        <div class="progress rounded-pill bg-light" style="height: 8px;">
                                            <div class="progress-bar bg-warning rounded-pill" style="width: {{ $simulation['destination']->political_risk }}%;"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted fw-bold small">Logistics Risk</span>
                                            <span class="fw-bold small">{{ $simulation['destination']->logistic_risk }}%</span>
                                        </div>
                                        <div class="progress rounded-pill bg-light" style="height: 8px;">
                                            <div class="progress-bar bg-danger rounded-pill" style="width: {{ $simulation['destination']->logistic_risk }}%;"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted fw-bold small">Port Congestion (Origin)</span>
                                            <span class="fw-bold small">{{ $simulation['origin']->traffic_level == 'High' ? '85' : ($simulation['origin']->traffic_level == 'Medium' ? '50' : '20') }}%</span>
                                        </div>
                                        <div class="progress rounded-pill bg-light" style="height: 8px;">
                                            <div class="progress-bar bg-primary rounded-pill" style="width: {{ $simulation['origin']->traffic_level == 'High' ? '85' : ($simulation['origin']->traffic_level == 'Medium' ? '50' : '20') }}%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ==========================================
                        SECTION 6: DELAY ESTIMATION
                    ========================================== --}}
                    <div class="col-md-6">
                        <div class="card enterprise-card h-100 bg-soft-danger border border-white">
                            <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-0">
                                <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-stopwatch me-2"></i> Delay Estimation</h5>
                            </div>
                            <div class="card-body p-4">
                                @if(count($simulation['delays']) > 0)
                                    <ul class="list-group list-group-flush bg-transparent">
                                        @foreach($simulation['delays'] as $delay)
                                        <li class="list-group-item bg-transparent px-0 py-3 border-danger border-opacity-25 d-flex justify-content-between align-items-center">
                                            <span class="fw-semibold text-dark"><i class="bi bi-exclamation-circle text-danger me-2"></i> {{ $delay['reason'] }}</span>
                                            <span class="badge bg-danger rounded-pill px-3 py-2">+{{ $delay['days'] }} Days</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                    <div class="mt-4 pt-3 border-top border-danger border-opacity-25 d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-dark fs-5">Total Penalty</span>
                                        <span class="fw-bold text-danger fs-4">+{{ $simulation['totalDelay'] }} Days</span>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 60px; height: 60px;">
                                            <i class="bi bi-check-lg fs-2 text-success"></i>
                                        </div>
                                        <h5 class="fw-bold text-success">Optimal Conditions</h5>
                                        <p class="text-muted mb-0">No active delays detected for this route.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5 fade-in">
            {{-- ==========================================
                SECTION 8: ALTERNATIVE ROUTES
            ========================================== --}}
            <div class="col-12">
                <div class="card enterprise-card h-100 overflow-hidden">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-shuffle text-primary me-2"></i> Route Alternatives</h5>
                    </div>
                    <div class="card-body p-0">
                        @if($simulation['routes'] && $simulation['routes']->count() > 0)
                            <div class="table-responsive px-4 pb-4">
                                <table class="table enterprise-table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Route ID</th>
                                            <th>Distance</th>
                                            <th>Base ETA</th>
                                            <th>Risk</th>
                                            <th>Shipping Cost</th>
                                            <th>Recommendation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($simulation['routes'] as $index => $route)
                                            <tr>
                                                <td><span class="fw-bold text-dark">RTE-{{ $route->id }}</span></td>
                                                <td><span class="fw-semibold text-muted">{{ number_format($route->distance_km) }} km</span></td>
                                                <td><span class="fw-semibold text-muted">{{ $route->estimated_days }} Days</span></td>
                                                <td>
                                                    @if($route->risk_level == 'Low')
                                                        <span class="badge bg-soft-success text-success rounded-pill">Low</span>
                                                    @elseif($route->risk_level == 'Medium')
                                                        <span class="badge bg-soft-warning text-dark rounded-pill">Medium</span>
                                                    @else
                                                        <span class="badge bg-soft-danger text-danger rounded-pill">High</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-dark">USD {{ number_format(500 + ($route->distance_km * 0.5)) }}</span>
                                                </td>
                                                <td>
                                                    @if($index == 0)
                                                        <span class="badge bg-primary rounded-pill px-3 py-1 shadow-sm"><i class="bi bi-star-fill text-warning me-1"></i> Best Route</span>
                                                    @else
                                                        <span class="text-muted small">Alternative</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm border border-white" style="width: 80px; height: 80px;">
                                    <i class="bi bi-signpost-split fs-1 text-muted"></i>
                                </div>
                                <h5 class="fw-bold text-dark">No Direct Alternatives</h5>
                                <p class="text-muted mb-0">Simulated path generated via geospatial calculation.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @endif

</div>

{{-- ==========================================
        SIMULATION HISTORY
    ========================================== --}}
    @if(isset($history) && $history->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <div class="card enterprise-card fade-in">
                <div class="card-header pb-0">
                    <h5 class="fw-bold mb-0">Simulation History</h5>
                    <p class="text-muted small">Your past trade route simulations.</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Route (Origin &rarr; Destination)</th>
                                    <th>ETA</th>
                                    <th>Risk Level</th>
                                    <th>Date</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($history as $h)
                                <tr>
                                    <td><span class="text-muted fw-medium">#{{ $h->id }}</span></td>
                                    <td>
                                        <div class="fw-bold text-dark">
                                            {{ $h->originPort->port_name ?? 'N/A' }}, {{ $h->originCountry->country_name ?? 'N/A' }}
                                        </div>
                                        <div class="text-muted small">
                                            &rarr; {{ $h->destinationPort->port_name ?? 'N/A' }}, {{ $h->destinationCountry->country_name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i> {{ $h->estimated_duration }} Days</span>
                                    </td>
                                    <td>
                                        @if($h->risk_level == 'Critical' || $h->risk_level == 'High')
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $h->risk_level }}</span>
                                        @elseif($h->risk_level == 'Medium')
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning"><i class="bi bi-exclamation-circle-fill me-1"></i> Medium</span>
                                        @else
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="bi bi-check-circle-fill me-1"></i> Low</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $h->created_at->format('M d, Y') }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('reports.trade-planner.single-pdf', $h->id) }}" class="btn btn-sm btn-outline-danger fw-semibold">
                                                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif


@endsection

    @push('scripts')
<style>
/* ==========================================
   ENTERPRISE DASHBOARD STYLES
========================================== */

.enterprise-title { font-size: 28px; letter-spacing: -0.5px; }

/* Hero Icon */
.hero-icon-container {
    width: 76px;
    height: 76px;
    border-radius: 50%;
    background: linear-gradient(135deg, #F59E0B 0%, #FBBF24 100%);
    box-shadow: 0 8px 20px rgba(245, 158, 11, 0.25);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hero-icon-container:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 25px rgba(245, 158, 11, 0.35);
}
.hero-icon {
    font-size: 36px;
}

/* Premium Cards */
.enterprise-card {
    border: none;
    border-radius: 18px;
    background: #fff;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.enterprise-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
}

/* Forms */
.enterprise-select, .enterprise-input {
    border-radius: 10px;
    border: 1px solid #dee2e6;
    padding: 12px 15px;
    font-weight: 500;
    transition: all 0.2s ease;
    background-color: #f8f9fa;
}
.enterprise-select:focus, .enterprise-input:focus {
    background-color: #fff;
    border-color: #fd7e14;
    box-shadow: 0 0 0 0.25rem rgba(253, 126, 20, 0.25);
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
}
.enterprise-table td { padding: 16px; vertical-align: middle; border-bottom: 1px solid #f1f3f5; }
.enterprise-table tbody tr { transition: background-color 0.2s ease; }
.enterprise-table tbody tr:hover { background-color: #f8f9fa; }

/* Map specific styling */
.enterprise-map {
    box-shadow: inset 0 0 10px rgba(0,0,0,0.05);
    border: 1px solid #f1f3f5;
}

/* Animations */
.fade-in {
    animation: fadeIn 0.6s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}

</style>

<script>
document.addEventListener("DOMContentLoaded", function() {

    // 1. Dynamic Dropdown Logic
    const countriesData = @json($countries);
    const originCountry = document.getElementById('origin_country');
    const originPort = document.getElementById('origin_port');
    const destCountry = document.getElementById('destination_country');
    const destPort = document.getElementById('destination_port');

    function populatePorts(countrySelect, portSelect, selectedPortId = null) {
        const countryId = countrySelect.value;
        portSelect.innerHTML = '<option value="">Select Port...</option>';
        if (countryId) {
            const country = countriesData.find(c => c.id == countryId);
            if (country && country.ports) {
                country.ports.forEach(port => {
                    const option = document.createElement('option');
                    option.value = port.id;
                    option.textContent = port.name + ' (' + port.code + ')';
                    if (selectedPortId && selectedPortId == port.id) option.selected = true;
                    portSelect.appendChild(option);
                });
            }
        }
    }

    originCountry.addEventListener('change', () => populatePorts(originCountry, originPort));
    destCountry.addEventListener('change', () => populatePorts(destCountry, destPort));

    // Restore selected values if form was submitted
    @if(request('origin_port'))
        // Need to find which country the origin port belongs to
        let originCountryId = null;
        countriesData.forEach(c => {
            if(c.ports.find(p => p.id == {{ request('origin_port') }})) originCountryId = c.id;
        });
        if(originCountryId) {
            originCountry.value = originCountryId;
            populatePorts(originCountry, originPort, {{ request('origin_port') }});
        }
    @endif

    @if(request('destination_port'))
        let destCountryId = null;
        countriesData.forEach(c => {
            if(c.ports.find(p => p.id == {{ request('destination_port') }})) destCountryId = c.id;
        });
        if(destCountryId) {
            destCountry.value = destCountryId;
            populatePorts(destCountry, destPort, {{ request('destination_port') }});
        }
    @endif


    // 2. Leaflet Map Initialization
    @if($simulation)
        const lat1 = {{ $simulation['origin']->latitude }};
        const lon1 = {{ $simulation['origin']->longitude }};
        const lat2 = {{ $simulation['destination']->latitude }};
        const lon2 = {{ $simulation['destination']->longitude }};
        
        const map = L.map('planner-map');

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        const portIcon = L.icon({
            iconUrl: '/images/map/port-marker.svg',
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32],
        });
        const shipIcon = L.icon({
            iconUrl: '/images/map/container-ship.svg',
            iconSize: [40, 40],
            iconAnchor: [20, 20],
        });

        L.marker([lat1, lon1]).addTo(map).bindPopup("<b>Origin:</b> {{ $simulation['origin']->name }}");
        L.marker([lat2, lon2]).addTo(map).bindPopup("<b>Destination:</b> {{ $simulation['destination']->name }}");

        const latlngs = [ [lat1, lon1], [lat2, lon2] ];
        const polyline = L.polyline(latlngs, {color: '#fd7e14', weight: 4, dashArray: '10, 10'}).addTo(map);

        map.fitBounds(polyline.getBounds(), {padding: [50, 50]});

        // Ship Marker placed at midpoint for visual appeal
        const midLat = (lat1 + lat2) / 2;
        const midLon = (lon1 + lon2) / 2;
        L.marker([midLat, midLon], {icon: shipIcon}).addTo(map);
    @endif

});
</script>
@endpush
