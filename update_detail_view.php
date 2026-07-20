<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\reports\country-comparison-detail.blade.php';
$content = file_get_contents($file);

// 1. Remove PDF & Excel buttons
$buttonsOld = <<<'EOD'
    <div class="col-md-4 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary rounded-pill px-4"><i class="bi bi-arrow-left me-2"></i> Back to Reports</a>
        <a href="{{ route('compare.pdf', $comp->id) }}" class="btn btn-danger rounded-pill px-4"><i class="bi bi-file-earmark-pdf me-2"></i> Export PDF</a>
        <a href="{{ route('compare.excel', $comp->id) }}" class="btn btn-success rounded-pill px-4"><i class="bi bi-file-earmark-excel me-2"></i> Export Excel</a>
    </div>
EOD;

$buttonsNew = <<<'EOD'
    <div class="col-md-4 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
        <a href="{{ route('compare.history') }}" class="btn btn-outline-secondary rounded-pill px-4"><i class="bi bi-arrow-left me-2"></i> Back to History</a>
    </div>
EOD;
$content = str_replace($buttonsOld, $buttonsNew, $content);

// 2. Fix Country Information A
$infoAOld = <<<'EOD'
                <div class="d-flex align-items-center mb-3">
                    <img src="https://flagcdn.com/w80/{{ $res['flag_a'] ?? 'xx' }}.png" width="40" class="shadow-sm rounded me-3" alt="Flag">
                    <h5 class="mb-0 fw-bold">{{ $comp->countryA->country_name ?? 'N/A' }}</h5>
                </div>
                <div class="row g-3">
                    <div class="col-6"><small class="text-muted">Capital</small><br><span class="fw-semibold">{{ $res['capital_a'] ?? '-' }}</span></div>
                    <div class="col-6"><small class="text-muted">Region</small><br><span class="fw-semibold">{{ $res['region_a'] ?? '-' }}</span></div>
                    <div class="col-6"><small class="text-muted">Population</small><br><span class="fw-semibold">{{ number_format($res['population_a'] ?? 0) }}</span></div>
                    <div class="col-6"><small class="text-muted">Currency</small><br><span class="fw-semibold">{{ $res['currency_code_a'] ?? '-' }}</span></div>
                    <div class="col-12"><small class="text-muted">Language</small><br><span class="fw-semibold">{{ $res['language_a'] ?? '-' }}</span></div>
                </div>
EOD;

$infoANew = <<<'EOD'
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ $comp->countryA->flag ?: 'https://flagcdn.com/w80/'.strtolower($comp->countryA->country_code).'.png' }}" width="40" class="shadow-sm rounded me-3" alt="Flag" onerror="this.src='https://flagcdn.com/w80/{{ strtolower($comp->countryA->country_code) }}.png'">
                    <h5 class="mb-0 fw-bold">{{ $comp->countryA->country_name ?? 'N/A' }}</h5>
                </div>
                <div class="row g-3">
                    <div class="col-6"><small class="text-muted">Capital</small><br><span class="fw-semibold">{{ $comp->countryA->capital ?? '-' }}</span></div>
                    <div class="col-6"><small class="text-muted">Region</small><br><span class="fw-semibold">{{ $comp->countryA->region ?? '-' }}</span></div>
                    <div class="col-6"><small class="text-muted">Population</small><br><span class="fw-semibold">{{ number_format($comp->countryA->population ?? 0) }}</span></div>
                    <div class="col-6"><small class="text-muted">Currency</small><br><span class="fw-semibold">{{ $comp->countryA->currency_name ?? $comp->countryA->currency_code ?? '-' }}</span></div>
                    <div class="col-12"><small class="text-muted">Language</small><br><span class="fw-semibold">{{ $comp->countryA->language ?? '-' }}</span></div>
                </div>
EOD;
$content = str_replace($infoAOld, $infoANew, $content);

// 3. Fix Country Information B
$infoBOld = <<<'EOD'
                <div class="d-flex align-items-center mb-3">
                    <img src="https://flagcdn.com/w80/{{ $res['flag_b'] ?? 'xx' }}.png" width="40" class="shadow-sm rounded me-3" alt="Flag">
                    <h5 class="mb-0 fw-bold">{{ $comp->countryB->country_name ?? 'N/A' }}</h5>
                </div>
                <div class="row g-3">
                    <div class="col-6"><small class="text-muted">Capital</small><br><span class="fw-semibold">{{ $res['capital_b'] ?? '-' }}</span></div>
                    <div class="col-6"><small class="text-muted">Region</small><br><span class="fw-semibold">{{ $res['region_b'] ?? '-' }}</span></div>
                    <div class="col-6"><small class="text-muted">Population</small><br><span class="fw-semibold">{{ number_format($res['population_b'] ?? 0) }}</span></div>
                    <div class="col-6"><small class="text-muted">Currency</small><br><span class="fw-semibold">{{ $res['currency_code_b'] ?? '-' }}</span></div>
                    <div class="col-12"><small class="text-muted">Language</small><br><span class="fw-semibold">{{ $res['language_b'] ?? '-' }}</span></div>
                </div>
EOD;

$infoBNew = <<<'EOD'
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ $comp->countryB->flag ?: 'https://flagcdn.com/w80/'.strtolower($comp->countryB->country_code).'.png' }}" width="40" class="shadow-sm rounded me-3" alt="Flag" onerror="this.src='https://flagcdn.com/w80/{{ strtolower($comp->countryB->country_code) }}.png'">
                    <h5 class="mb-0 fw-bold">{{ $comp->countryB->country_name ?? 'N/A' }}</h5>
                </div>
                <div class="row g-3">
                    <div class="col-6"><small class="text-muted">Capital</small><br><span class="fw-semibold">{{ $comp->countryB->capital ?? '-' }}</span></div>
                    <div class="col-6"><small class="text-muted">Region</small><br><span class="fw-semibold">{{ $comp->countryB->region ?? '-' }}</span></div>
                    <div class="col-6"><small class="text-muted">Population</small><br><span class="fw-semibold">{{ number_format($comp->countryB->population ?? 0) }}</span></div>
                    <div class="col-6"><small class="text-muted">Currency</small><br><span class="fw-semibold">{{ $comp->countryB->currency_name ?? $comp->countryB->currency_code ?? '-' }}</span></div>
                    <div class="col-12"><small class="text-muted">Language</small><br><span class="fw-semibold">{{ $comp->countryB->language ?? '-' }}</span></div>
                </div>
EOD;
$content = str_replace($infoBOld, $infoBNew, $content);

file_put_contents($file, $content);
echo "Detail view updated successfully.\n";
