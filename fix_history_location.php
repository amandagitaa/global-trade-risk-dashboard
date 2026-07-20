<?php

$content = file_get_contents('C:\laragon\www\global-trade-risk-dashboard\resources\views\trade-planner\index.blade.php');

// The block to move
$startMarker = "{{-- ==========================================\n        SIMULATION HISTORY";
$endMarker = "    @endif\n\n@push('scripts')";

$startIndex = strpos($content, $startMarker);
$endIndex = strpos($content, "@push('scripts')");

if ($startIndex !== false && $endIndex !== false) {
    // Extract the block
    $block = substr($content, $startIndex, $endIndex - $startIndex);
    
    // Remove the block from its current location
    $content = str_replace($block, "", $content);
    
    // Insert it before @endsection
    $content = str_replace("@endsection", $block . "\n@endsection", $content);
    
    file_put_contents('C:\laragon\www\global-trade-risk-dashboard\resources\views\trade-planner\index.blade.php', $content);
    echo "Moved Simulation History block inside @section('content').\n";
} else {
    echo "Could not find the block to move.\n";
}
