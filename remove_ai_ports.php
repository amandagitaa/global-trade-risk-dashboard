<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\ports\show.blade.php';
$content = file_get_contents($file);

$patternToRemove = <<<'EOD'
        {{-- Risk Dashboard --}}
        <div class="col-lg-7">
EOD;

$replacementContent = <<<'EOD'
        {{-- Risk Dashboard --}}
        <div class="col-12">
EOD;

$content = str_replace($patternToRemove, $replacementContent, $content);

$patternToRemove2 = <<<'EOD'
        {{-- AI Recommendation --}}
        <div class="col-lg-5">
            <div class="card enterprise-card h-100 bg-dark text-white shadow-lg overflow-hidden position-relative">
                <div class="position-absolute end-0 top-0 p-4 opacity-25">
                    <i class="bi bi-robot" style="font-size: 120px;"></i>
                </div>
                <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-2 position-relative z-1">
                    <h5 class="fw-bold mb-0 text-white"><i class="bi bi-stars text-warning me-2"></i> AI Trade Recommendation</h5>
                </div>
                <div class="card-body p-4 position-relative z-1 d-flex flex-column justify-content-between">
                    <div>
                        <div class="mb-4">
                            <span class="badge bg-warning text-dark rounded-pill mb-3 px-3 py-2 fw-bold shadow-sm">Executive Summary</span>
                            <p class="fs-5 fw-light lh-base">{{ $port->ai_recommendation }}</p>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 bg-white bg-opacity-10 rounded-4 h-100">
                                <small class="text-white-50 d-block mb-1">Priority</small>
                                <strong class="text-white"><i class="bi bi-arrow-up-circle-fill text-warning me-1"></i> High Action</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-white bg-opacity-10 rounded-4 h-100">
                                <small class="text-white-50 d-block mb-1">Confidence Score</small>
                                <strong class="text-white"><i class="bi bi-check-circle-fill text-success me-1"></i> 94% Reliable</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
EOD;

$content = str_replace($patternToRemove2, "", $content);

file_put_contents($file, $content);
echo "AI Trade Recommendation removed from Port show view.\n";
