<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\watch-list\index.blade.php';
$content = file_get_contents($file);

$oldSection = <<<'EOD'
    {{-- ==========================================
        SECTION 1: SUMMARY DASHBOARD
    ========================================== --}}
    <div class="row g-4 mb-5 fade-in">
        {{-- Card 1: Total Watch Items --}}
        <div class="col-lg-3 col-md-6">
            <div class="card enterprise-card h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-soft-primary rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width:56px; height:56px;">
                        <i class="bi bi-bookmark-star fs-3 text-primary"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase d-block mb-1">Total Watch Items</small>
                        <h3 class="fw-bold mb-0 text-dark">{{ $totalItems }} Items</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: High Risk Monitoring --}}
        <div class="col-lg-3 col-md-6">
            <div class="card enterprise-card h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-soft-danger rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width:56px; height:56px;">
                        <i class="bi bi-exclamation-triangle fs-3 text-danger"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase d-block mb-1">High Risk Monitoring</small>
                        <h3 class="fw-bold mb-0 text-dark">{{ $highRiskItems }} High Risk</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Weather Alert --}}
        <div class="col-lg-3 col-md-6">
            <div class="card enterprise-card h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-soft-warning rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width:56px; height:56px;">
                        <i class="bi bi-cloud-lightning fs-3 text-warning"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase d-block mb-1">Weather Alert</small>
                        <h3 class="fw-bold mb-0 text-dark">{{ $weatherAlerts }} Alerts</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Trade Opportunity --}}
        <div class="col-lg-3 col-md-6">
            <div class="card enterprise-card h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-soft-success rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width:56px; height:56px;">
                        <i class="bi bi-graph-up-arrow fs-3 text-success"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase d-block mb-1">Trade Opportunity</small>
                        <h3 class="fw-bold mb-0 text-dark">{{ $opportunities }} Opportunities</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
EOD;

$newSection = <<<'EOD'
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
EOD;

$content = str_replace($oldSection, $newSection, $content);

file_put_contents($file, $content);
echo "Watch List Summary Cards UI fixed.\n";
