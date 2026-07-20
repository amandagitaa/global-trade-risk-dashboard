@extends('layouts.app')

@section('content')

<div class="page-header mb-4 d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-center bg-soft-orange rounded-circle shadow-sm me-4 flex-shrink-0" style="width: 76px; height: 76px;">
        <i class="bi bi-file-earmark-bar-graph-fill text-orange" style="font-size: 34px;"></i>
    </div>
    <div>
        <h1 class="display-5 fw-bold mb-2">
            Trade Reports
        </h1>
        <p class="text-muted mb-0 fs-6">
            Export and manage analytical reports generated from monitoring, simulation, and risk assessment modules.
        </p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 rounded-4">
            <div class="card-body">
                <small class="text-muted fw-semibold">Total Countries</small>
                <h2 class="fw-bold text-dark mt-2">{{ number_format($totalCountries) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 rounded-4">
            <div class="card-body">
                <small class="text-muted fw-semibold">Average Risk Score</small>
                <h2 class="fw-bold text-warning mt-2">{{ number_format($averageRisk,2) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 rounded-4">
            <div class="card-body">
                <small class="text-muted fw-semibold">Trade Simulations</small>
                <h2 class="fw-bold text-success mt-2">{{ number_format($tradeSimulations) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 rounded-4">
            <div class="card-body">
                <small class="text-muted fw-semibold">Watch List Items</small>
                <h2 class="fw-bold text-primary mt-2">{{ number_format($watchListItems) }}</h2>
            </div>
        </div>
    </div>
</div>

{{-- ================= AVAILABLE REPORTS ================= --}}
<div class="card border-0 shadow-sm mt-4 rounded-4">
    <div class="card-header bg-white py-3 border-bottom-0">
        <h4 class="fw-bold mb-0 text-dark">
            <i class="bi bi-folder2-open text-orange me-2"></i> Available Reports
        </h4>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <tbody>
                {{-- 1. Trade Planner Report --}}
                <tr>
                    <td width="60" class="text-center fs-4">🧭</td>
                    <td>
                        <h6 class="fw-bold mb-1 text-dark">Trade Planner Report</h6>
                        <small class="text-muted">Summary of shipment simulation, ETA estimation, shipping route, weather impact, currency impact, trade risk, and AI recommendation.</small>
                    </td>
                    <td class="text-end pe-4" style="min-width: 320px;">
                        <div class="d-flex gap-2 flex-wrap justify-content-end">
                            <a href="{{ route('trade-planner.view') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn"><i class="bi bi-eye me-1"></i> View</a>
                            <a href="{{ route('trade-planner.pdf') }}" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn"><i class="bi bi-file-earmark-pdf me-1"></i> Export PDF</a>
                            <a href="{{ route('trade-planner.excel') }}" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn"><i class="bi bi-file-earmark-excel me-1"></i> Export Excel</a>
                        </div>
                    </td>
                </tr>

                {{-- 2. Risk Analysis Report --}}
                <tr>
                    <td width="60" class="text-center fs-4">📊</td>
                    <td>
                        <h6 class="fw-bold mb-1 text-dark">Risk Analysis Report</h6>
                        <small class="text-muted">Summary of global trade risk analysis and country risk assessment.</small>
                    </td>
                    <td class="text-end pe-4" style="min-width: 320px;">
                        <div class="d-flex gap-2 flex-wrap justify-content-end">
                            <a href="{{ route('risk-analysis.view') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-eye me-1"></i> View
                            </a>
                            <a href="{{ route('risk-analysis.pdf') }}" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                            </a>
                            <a href="{{ route('risk-analysis.excel') }}" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                            </a>
                        </div>
                    </td>
                </tr>

                                                {{-- Country Comparison Report --}}
                <tr>
                    <td width="60" class="text-center fs-4"><i class="bi bi-arrow-left-right text-primary"></i></td>
                    <td>
                        <h6 class="fw-bold mb-1 text-dark">Country Comparison Report</h6>
                        <small class="text-muted">Summary of saved country comparison analysis including economy, weather, currency, ports, trade risk, news sentiment, and final trade recommendation.</small>
                    </td>
                    <td class="text-end pe-4" style="min-width: 320px;">
                        <div class="d-flex gap-2 flex-wrap justify-content-end">
                            <a href="{{ route('compare.history') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-eye me-1"></i> View
                            </a>
                            <a href="{{ route('compare.pdf.all') }}" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                            </a>
                            <a href="{{ route('compare.excel.all') }}" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                            </a>
                        </div>
                    </td>
                </tr>
                {{-- 3. Countries Report --}}
                <tr>
                    <td width="60" class="text-center fs-4">🌍</td>
                    <td>
                        <h6 class="fw-bold mb-1 text-dark">Countries Report</h6>
                        <small class="text-muted">Country profile, statistics, economic indicators, and trade overview.</small>
                    </td>
                    <td class="text-end pe-4" style="min-width: 320px;">
                        <div class="d-flex gap-2 flex-wrap justify-content-end">
                            <a href="{{ route('countries.view') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-eye me-1"></i> View
                            </a>
                            <a href="{{ route('countries.pdf') }}" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                            </a>
                            <a href="{{ route('countries.excel') }}" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                            </a>
                        </div>
                    </td>
                </tr>

                {{-- 4. Weather Report --}}
                <tr>
                    <td width="60" class="text-center fs-4">🌦</td>
                    <td>
                        <h6 class="fw-bold mb-1 text-dark">Weather Report</h6>
                        <small class="text-muted">Weather conditions, storm alerts, and shipping weather impact.</small>
                    </td>
                    <td class="text-end pe-4" style="min-width: 320px;">
                        <div class="d-flex gap-2 flex-wrap justify-content-end">
                            <a href="{{ route('weather.view') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-eye me-1"></i> View
                            </a>
                            <a href="{{ route('weather.pdf') }}" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                            </a>
                            <a href="{{ route('weather.excel') }}" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                            </a>
                        </div>
                    </td>
                </tr>

                {{-- 5. Currency Report --}}
                <tr>
                    <td width="60" class="text-center fs-4">💱</td>
                    <td>
                        <h6 class="fw-bold mb-1 text-dark">Currency Report</h6>
                        <small class="text-muted">Currency exchange trends and trade cost analysis.</small>
                    </td>
                    <td class="text-end pe-4" style="min-width: 320px;">
                        <div class="d-flex gap-2 flex-wrap justify-content-end">
                            <a href="{{ route('currency.view') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-eye me-1"></i> View
                            </a>
                            <a href="{{ route('currency.pdf') }}" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                            </a>
                            <a href="{{ route('currency.excel') }}" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                            </a>
                        </div>
                    </td>
                </tr>

                {{-- 6. Economy Report --}}
                <tr>
                    <td width="60" class="text-center fs-4">📈</td>
                    <td>
                        <h6 class="fw-bold mb-1 text-dark">Economy Report</h6>
                        <small class="text-muted">GDP, Inflation, Import, Export, and economic indicators.</small>
                    </td>
                    <td class="text-end pe-4" style="min-width: 320px;">
                        <div class="d-flex gap-2 flex-wrap justify-content-end">
                            <a href="{{ route('economy.view') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-eye me-1"></i> View
                            </a>
                            <a href="{{ route('economy.pdf') }}" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                            </a>
                            <a href="{{ route('economy.excel') }}" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                            </a>
                        </div>
                    </td>
                </tr>

                {{-- 7. Ports Report --}}
                <tr>
                    <td width="60" class="text-center fs-4">🚢</td>
                    <td>
                        <h6 class="fw-bold mb-1 text-dark">Ports Report</h6>
                        <small class="text-muted">Port capacity, active ships, congestion, and operational performance.</small>
                    </td>
                    <td class="text-end pe-4" style="min-width: 320px;">
                        <div class="d-flex gap-2 flex-wrap justify-content-end">
                            <a href="{{ route('ports.view') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-eye me-1"></i> View
                            </a>
                            <a href="{{ route('ports.pdf') }}" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                            </a>
                            <a href="{{ route('ports.excel') }}" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                            </a>
                        </div>
                    </td>
                </tr>

                {{-- 8. Watch List Report --}}
                <tr>
                    <td width="60" class="text-center fs-4">👀</td>
                    <td>
                        <h6 class="fw-bold mb-1 text-dark">Watch List Report</h6>
                        <small class="text-muted">Summary of monitored countries, ports, trade routes, alerts, and monitoring status.</small>
                    </td>
                    <td class="text-end pe-4" style="min-width: 320px;">
                        <div class="d-flex gap-2 flex-wrap justify-content-end">
                            <a href="{{ route('watch-list.view') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-eye me-1"></i> View
                            </a>
                            <a href="{{ route('watch-list.pdf') }}" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                            </a>
                            <a href="{{ route('watch-list.excel') }}" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<style>
.text-orange { color: #F59E0B !important; }
.bg-soft-orange { background-color: rgba(245, 158, 11, 0.1); }
.btn-outline-primary {
    border-color: #0d6efd;
    color: #0d6efd;
}
.btn-outline-primary:hover {
    background-color: #0d6efd;
    color: #fff;
}
.report-btn {
    height: 38px;
}
</style>

@endsection