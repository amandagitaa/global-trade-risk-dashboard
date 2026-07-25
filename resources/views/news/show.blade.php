@extends('layouts.app')

@section('content')

<div class="container-fluid">

{{-- PAGE HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1"><i class="bi bi-newspaper text-warning me-2"></i>News Intelligence Analysis</h2>
        <p class="text-muted mb-0">Detailed trade impact and supply chain intelligence.</p>
    </div>
    <a href="{{ route('news.index') }}" class="btn btn-outline-secondary rounded-pill">
        <i class="bi bi-arrow-left me-2"></i>Back to News
    </a>
</div>

@php
    $impactColor = match($news->impact_level) {
        'Critical' => 'dark',
        'High' => 'danger',
        'Medium' => 'warning',
        default => 'secondary'
    };
    $riskColor = match($news->risk_direction) {
        'Increasing' => 'danger',
        'Decreasing' => 'success',
        default => 'secondary'
    };
@endphp

{{-- HERO CARD --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-4" style="width:80px;height:80px;">
                        <i class="bi bi-robot fs-2 text-primary"></i>
                    </div>
                    <div>
                        <span class="badge bg-{{ $impactColor }} mb-3">{{ $news->impact_level }} Impact</span>
                        <h2 class="fw-bold mb-3">{{ $news->title }}</h2>
                        <div class="text-muted">
                            <i class="bi bi-building me-2"></i>{{ $news->source ?? 'Unknown Source' }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="border rounded-4 p-3 text-center">
                            <small class="text-muted">Impact Score</small>
                            <h3 class="fw-bold text-{{ $impactColor }} mb-0">{{ $news->impact_score ?? 0 }}/100</h3>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded-4 p-3 text-center">
                            <small class="text-muted">Risk Direction</small>
                            <h3 class="fw-bold text-{{ $riskColor }} mb-0">
                                @if($news->risk_direction == 'Increasing') ↑ @elseif($news->risk_direction == 'Decreasing') ↓ @else → @endif 
                            </h3>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="border rounded-4 p-3">
                            <small class="text-muted">Published</small>
                            <div class="fw-semibold mt-1">{{ optional($news->published_at)->format('d M Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- INTELLIGENCE SUMMARY --}}
@if($news->intelligence_summary)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0">
        <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-text-fill text-warning me-2"></i>Executive Intelligence Summary</h5>
    </div>
    <div class="card-body">
        <p class="fs-5 text-dark lh-lg mb-0 fw-medium">{{ $news->intelligence_summary }}</p>
    </div>
</div>
@endif

<div class="row g-4 mb-4">
    {{-- TRADE IMPACT & EXPOSURE --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart-fill text-danger me-2"></i>Trade Impact Profile</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless align-middle mb-0">
                    <tr>
                        <td width="35%" class="text-muted">Category</td>
                        <td><span class="badge bg-primary">{{ ucfirst($news->category) }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Trade Exposure</td>
                        <td><span class="fw-bold"><i class="bi bi-tag me-2"></i>{{ $news->trade_exposure_type ?? 'N/A' }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Risk Direction</td>
                        <td class="text-{{ $riskColor }} fw-bold">{{ $news->risk_direction ?? 'Unknown' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Confidence Level</td>
                        <td>{{ ($news->impact_confidence ?? 0) * 100 }}%</td>
                    </tr>
                </table>

                @if(!empty($news->impact_factors))
                    <div class="mt-4">
                        <small class="text-muted d-block mb-2">Detected Operational Factors</small>
                        @foreach($news->impact_factors as $factor)
                            <span class="badge bg-light text-dark border me-1">{{ $factor }}</span>
                        @endforeach
                    </div>
                @endif
                
                @if(!empty($news->affected_sectors))
                    <div class="mt-3">
                        <small class="text-muted d-block mb-2">Affected Sectors</small>
                        @foreach($news->affected_sectors as $sector)
                            <span class="badge bg-light text-dark border me-1">{{ $sector }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- GEOGRAPHIC EXPOSURE --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h5 class="fw-bold mb-0"><i class="bi bi-globe-americas text-info me-2"></i>Geographic & Port Exposure</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <small class="text-muted d-block mb-2">Mapped Countries & Regions</small>
                    @if(!empty($news->mapped_countries) || !empty($news->regional_entities))
                        @if(!empty($news->mapped_countries))
                            @foreach($news->mapped_countries as $country)
                                <span class="badge border border-secondary text-secondary me-1 fs-6 mb-1">{{ $country['name'] }}</span>
                            @endforeach
                        @endif
                        @if(!empty($news->regional_entities))
                            @foreach($news->regional_entities as $region)
                                <span class="badge border border-primary text-primary me-1 fs-6 mb-1">{{ $region['name'] }}</span>
                            @endforeach
                        @endif
                    @else
                        <span class="text-muted">No specific geographic exposure detected.</span>
                    @endif
                </div>

                <div>
                    <small class="text-muted d-block mb-2">Maritime Port Exposure</small>
                    @if($news->port_impact_type == 'DIRECT' && !empty($news->mapped_ports))
                        @foreach($news->mapped_ports as $port)
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-water text-info fs-4 me-3"></i>
                                <div>
                                    <div class="fw-bold">{{ $port['name'] }}</div>
                                    <small class="text-muted">Confidence: {{ ($port['confidence'] ?? 0) * 100 }}%</small>
                                </div>
                            </div>
                        @endforeach
                    @elseif($news->port_impact_type == 'COUNTRY_LEVEL')
                        <div class="alert alert-light border d-flex align-items-center">
                            <i class="bi bi-water text-secondary fs-4 me-3"></i>
                            <div>Country-level maritime exposure inferred from context.</div>
                        </div>
                    @else
                        <span class="text-muted">No port exposure detected.</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ORIGINAL ARTICLE CONTENT --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Original Article Details</h5>
        @if($news->original_url)
            <a href="{{ $news->original_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary">
                Read Original Article <i class="bi bi-box-arrow-up-right ms-1"></i>
            </a>
        @endif
    </div>
    <div class="card-body">
        <p class="text-muted lh-lg mb-0">{{ $news->description }}</p>
    </div>
</div>

</div>
@endsection