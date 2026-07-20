<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\reports\index.blade.php';
$content = file_get_contents($file);

// 1. Update the card
$content = str_replace('{{ number_format($countryComparisons ?? 0) }}', '{{ number_format($countryComparisonsCount ?? 0) }}', $content);

// 2. Replace the Country Comparison row
// I will find the <!-- Country Comparison Report --> section and replace it until <!-- Watch List Report -->
$pattern = '/<!-- Country Comparison Report -->.*?<!-- Watch List Report -->/s';

$replacement = <<<'EOD'
                  <!-- Country Comparison Report -->
                  @if(isset($countryComparisonsList) && count($countryComparisonsList) > 0)
                      <tr>
                          <td colspan="2" class="p-0 border-0">
                              <div class="bg-light p-3 border-bottom">
                                  <h6 class="mb-0 fw-bold"><i class="bi bi-arrow-left-right text-primary me-2"></i>Country Comparison Report</h6>
                                  <small class="text-muted">Summary of saved country comparison analysis including economy, weather, currency, ports, trade risk, news sentiment, and final trade recommendation.</small>
                              </div>
                          </td>
                      </tr>
                      @foreach($countryComparisonsList as $comp)
                      <tr class="align-middle">
                          <td class="ps-4 py-3">
                              <div class="d-flex flex-column">
                                  <h6 class="mb-1 fw-bold text-dark">{{ $comp->countryA->country_name ?? 'N/A' }} vs {{ $comp->countryB->country_name ?? 'N/A' }}</h6>
                                  <small class="text-muted">{{ $comp->created_at->format('d M Y') }}</small>
                              </div>
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
                          <td colspan="2" class="p-0 border-0">
                              <div class="bg-light p-3 border-bottom">
                                  <h6 class="mb-0 fw-bold"><i class="bi bi-arrow-left-right text-primary me-2"></i>Country Comparison Report</h6>
                                  <small class="text-muted">Summary of saved country comparison analysis including economy, weather, currency, ports, trade risk, news sentiment, and final trade recommendation.</small>
                              </div>
                          </td>
                      </tr>
                      <tr>
                          <td colspan="2" class="text-center py-5 text-muted">
                              No comparison reports available.<br>
                              <a href="{{ route('compare.index') }}" class="btn btn-primary mt-3"><i class="bi bi-arrow-left-right me-1"></i> Go to Compare</a>
                          </td>
                      </tr>
                  @endif
                  <!-- Watch List Report -->
EOD;

$content = preg_replace($pattern, $replacement, $content);
file_put_contents($file, $content);
echo "index.blade.php updated.\n";
