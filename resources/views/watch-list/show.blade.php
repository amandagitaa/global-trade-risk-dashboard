@extends('layouts.app')

@section('content')

@php
    $itemName = 'Unknown';
    $location = 'Unknown';
    $risk = 'N/A';
    $weather = 'N/A';
    $currency = 'N/A';

    if ($watchList->watch_type === 'country') {
        $itemName = $watchList->country->country_name ?? 'Unknown Country';
        $location = $watchList->country->region ?? '-';
        $risk = $watchList->country->latestRisk->risk_level ?? 'Low';
        $weather = $watchList->country->latestWeather->weather_status ?? 'Clear';
        $currency = $watchList->country->latestCurrency->target_currency ?? '-';
    } elseif ($watchList->watch_type === 'port') {
        $itemName = $watchList->port->port_name ?? 'Unknown Port';
        $location = ($watchList->port->country->country_name ?? '') . ' / ' . ($watchList->port->city ?? '');
        $risk = $watchList->port->risk_level ?? 'Low';
        $weather = $watchList->port->weather_status ?? 'Clear';
    } elseif ($watchList->watch_type === 'route') {
        $origin = $watchList->route->originPort->port_name ?? 'Unknown';
        $dest = $watchList->route->destinationPort->port_name ?? 'Unknown';
        $itemName = "$origin to $dest";
        $location = 'Maritime Route';
        $risk = $watchList->route->risk_level ?? 'Low';
        $weather = $watchList->route->weather_condition ?? 'Clear';
    }
@endphp

<div class="container-fluid px-4 py-5">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-5 fade-in">
        <div>
            <h2 class="fw-bold text-dark mb-2 display-6">
                <i class="bi bi-eye text-primary me-2"></i> Watch List Detail
            </h2>
            <p class="text-muted fs-5 mb-0">Detailed view of your monitored item.</p>
        </div>
        <a href="{{ route('watch-list.index') }}" class="btn btn-light enterprise-btn fw-bold">
            <i class="bi bi-arrow-left me-2"></i> Back to Watch List
        </a>
    </div>

    {{-- Detail Card --}}
    <div class="row fade-in" style="animation-delay: 0.1s;">
        <div class="col-lg-8 mx-auto">
            <div class="card enterprise-card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-2">
                    <h4 class="fw-bold mb-0 text-primary">Monitoring Details</h4>
                </div>
                <div class="card-body p-4">
                    <table class="table table-borderless table-hover align-middle mb-0 fs-5">
                        <tbody>
                            <tr>
                                <th class="text-muted w-35" style="width: 35%;">Item Name</th>
                                <td class="fw-bold text-dark">{{ $itemName }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Monitoring Type</th>
                                <td><span class="badge bg-soft-primary text-primary text-capitalize rounded-pill px-3">{{ $watchList->watch_type }}</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Location / Region</th>
                                <td class="fw-semibold text-secondary">{{ $location }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Risk Status</th>
                                <td>
                                    @if(strtolower($risk) == 'high' || strtolower($risk) == 'critical')
                                        <span class="badge bg-soft-danger text-danger rounded-pill px-3"><i class="bi bi-exclamation-triangle me-1"></i> {{ $risk }}</span>
                                    @elseif(strtolower($risk) == 'medium')
                                        <span class="badge bg-soft-warning text-dark rounded-pill px-3">{{ $risk }}</span>
                                    @else
                                        <span class="badge bg-soft-success text-success rounded-pill px-3">{{ $risk }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Weather Conditions</th>
                                <td>
                                    @if(str_contains(strtolower($weather), 'storm') || str_contains(strtolower($weather), 'extreme'))
                                        <span class="badge bg-soft-danger text-danger rounded-pill px-3"><i class="bi bi-cloud-lightning-rain me-1"></i> {{ ucfirst($weather) }}</span>
                                    @elseif(str_contains(strtolower($weather), 'rain'))
                                        <span class="badge bg-soft-primary text-primary rounded-pill px-3"><i class="bi bi-cloud-rain me-1"></i> {{ ucfirst($weather) }}</span>
                                    @else
                                        <span class="badge bg-soft-success text-success rounded-pill px-3"><i class="bi bi-sun me-1"></i> {{ ucfirst($weather) }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Currency (Country)</th>
                                <td class="fw-semibold text-dark">{{ $currency }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Monitoring Status</th>
                                <td><span class="badge bg-soft-info text-info rounded-pill px-3 text-capitalize"><i class="bi bi-activity me-1"></i> {{ $watchList->status }}</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Created At</th>
                                <td class="text-secondary">{{ $watchList->created_at->format('d M Y, H:i') }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Last Updated</th>
                                <td class="text-secondary">{{ $watchList->updated_at->format('d M Y, H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
