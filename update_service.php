<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\app\Services\DashboardService.php';
$content = file_get_contents($file);

// Add Port model
if (strpos($content, 'use App\Models\Port;') === false) {
    $content = str_replace('use App\Models\Country;', "use App\Models\Country;\nuse App\Models\Port;", $content);
}

// Replace recommendations array key
$content = str_replace("'recommendations' => \$this->getRecommendations(\$countryId),", "'portRisks' => \$this->getPortRisks(\$countryId),", $content);

// Replace getRecommendations with getPortRisks
$portRisksFunc = <<<'EOD'
    /**
     * ==========================================================
     * PORT RISK MONITORING
     * ==========================================================
     */

    private function getPortRisks($countryId)
    {
        $query = Port::query();
        
        if ($countryId) {
            $query->where('country_id', $countryId);
        }
        
        return $query->orderByDesc('risk_score')->take(10)->get();
    }
EOD;

$content = preg_replace('/\/\*\*[\s\*=]*TRADE RECOMMENDATIONS[\s\*=]*\*\/\s*private function getRecommendations\(\$countryId\)\s*\{\s*\$query = TradeRecommendation::with\(\'country\'\)->latest\(\);\s*if \(\$countryId\) \{\s*\$query->where\(\'country_id\', \$countryId\);\s*\}\s*return \$query->take\(10\)->get\(\);\s*\}/s', $portRisksFunc, $content);

file_put_contents($file, $content);
echo "DashboardService updated.\n";
