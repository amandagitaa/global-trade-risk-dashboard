@extends('layouts.app')

@section('title', 'Country Comparison History')

@section('content')

<div class="page-header mb-4 d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-center bg-soft-primary rounded-circle shadow-sm me-4 flex-shrink-0" style="width: 76px; height: 76px;">
        <i class="bi bi-clock-history text-primary" style="font-size: 34px;"></i>
    </div>
    <div class="flex-grow-1">
        <h1 class="display-5 fw-bold mb-2">
            Country Comparison History
        </h1>
        <p class="text-muted mb-0 fs-6">
            Saved comparison reports generated from the Compare module.
        </p>
    </div>
    <div>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary rounded-pill px-4"><i class="bi bi-arrow-left me-2"></i> Back to Reports</a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <tbody>
                @forelse($countryComparisonsList as $comp)
                <tr>
                    <td class="ps-4 py-3" width="60">
                        <i class="bi bi-file-earmark-bar-graph text-muted fs-4"></i>
                    </td>
                    <td class="py-3">
                        <h5 class="fw-bold mb-1 text-dark">{{ $comp->countryA->country_name ?? 'N/A' }} vs {{ $comp->countryB->country_name ?? 'N/A' }}</h5>
                        <small class="text-muted">{{ $comp->created_at->format('d M Y') }}</small>
                    </td>
                    <td class="text-end pe-4 py-3" style="min-width: 380px;">
                        <div class="d-flex gap-2 flex-wrap justify-content-end">
                            <a href="{{ route('compare.view', $comp->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center justify-content-center report-btn">
                                <i class="bi bi-eye me-1"></i> View Detail
                            </a>
                            
                            
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center py-5">
                        <div class="text-muted mb-3">No comparison reports available.</div>
                        <a href="{{ route('compare.index') }}" class="btn btn-primary rounded-pill px-4"><i class="bi bi-arrow-left-right me-2"></i> Go to Compare</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
.bg-soft-primary { background-color: rgba(13, 110, 253, 0.1); }
.report-btn { height: 38px; }
</style>

@endsection
