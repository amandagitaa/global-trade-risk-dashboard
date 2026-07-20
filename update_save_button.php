<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\country-comparison\index.blade.php';
$content = file_get_contents($file);

$oldBtn = '<button class="btn btn-outline-secondary px-4"><i class="bi bi-bookmark"></i> Save Comparison</button>';

$newForm = <<<EOD
    <form action="{{ route('compare.save') }}" method="POST" class="d-inline">
        @csrf
        <input type="hidden" name="country_a_id" value="{{ \$countryA->id }}">
        <input type="hidden" name="country_b_id" value="{{ \$countryB->id }}">
        <button type="submit" class="btn btn-outline-secondary px-4"><i class="bi bi-bookmark"></i> Save Comparison</button>
    </form>
EOD;

$content = str_replace($oldBtn, "@if(isset(\$countryA) && isset(\$countryB))\n" . $newForm . "\n    @endif", $content);

file_put_contents($file, $content);
echo "Save Comparison form added to UI\n";
