<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\trade-planner\index.blade.php';
$content = file_get_contents($file);

$patternToRemove = <<<'EOD'
            {{-- ==========================================
                SECTION 7: AI RECOMMENDATION
            ========================================== --}}
            <div class="col-lg-4">
                <div class="card enterprise-card h-100 bg-dark text-white shadow-lg overflow-hidden position-relative">
                    <div class="position-absolute end-0 top-0 p-4 opacity-25">
                        <i class="bi bi-robot" style="font-size: 120px;"></i>
                    </div>
                    <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-2 position-relative z-1">
                        <h5 class="fw-bold mb-0 text-white"><i class="bi bi-stars text-warning me-2"></i> AI Recommendation</h5>
                    </div>
                    <div class="card-body p-4 position-relative z-1 d-flex flex-column justify-content-between">
                        <div>
                            <span class="badge bg-{{ $simulation['recommendation']['badge'] }} rounded-pill mb-3 px-3 py-2 fw-bold shadow-sm fs-6">
                                {{ $simulation['recommendation']['status'] }}
                            </span>
                            <p class="fs-5 fw-light lh-base mb-4">"{{ $simulation['recommendation']['reason'] }}"</p>
                        </div>
                        <div class="p-3 bg-white bg-opacity-10 rounded-4">
                            <small class="text-white-50 d-block mb-1">Actionable Insight</small>
                            <strong class="text-white"><i class="bi bi-arrow-right-circle-fill text-{{ $simulation['recommendation']['badge'] }} me-2"></i>{{ $simulation['recommendation']['action'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==========================================
                SECTION 8: ALTERNATIVE ROUTES
            ========================================== --}}
            <div class="col-lg-8">
EOD;

$replacementContent = <<<'EOD'
            {{-- ==========================================
                SECTION 8: ALTERNATIVE ROUTES
            ========================================== --}}
            <div class="col-12">
EOD;

$content = str_replace($patternToRemove, $replacementContent, $content);
file_put_contents($file, $content);
echo "AI Recommendation UI removed and layout fixed.\n";
