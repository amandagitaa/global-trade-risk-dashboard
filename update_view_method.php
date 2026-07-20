<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\app\Http\Controllers\ReportExportController.php';
$content = file_get_contents($file);

$oldViewMethod = <<<'EOD'
    public function comparisonView($id) {
        $data = $this->getComparisonData($id, true);
        return view('reports.exports.country-comparison', compact('data', 'id'));
    }
EOD;

$newViewMethod = <<<'EOD'
    public function comparisonDetail($id) {
        $comp = \App\Models\CountryComparison::with(['countryA', 'countryB', 'user'])
            ->where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        return view('reports.country-comparison-detail', compact('comp'));
    }
EOD;

$content = str_replace($oldViewMethod, $newViewMethod, $content);
file_put_contents($file, $content);
echo "Updated comparisonView to comparisonDetail.\n";
