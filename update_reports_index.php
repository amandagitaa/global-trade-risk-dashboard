<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\app\Http\Controllers\ReportController.php';
$content = file_get_contents($file);

if (strpos($content, '$countryComparisons') === false) {
    // Add countryComparisons variable
    $content = str_replace(
        '$watchListItems = WatchList::count();',
        "\$watchListItems = WatchList::count();\n        \$countryComparisons = \App\Models\CountryComparison::where('user_id', auth()->id())->count();",
        $content
    );

    // Add countryComparisons to compact array
    $content = str_replace(
        "'watchListItems'",
        "'watchListItems',\n            'countryComparisons'",
        $content
    );

    file_put_contents($file, $content);
    echo "Added Country Comparison stats to ReportController\n";
}

$indexFile = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\reports\index.blade.php';
$indexContent = file_get_contents($indexFile);

// Update stats card (replace averageRisk with countryComparisons if needed, or add a new one)
// Wait, the user said "Jika terdapat 7 comparison yang disimpan, maka Country Comparison Report memiliki: 7 laporan. Statistik harus berasal dari database."
// It means we can just show it on the row or add a new card. Let's add a new card.
$oldCards = <<<'EOD'
              <div class="col-md-3">
                  <div class="card border-0 shadow-sm rounded-4 h-100 bg-orange text-white">
                      <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center text-center">
                          <i class="bi bi-bookmark-star fs-1 mb-2"></i>
                          <h2 class="fw-bold mb-0">{{ number_format($watchListItems) }}</h2>
                          <p class="mb-0 text-white-50">Watch List Items</p>
                      </div>
                  </div>
              </div>
EOD;

$newCards = <<<'EOD'
              <div class="col-md-3">
                  <div class="card border-0 shadow-sm rounded-4 h-100 bg-orange text-white">
                      <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center text-center">
                          <i class="bi bi-bookmark-star fs-1 mb-2"></i>
                          <h2 class="fw-bold mb-0">{{ number_format($watchListItems) }}</h2>
                          <p class="mb-0 text-white-50">Watch List Items</p>
                      </div>
                  </div>
              </div>
              
              <div class="col-md-3 mt-4 mt-md-0">
                  <div class="card border-0 shadow-sm rounded-4 h-100 border-primary border-top border-4">
                      <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center text-center">
                          <i class="bi bi-arrow-left-right text-primary fs-1 mb-2"></i>
                          <h2 class="fw-bold mb-0 text-dark">{{ number_format($countryComparisons ?? 0) }}</h2>
                          <p class="mb-0 text-muted">Comparisons Saved</p>
                      </div>
                  </div>
              </div>
EOD;

if (strpos($indexContent, "Comparisons Saved") === false) {
    // Actually the above col-md-3 are inside a row of 4 cols. Adding a 5th would break the layout if not wrapped or sized right.
    // The user says "Statistik harus berasal dari database" -> I will replace averageRisk or totalCountries or just add it.
    // Wait! Let's check how many cards are there. 
    // 1: Total Countries, 2: Average Risk, 3: Trade Simulations, 4: Watch List Items.
    // If I just add a badge inside the row of "Country Comparison Report", that fulfills it: "Country Comparison Report memiliki: 7 laporan"
}

// Add the new row for Country Comparison Report
$newRow = <<<'EOD'
                  <!-- Country Comparison Report -->
                  <tr class="align-middle">
                      <td class="ps-4 py-3">
                          <div class="d-flex align-items-center">
                              <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                  <i class="bi bi-arrow-left-right text-primary fs-4"></i>
                              </div>
                              <div>
                                  <h6 class="mb-1 fw-bold text-dark">Country Comparison Report</h6>
                                  <small class="text-muted">Summary of country comparison analysis including economy, weather, currency, trade risk, ports, news sentiment, and recommendation.</small>
                                  <span class="badge bg-primary ms-2">{{ number_format($countryComparisons ?? 0) }} saved</span>
                              </div>
                          </div>
                      </td>
                      <td class="text-end pe-4" style="min-width: 320px;">
                          <div class="d-flex gap-2 flex-wrap justify-content-end">
                              <a href="{{ route('compare.view') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                  <i class="bi bi-eye me-1"></i> View
                              </a>
                              <a href="{{ route('compare.pdf') }}" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                  <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                              </a>
                              <a href="{{ route('compare.excel') }}" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                  <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                              </a>
                          </div>
                      </td>
                  </tr>
EOD;

if (strpos($indexContent, "Country Comparison Report") === false) {
    // Insert after Trade Planner or Risk Analysis. Let's find Risk Analysis.
    $insertAfter = <<<'EOD'
                                  <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                              </a>
                          </div>
                      </td>
                  </tr>
EOD;
    
    // Find Risk Analysis row ends
    // To be safe, just inject it right before <!-- Watch List Report -->
    $indexContent = str_replace('<!-- Watch List Report -->', $newRow . "\n                  <!-- Watch List Report -->", $indexContent);
    file_put_contents($indexFile, $indexContent);
    echo "Added Country Comparison Report row to index.blade.php\n";
} else {
    echo "Row already exists.\n";
}
