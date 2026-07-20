<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\app\Http\Controllers\ReportExportController.php';
$content = file_get_contents($file);

$newMethod = <<<'EOD'
    public function compareHistory() {
        $countryComparisonsList = \App\Models\CountryComparison::with(['countryA', 'countryB'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
            
        return view('reports.compare-history', compact('countryComparisonsList'));
    }

    public function comparisonDetail($id) {
EOD;

$content = str_replace(
    "    public function comparisonDetail(\$id) {",
    $newMethod,
    $content
);
file_put_contents($file, $content);
echo "ReportExportController updated with compareHistory.\n";
