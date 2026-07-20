<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\reports\index.blade.php';
$content = file_get_contents($file);

$newSection = <<<'EOD'
                {{-- Country Comparison Report --}}
                <tr>
                    <td width="60" class="text-center fs-4"><i class="bi bi-arrow-left-right text-primary"></i></td>
                    <td colspan="2">
                        <h6 class="fw-bold mb-1 text-dark">Country Comparison Report</h6>
                        <small class="text-muted">Summary of saved country comparison analysis including economy, weather, currency, ports, trade risk, news sentiment, and final trade recommendation.</small>
                    </td>
                </tr>
                @if(isset($countryComparisonsList) && $countryComparisonsList->count() > 0)
                    @foreach($countryComparisonsList as $comp)
                    <tr>
                        <td width="60" class="text-center"><i class="bi bi-dot text-muted fs-4"></i></td>
                        <td>
                            <h6 class="fw-bold mb-1 text-dark">{{ $comp->countryA->country_name ?? 'N/A' }} vs {{ $comp->countryB->country_name ?? 'N/A' }}</h6>
                            <small class="text-muted">{{ $comp->created_at->format('d M Y') }}</small>
                        </td>
                        <td class="text-end pe-4" style="min-width: 320px;">
                            <div class="d-flex gap-2 flex-wrap justify-content-end">
                                <a href="{{ route('compare.view', $comp->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                    <i class="bi bi-eye me-1"></i> View
                                </a>
                                <a href="{{ route('compare.pdf', $comp->id) }}" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                                </a>
                                <a href="{{ route('compare.excel', $comp->id) }}" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                    <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td width="60"></td>
                        <td colspan="2" class="py-3">
                            <span class="text-muted me-3">No comparison reports available.</span>
                            <a href="{{ route('compare.index') }}" class="btn btn-primary btn-sm rounded-pill px-3"><i class="bi bi-arrow-left-right me-1"></i> Go to Compare</a>
                        </td>
                    </tr>
                @endif

EOD;

// Insert right before {{-- 3. Countries Report --}}
$content = str_replace("{{-- 3. Countries Report --}}", $newSection . "                {{-- 3. Countries Report --}}", $content);

file_put_contents($file, $content);
echo "Injected Country Comparison section perfectly!\n";
