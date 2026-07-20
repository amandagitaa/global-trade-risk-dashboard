<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\reports\index.blade.php';
$content = file_get_contents($file);

// We need to replace the entire section from {{-- Country Comparison Report --}} to the end of the @endif block.
$pattern = '/\{\{-- Country Comparison Report --\}\}.*?@endif/s';

$replacement = <<<'EOD'
                {{-- Country Comparison Report --}}
                <tr>
                    <td width="60" class="text-center fs-4"><i class="bi bi-arrow-left-right text-primary"></i></td>
                    <td>
                        <h6 class="fw-bold mb-1 text-dark">Country Comparison Report</h6>
                        <small class="text-muted">Summary of saved country comparison analysis including economy, weather, currency, ports, trade risk, news sentiment, and final trade recommendation.</small>
                    </td>
                    <td class="text-end pe-4" style="min-width: 320px;">
                        <div class="d-flex gap-2 flex-wrap justify-content-end">
                            <a href="{{ route('compare.history') }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-eye me-2"></i> View History
                            </a>
                        </div>
                    </td>
                </tr>
EOD;

$content = preg_replace($pattern, $replacement, $content);
file_put_contents($file, $content);
echo "Updated index.blade.php\n";
