<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\dashboard\components\risk-chart.blade.php';
$content = <<<'EOD'
<div class="card border-0 shadow-sm rounded-4 h-100">
    <div class="card-header bg-white border-0">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-orange mb-1">
                    📊 Global Risk Distribution
                </h5>
                <small class="text-muted">
                    Distribution of countries by trade risk level
                </small>
            </div>
        </div>
    </div>
    <div class="card-body d-flex align-items-center justify-content-center">
        <canvas id="riskDistributionChart" height="200"></canvas>
    </div>
</div>

@push('scripts')
<script>
let riskChartInstance;

window.updateRiskChart = function(riskData) {
    const labels = [];
    const values = [];
    const colors = [];

    riskData.forEach(item => {
        labels.push(item.risk_level.toUpperCase());
        values.push(item.total);
        switch(item.risk_level) {
            case 'safe': colors.push('#28a745'); break;
            case 'stable': colors.push('#0d6efd'); break;
            case 'alert': colors.push('#ffc107'); break;
            case 'dangerous': colors.push('#ff8c00'); break;
            case 'critical': colors.push('#dc3545'); break;
        }
    });

    if (riskChartInstance) {
        riskChartInstance.data.labels = labels;
        riskChartInstance.data.datasets[0].data = values;
        riskChartInstance.data.datasets[0].backgroundColor = colors;
        riskChartInstance.update();
    } else {
        riskChartInstance = new Chart(
            document.getElementById('riskDistributionChart'),
            {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            }
        );
    }
};

document.addEventListener("DOMContentLoaded", () => {
    const initialRisk = @json($riskDistribution);
    window.updateRiskChart(initialRisk);
});
</script>
@endpush
EOD;

file_put_contents($file, $content);
echo "risk-chart.blade.php updated.\n";
