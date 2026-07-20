<div class="card border-0 shadow-sm rounded-4 h-100">
    <div class="card-header bg-white border-0">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-orange mb-1">
                    🚢 Port Risk Monitoring
                </h5>
                <small class="text-muted">
                    Monitor operational risk of major international ports.
                </small>
            </div>
            <span class="badge bg-danger">
                Top {{ count($portRisks) }} Ports
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light">
                <tr>
                    <th width="60">#</th>
                    <th>Port Name</th>
                    <th>Country</th>
                    <th>Risk Score</th>
                    <th>Risk Level</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse($portRisks as $index => $port)
                    <tr>
                        <td>
                            <strong>{{ $index + 1 }}</strong>
                        </td>
                        <td>
                            <strong>{{ $port->name }}</strong>
                            @if($port->code)
                                <br><small class="text-muted">{{ $port->code }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($port->country && $port->country->flag)
                                    <img src="{{ $port->country->flag }}" width="24" class="me-2 rounded shadow-sm">
                                @endif
                                <span>{{ $port->country_name }}</span>
                            </div>
                        </td>
                        <td>
                            <strong>{{ number_format($port->risk_score, 0) }}</strong>
                        </td>
                        <td>
                            @php
                                $levelLower = strtolower($port->risk_level);
                                $badgeClass = match($levelLower) {
                                    'low' => 'bg-success',
                                    'medium' => 'bg-warning text-dark',
                                    'high' => 'bg-danger',
                                    'critical' => 'badge-critical',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} rounded-pill">
                                {{ ucfirst($port->risk_level) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusLower = strtolower($port->status);
                                $statusBadge = match($statusLower) {
                                    'operational', 'normal' => 'bg-success',
                                    'congested', 'delayed' => 'bg-warning text-dark',
                                    'maintenance' => 'bg-info text-dark',
                                    'restricted' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $statusBadge }} rounded-pill">
                                {{ ucfirst($port->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            No port risk data available.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.badge-critical {
    background-color: #8b0000;
    color: #fff;
}
</style>
