<?php

$content = file_get_contents('C:\laragon\www\global-trade-risk-dashboard\resources\views\trade-planner\index.blade.php');

$historyHtml = <<<HTML
    {{-- ==========================================
        SIMULATION HISTORY
    ========================================== --}}
    @if(isset(\$history) && \$history->count() > 0)
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
                                @foreach(\$history as \$h)
                                <tr>
                                    <td><span class="text-muted fw-medium">#{{ \$h->id }}</span></td>
                                    <td>
                                        <div class="fw-bold text-dark">
                                            {{ \$h->originPort->port_name ?? 'N/A' }}, {{ \$h->originCountry->country_name ?? 'N/A' }}
                                        </div>
                                        <div class="text-muted small">
                                            &rarr; {{ \$h->destinationPort->port_name ?? 'N/A' }}, {{ \$h->destinationCountry->country_name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i> {{ \$h->estimated_duration }} Days</span>
                                    </td>
                                    <td>
                                        @if(\$h->risk_level == 'Critical' || \$h->risk_level == 'High')
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ \$h->risk_level }}</span>
                                        @elseif(\$h->risk_level == 'Medium')
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning"><i class="bi bi-exclamation-circle-fill me-1"></i> Medium</span>
                                        @else
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="bi bi-check-circle-fill me-1"></i> Low</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ \$h->created_at->format('M d, Y') }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('reports.trade-planner.single-pdf', \$h->id) }}" class="btn btn-sm btn-outline-danger fw-semibold">
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
HTML;

$content = str_replace("@push('scripts')", $historyHtml . "\n\n@push('scripts')", $content);

file_put_contents('C:\laragon\www\global-trade-risk-dashboard\resources\views\trade-planner\index.blade.php', $content);
echo "Trade Planner view updated.\n";
