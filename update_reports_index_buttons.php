<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\reports\index.blade.php';
$content = file_get_contents($file);

$oldRow = <<<'EOD'
                        <div class="d-flex gap-2 flex-wrap justify-content-end">
                            <a href="{{ route('compare.history') }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-eye me-2"></i> View History
                            </a>
                        </div>
EOD;

$newRow = <<<'EOD'
                        <div class="d-flex gap-2 flex-wrap justify-content-end">
                            <a href="{{ route('compare.history') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-eye me-1"></i> View
                            </a>
                            <a href="{{ route('compare.pdf.all') }}" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                            </a>
                            <a href="{{ route('compare.excel.all') }}" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                            </a>
                        </div>
EOD;

$content = str_replace($oldRow, $newRow, $content);
file_put_contents($file, $content);
echo "index.blade.php updated.\n";
