@extends('layouts.app')

@section('title', 'Watch List - Global Trade Intelligence')

@section('content')

@php
if (!function_exists('riskClass')) {
    function riskClass($r) {
        if (in_array(strtolower($r), ['low', 'safe'])) return 'success';
        if (in_array(strtolower($r), ['medium', 'monitoring', 'volatile'])) return 'warning text-dark';
        if (in_array(strtolower($r), ['high', 'warning'])) return 'danger';
        return 'dark'; // critical
    }
}

if (!function_exists('weatherClass')) {
    function weatherClass($w) {
        $w = strtolower($w);
        if (str_contains($w, 'clear') || str_contains($w, 'sunny')) return 'success';
        if (str_contains($w, 'rain')) return 'info text-dark';
        if (str_contains($w, 'storm')) return 'warning text-dark';
        if (str_contains($w, 'extreme')) return 'danger';
        return 'secondary';
    }
}
@endphp

<div class="container-fluid mb-5">

    {{-- ==========================================
        HEADER
    ========================================== --}}
    <div class="d-flex align-items-center mb-4">
        <div class="bg-soft-primary rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
            <i class="bi bi-star-fill fs-2 text-primary"></i>
        </div>
        <div>
            <h2 class="fw-bold mb-1 enterprise-title text-dark">Watch List</h2>
            <p class="text-muted mb-0">Monitor selected countries, ports, and trade routes.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ==========================================
        SECTION 1: SUMMARY DASHBOARD
    ========================================== --}}
    <div class="row g-4 mb-5 fade-in">
        {{-- Card 1: Total Watch Items --}}
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card enterprise-card h-100">
                <div class="card-body p-4 d-flex flex-column align-items-center text-center justify-content-center">
                    <div class="bg-soft-primary rounded-circle p-3 mb-3 d-flex align-items-center justify-content-center" style="width:64px; height:64px;">
                        <i class="bi bi-bookmark-star fs-2 text-primary"></i>
                    </div>
                    <small class="text-muted fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Watch Items</small>
                    <h1 class="fw-bold text-dark mb-0 display-6">{{ $totalItems }}</h1>
                    <small class="text-muted mt-1 fw-semibold">Items</small>
                </div>
            </div>
        </div>

        {{-- Card 2: High Risk Monitoring --}}
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card enterprise-card h-100">
                <div class="card-body p-4 d-flex flex-column align-items-center text-center justify-content-center">
                    <div class="bg-soft-danger rounded-circle p-3 mb-3 d-flex align-items-center justify-content-center" style="width:64px; height:64px;">
                        <i class="bi bi-exclamation-triangle fs-2 text-danger"></i>
                    </div>
                    <small class="text-muted fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">High Risk Monitoring</small>
                    <h1 class="fw-bold text-dark mb-0 display-6">{{ $highRiskItems }}</h1>
                    <small class="text-muted mt-1 fw-semibold">High Risk</small>
                </div>
            </div>
        </div>

        {{-- Card 3: Weather Alert --}}
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card enterprise-card h-100">
                <div class="card-body p-4 d-flex flex-column align-items-center text-center justify-content-center">
                    <div class="bg-soft-warning rounded-circle p-3 mb-3 d-flex align-items-center justify-content-center" style="width:64px; height:64px;">
                        <i class="bi bi-cloud-lightning fs-2 text-warning"></i>
                    </div>
                    <small class="text-muted fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Weather Alert</small>
                    <h1 class="fw-bold text-dark mb-0 display-6">{{ $weatherAlerts }}</h1>
                    <small class="text-muted mt-1 fw-semibold">Alerts</small>
                </div>
            </div>
        </div>

        {{-- Card 4: Trade Opportunity --}}
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card enterprise-card h-100">
                <div class="card-body p-4 d-flex flex-column align-items-center text-center justify-content-center">
                    <div class="bg-soft-success rounded-circle p-3 mb-3 d-flex align-items-center justify-content-center" style="width:64px; height:64px;">
                        <i class="bi bi-graph-up-arrow fs-2 text-success"></i>
                    </div>
                    <small class="text-muted fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Trade Opportunity</small>
                    <h1 class="fw-bold text-dark mb-0 display-6">{{ $opportunities }}</h1>
                    <small class="text-muted mt-1 fw-semibold">Opportunities</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5 fade-in">
        
        {{-- ==========================================
            SECTION 2 & 3: FILTER & TABLE
        ========================================== --}}
        <div class="col-lg-9">
            <div class="card enterprise-card h-100 overflow-hidden">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-table text-primary me-2"></i> Watch List Data</h5>
                        
                        {{-- Filter --}}
                        <form action="{{ route('watch-list.index') }}" method="GET" class="d-flex gap-2">
                            <select name="watch_type" class="form-select enterprise-input rounded-pill py-2" onchange="this.form.submit()">
                                <option value="all" {{ request('watch_type') == 'all' ? 'selected' : '' }}>All Types</option>
                                <option value="country" {{ request('watch_type') == 'country' ? 'selected' : '' }}>Country</option>
                                <option value="port" {{ request('watch_type') == 'port' ? 'selected' : '' }}>Port</option>
                                <option value="route" {{ request('watch_type') == 'route' ? 'selected' : '' }}>Route</option>
                            </select>
                            <select name="status" class="form-select enterprise-input rounded-pill py-2" onchange="this.form.submit()">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                                <option value="monitoring" {{ request('status') == 'monitoring' ? 'selected' : '' }}>Monitoring</option>
                                <option value="safe" {{ request('status') == 'safe' ? 'selected' : '' }}>Safe</option>
                                <option value="warning" {{ request('status') == 'warning' ? 'selected' : '' }}>Warning</option>
                                <option value="critical" {{ request('status') == 'critical' ? 'selected' : '' }}>High Risk</option>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive px-4 pb-4">
                        <table class="table enterprise-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Type</th>
                                    <th>Country</th>
                                    <th>Risk</th>
                                    <th>Weather</th>
                                    <th>Currency</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($watchLists as $item)
                                    @php
                                        // Dynamic Extraction
                                        $itemName = '';
                                        $countryName = '-';
                                        $risk = 'Low';
                                        $weather = 'Clear';
                                        $currency = 'Stable';

                                        if ($item->watch_type === 'country' && $item->country) {
                                            $itemName = $item->country->country_name;
                                            $countryName = $item->country->country_name;
                                            $riskScore = $item->country->latestRisk->score ?? 0;
                                            if ($riskScore > 75) $risk = 'Critical';
                                            elseif ($riskScore > 50) $risk = 'High';
                                            elseif ($riskScore > 25) $risk = 'Medium';
                                            $weather = $item->country->latestWeather->condition ?? 'Clear';
                                            $currRate = $item->country->latestCurrency->exchange_rate ?? 1;
                                            if ($currRate > 15000) $currency = 'Critical';
                                            elseif ($currRate > 10000) $currency = 'Volatile';
                                        } 
                                        elseif ($item->watch_type === 'port' && $item->port) {
                                            $itemName = $item->port->name;
                                            $countryName = $item->port->country->country_name ?? '-';
                                            $risk = $item->port->risk_level;
                                            $weather = $item->port->country->latestWeather->condition ?? 'Clear';
                                        }
                                        elseif ($item->watch_type === 'route' && $item->route) {
                                            $itemName = $item->route->originPort->name . ' → ' . $item->route->destinationPort->name;
                                            $countryName = $item->route->destinationPort->country->country_name ?? '-';
                                            $risk = $item->route->risk_level;
                                            $weather = $item->route->destinationPort->country->latestWeather->condition ?? 'Clear';
                                        }



                                    @endphp
                                    <tr>
                                        <td><span class="fw-bold text-dark">{{ $itemName }}</span></td>
                                        <td>
                                            <span class="badge bg-light text-muted border text-uppercase">
                                                {{ $item->watch_type }}
                                            </span>
                                        </td>
                                        <td><span class="fw-semibold text-muted">{{ $countryName }}</span></td>
                                        <td>
                                            <span class="badge bg-soft-{{ riskClass($risk) }} text-{{ explode(' ', riskClass($risk))[0] }} rounded-pill">
                                                {{ ucfirst($risk) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-{{ weatherClass($weather) }} text-{{ explode(' ', weatherClass($weather))[0] }} rounded-pill">
                                                {{ ucfirst($weather) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-{{ riskClass($currency) }} text-{{ explode(' ', riskClass($currency))[0] }} rounded-pill">
                                                {{ ucfirst($currency) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ riskClass($item->status) }} rounded-pill shadow-sm">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td><span class="text-muted small">{{ $item->updated_at->format('d M Y') }}</span></td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-2">
                                                <a href="{{ route('watch-list.show', $item->id) }}" class="btn btn-sm btn-light rounded-pill px-3 shadow-sm text-primary fw-bold">View</a>
                                                <form action="{{ route('watch-list.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Remove from Watch List?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light rounded-circle text-danger shadow-sm"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="bi bi-star text-muted fs-1 mb-3 d-block"></i>
                                            <h6 class="fw-bold text-dark">Watch List Empty</h6>
                                            <p class="text-muted mb-0">No items are currently being monitored.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==========================================
            SECTION 4: RECENT ALERTS PANEL
        ========================================== --}}
        <div class="col-lg-3">
            <div class="card enterprise-card h-100 bg-soft-danger border border-white">
                <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-bell me-2"></i> Recent Alerts</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3">
                        @if($weatherAlerts > 0)
                        <div class="alert-item p-3 bg-white rounded-4 shadow-sm border-start border-4 border-danger">
                            <span class="text-danger fw-bold small d-block mb-1"><i class="bi bi-exclamation-triangle me-1"></i> Storm Detected</span>
                            <p class="mb-0 text-dark small fw-semibold">Weather risks identified in monitored locations. Potential delay +3 days.</p>
                        </div>
                        @endif

                        @if($highRiskItems > 0)
                        <div class="alert-item p-3 bg-white rounded-4 shadow-sm border-start border-4 border-warning">
                            <span class="text-warning fw-bold small d-block mb-1"><i class="bi bi-shield-exclamation me-1"></i> Political Risk Increased</span>
                            <p class="mb-0 text-dark small fw-semibold">Monitored risk level shifted to High.</p>
                        </div>
                        @endif

                        <div class="alert-item p-3 bg-white rounded-4 shadow-sm border-start border-4 border-secondary">
                            <span class="text-secondary fw-bold small d-block mb-1"><i class="bi bi-currency-exchange me-1"></i> Currency Volatility</span>
                            <p class="mb-0 text-dark small fw-semibold">Estimated shipping costs may fluctuate.</p>
                        </div>
                        
                        @if($weatherAlerts == 0 && $highRiskItems == 0)
                        <div class="text-center mt-3">
                            <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 50px; height: 50px;">
                                <i class="bi bi-check-lg fs-4 text-success"></i>
                            </div>
                            <h6 class="fw-bold text-success mb-0">All Clear</h6>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<style>
/* ==========================================
   ENTERPRISE DASHBOARD STYLES
========================================== */

.enterprise-title { font-size: 28px; letter-spacing: -0.5px; }

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
.enterprise-input {
    border: 1px solid #e9ecef;
    background-color: #f8f9fa;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
}
.enterprise-input:focus {
    background-color: #fff;
    border-color: #fd7e14;
    box-shadow: 0 0 0 0.2rem rgba(253, 126, 20, 0.2);
}

/* Colors */
.bg-soft-primary { background-color: rgba(253, 126, 20, 0.1); }
.bg-soft-success { background-color: rgba(25, 135, 84, 0.1); }
.bg-soft-warning { background-color: rgba(255, 193, 7, 0.2); }
.bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
.bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }

.text-primary { color: #fd7e14 !important; }

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

/* Animations */
.fade-in {
    animation: fadeIn 0.6s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}

</style>
@endpush
