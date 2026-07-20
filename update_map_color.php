<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\dashboard\components\world-map.blade.php';
$content = file_get_contents($file);

// Add CSS classes
$newCss = <<<CSS
.marker-dimmed {
    opacity: 0.3 !important;
}
.marker-safe {
    filter: hue-rotate(287deg) saturate(2);
}
.marker-alert {
    filter: hue-rotate(185deg) saturate(3) brightness(1.2);
}
.marker-dangerous {
    filter: hue-rotate(160deg) saturate(3) brightness(1.2);
}
.marker-critical {
    filter: hue-rotate(146deg) saturate(3) brightness(0.9);
}
CSS;

$content = str_replace(
    ".marker-dimmed {
    opacity: 0.3 !important;
}",
    $newCss,
    $content
);

// Add JS logic to apply classes
$jsOld = <<<'JS'
        } else {
            // Standard marker using exact coordinates
            let defaultIcon = new L.Icon.Default();
            if (selectedCountryId) {
                defaultIcon.options.className = 'marker-dimmed';
            }
JS;

$jsNew = <<<'JS'
        } else {
            // Standard marker using exact coordinates
            let defaultIcon = new L.Icon.Default();
            let markerClass = '';
            
            let riskLower = risk.toLowerCase();
            if(riskLower === 'safe') markerClass = 'marker-safe';
            else if(riskLower === 'alert') markerClass = 'marker-alert';
            else if(riskLower === 'dangerous') markerClass = 'marker-dangerous';
            else if(riskLower === 'critical') markerClass = 'marker-critical';
            
            if (selectedCountryId) {
                markerClass += (markerClass ? ' ' : '') + 'marker-dimmed';
            }
            
            if (markerClass) {
                defaultIcon.options.className = markerClass;
            }
JS;

$content = str_replace($jsOld, $jsNew, $content);

file_put_contents($file, $content);
echo "world-map CSS updated.\n";
