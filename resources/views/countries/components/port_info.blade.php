<div class="card border-0 shadow-sm rounded-4 w-100 d-flex flex-column h-100">
    <div class="card-header bg-white border-0 pb-0">
        <h5 class="fw-bold text-orange">
            ⚓ Port Information
        </h5>
        @if(count($ports) > 0)
            <p class="text-muted small mb-0">Logistics and port infrastructure summary.</p>
        @else
            <p class="text-muted small mb-0">Landlocked Country. This country has no direct seaport access.</p>
        @endif
    </div>
    <div class="card-body d-flex flex-column justify-content-between">
        @if(count($ports) > 0)
            <!-- KPI Mini Cards -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6">
                    <div class="card border border-light shadow-sm rounded-3 h-100 p-2">
                        <small class="text-secondary fw-medium">Total Ports</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ count($ports) }}</h4>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="card border border-light shadow-sm rounded-3 h-100 p-2">
                        <small class="text-secondary fw-medium">Active Ships</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $ships }}</h4>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="card border border-light shadow-sm rounded-3 h-100 p-2">
                        <small class="text-secondary fw-medium">Shipping Routes</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $shippingRoutes }}</h4>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="card border border-light shadow-sm rounded-3 h-100 p-2">
                        <small class="text-secondary fw-medium">Connected Countries</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $connectedCountries }}</h4>
                    </div>
                </div>
            </div>

            <!-- Detail Information -->
            <div class="d-flex flex-column gap-2 mt-2">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                    <span class="text-secondary fw-medium">Average Capacity</span>
                    <span class="fw-bold">{{ $averageCapacity }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                    <span class="text-secondary fw-medium">Main Export Gateway</span>
                    <span class="fw-bold text-end">{{ $mainExportGateway }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                    <span class="text-secondary fw-medium">Main Import Gateway</span>
                    <span class="fw-bold text-end">{{ $mainImportGateway }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                    <span class="text-secondary fw-medium">Operational Status</span>
                    @php
                        $statusColor = 'success';
                        if($operationalStatus == 'Closed') $statusColor = 'danger';
                        if($operationalStatus == 'Congested') $statusColor = 'warning';
                    @endphp
                    <span class="badge bg-{{ $statusColor }}">
                        {{ $operationalStatus }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-secondary fw-medium">Port Risk Level</span>
                    @php
                        $riskColor = 'success';
                        if($riskLevel == 'High') $riskColor = 'danger';
                        if($riskLevel == 'Medium') $riskColor = 'warning';
                    @endphp
                    <span class="badge bg-{{ $riskColor }}">
                        {{ $riskLevel }}
                    </span>
                </div>
            </div>
        @else
            <table class="table table-borderless align-middle mb-4">
                <tr>
                    <th width="180" class="text-secondary fw-medium">🌍 Country Type</th>
                    <td class="fw-bold">Landlocked</td>
                </tr>
                <tr>
                    <th class="text-secondary fw-medium">⚓ Maritime Access</th>
                    <td class="fw-bold">Depends on neighboring countries</td>
                </tr>
                <tr>
                    <th class="text-secondary fw-medium">🌐 Trade Gateway</th>
                    <td class="fw-bold">Nearest international ports</td>
                </tr>
                <tr>
                    <th class="text-secondary fw-medium">⚠ Port Risk Level</th>
                    <td><span class="badge bg-secondary">N/A</span></td>
                </tr>
            </table>

            <h6 class="fw-bold mb-3 text-secondary">Nearest Maritime Gateway</h6>
            
            @if(isset($nearestPorts) && $nearestPorts->count() > 0)
                <ul class="list-group list-group-flush border-top">
                    @foreach($nearestPorts as $nPort)
                        <li class="list-group-item px-0 py-2 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 fw-bold">⚓ {{ $nPort->name }}</h6>
                                    <small class="text-muted">{{ $nPort->country->country_name ?? 'Unknown Country' }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-light text-dark border">
                                        Distance: {{ number_format($nPort->distance, 0) }} km
                                    </span>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="alert alert-light border text-muted small">
                    No ports found in proximity.
                </div>
            @endif
        @endif
    </div>
</div>
