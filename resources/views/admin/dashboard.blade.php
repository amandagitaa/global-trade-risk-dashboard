@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="row">
    {{-- Card Total Users --}}
    <div class="col-md-3 mb-4">
        <div class="panel d-flex align-items-center">
            <div class="card-icon me-3" style="background:#FFF3E0; color:#FF7A00; width:50px; height:50px; border-radius:12px; display:flex; justify-content:center; align-items:center; font-size:24px;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase">Total Users</div>
                <h3 class="mb-0 fw-bold">{{ number_format($data['total_users']) }}</h3>
            </div>
        </div>
    </div>

    {{-- Card Total Countries --}}
    <div class="col-md-3 mb-4">
        <div class="panel d-flex align-items-center">
            <div class="card-icon me-3" style="background:#FFF3E0; color:#FF7A00; width:50px; height:50px; border-radius:12px; display:flex; justify-content:center; align-items:center; font-size:24px;">
                <i class="bi bi-globe"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase">Total Countries</div>
                <h3 class="mb-0 fw-bold">{{ number_format($data['total_countries']) }}</h3>
            </div>
        </div>
    </div>

    {{-- Card Total Ports --}}
    <div class="col-md-3 mb-4">
        <div class="panel d-flex align-items-center">
            <div class="card-icon me-3" style="background:#FFF3E0; color:#FF7A00; width:50px; height:50px; border-radius:12px; display:flex; justify-content:center; align-items:center; font-size:24px;">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase">Total Ports</div>
                <h3 class="mb-0 fw-bold">{{ number_format($data['total_ports']) }}</h3>
            </div>
        </div>
    </div>

    {{-- Card Total News --}}
    <div class="col-md-3 mb-4">
        <div class="panel d-flex align-items-center">
            <div class="card-icon me-3" style="background:#FFF3E0; color:#FF7A00; width:50px; height:50px; border-radius:12px; display:flex; justify-content:center; align-items:center; font-size:24px;">
                <i class="bi bi-newspaper"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase">Total News</div>
                <h3 class="mb-0 fw-bold">{{ number_format($data['total_news']) }}</h3>
            </div>
        </div>
    </div>

    {{-- Card Total Articles --}}
    <div class="col-md-3 mb-4">
        <div class="panel d-flex align-items-center">
            <div class="card-icon me-3" style="background:#FFF3E0; color:#FF7A00; width:50px; height:50px; border-radius:12px; display:flex; justify-content:center; align-items:center; font-size:24px;">
                <i class="bi bi-journal-text"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase">Total Articles</div>
                <h3 class="mb-0 fw-bold">{{ number_format($data['total_articles']) }}</h3>
            </div>
        </div>
    </div>

    {{-- Card Total Risk Scores --}}
    <div class="col-md-3 mb-4">
        <div class="panel d-flex align-items-center">
            <div class="card-icon me-3" style="background:#FFF3E0; color:#FF7A00; width:50px; height:50px; border-radius:12px; display:flex; justify-content:center; align-items:center; font-size:24px;">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase">Total Risk Scores</div>
                <h3 class="mb-0 fw-bold">{{ number_format($data['total_risk_scores']) }}</h3>
            </div>
        </div>
    </div>

    {{-- Card Total Watch List --}}
    <div class="col-md-3 mb-4">
        <div class="panel d-flex align-items-center">
            <div class="card-icon me-3" style="background:#FFF3E0; color:#FF7A00; width:50px; height:50px; border-radius:12px; display:flex; justify-content:center; align-items:center; font-size:24px;">
                <i class="bi bi-eye-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase">Total Watch List</div>
                <h3 class="mb-0 fw-bold">{{ number_format($data['total_watchlists']) }}</h3>
            </div>
        </div>
    </div>

    {{-- Card Total Comparison --}}
    <div class="col-md-3 mb-4">
        <div class="panel d-flex align-items-center">
            <div class="card-icon me-3" style="background:#FFF3E0; color:#FF7A00; width:50px; height:50px; border-radius:12px; display:flex; justify-content:center; align-items:center; font-size:24px;">
                <i class="bi bi-arrow-left-right"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase">Total Comparison</div>
                <h3 class="mb-0 fw-bold">{{ number_format($data['total_comparisons']) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel">
            <h5 class="panel-title mb-4">User Activity</h5>
            <canvas id="userActivityChart" height="80"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('userActivityChart').getContext('2d');
        const chartLabels = {!! json_encode($chartLabels) !!};
        const chartData = {!! json_encode($chartData) !!};

        if (chartLabels.length === 0) {
            // No data
            ctx.font = '14px sans-serif';
            ctx.fillStyle = '#6c757d';
            ctx.textAlign = 'center';
            ctx.fillText('No user activity data available.', ctx.canvas.width / 2, ctx.canvas.height / 2);
            return;
        }

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'New Registrations',
                    data: chartData,
                    borderColor: '#FF7A00',
                    backgroundColor: 'rgba(255, 122, 0, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
