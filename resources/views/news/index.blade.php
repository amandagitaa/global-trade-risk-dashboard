@extends('layouts.app')

@section('content')
<div class="container-fluid">

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold">
            Trade Intelligence News
        </h1>
        <p class="text-muted">
            Real-time supply chain, trade, shipping and geopolitical intelligence.
        </p>
    </div>

    <a href="{{ route('news.sync') }}"
       class="btn btn-warning px-4">
        🔄 Sync News
    </a>
</div>

{{-- STATISTIC CARDS --}}
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6>Validated Intelligence Articles</h6>
                <h2 class="fw-bold">{{ number_format($totalNews) }}</h2>
                <span class="text-success">Latest Articles</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6>High Impact</h6>
                <h2 class="text-danger fw-bold">{{ number_format($highImpactCount) }}</h2>
                <span>Critical events detected</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6>Increasing Risk</h6>
                <h2 class="text-warning fw-bold">{{ number_format($increasingRiskCount) }}</h2>
                <span>Negative direction</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6>Countries / Regions Affected</h6>
                <h2 class="text-primary fw-bold">{{ number_format($affectedCountriesCount) }}</h2>
                <span>Geographic exposure</span>
            </div>
        </div>
    </div>
</div>

{{-- FILTER AREA --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('news.index') }}">
            {{-- ROW 1 --}}
            <div class="row g-3 mb-3">
                {{-- SEARCH --}}
                <div class="col-md-5">
                    <label class="form-label text-muted small fw-bold">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search title, source, country..." value="{{ request('search') }}">
                </div>

                {{-- COUNTRY --}}
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold">Country</label>
                    <select name="country" class="form-select">
                        <option value="">All Countries</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" @if(request('country') == $country->id) selected @endif>
                                {{ $country->country_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- CATEGORY --}}
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" @if(request('category') == $category) selected @endif>
                                {{ ucfirst($category) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- ROW 2: INTELLIGENCE FILTERS --}}
            <div class="row g-3">
                {{-- IMPACT LEVEL --}}
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Impact Level</label>
                    <select name="impact_level" class="form-select">
                        <option value="">All</option>
                        <option value="High" @if(request('impact_level') == 'High') selected @endif>High</option>
                        <option value="Medium" @if(request('impact_level') == 'Medium') selected @endif>Medium</option>
                        <option value="Low" @if(request('impact_level') == 'Low') selected @endif>Low</option>
                    </select>
                </div>

                {{-- RISK DIRECTION --}}
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Risk Direction</label>
                    <select name="risk_direction" class="form-select">
                        <option value="">All</option>
                        <option value="Increasing" @if(request('risk_direction') == 'Increasing') selected @endif>Increasing</option>
                        <option value="Stable" @if(request('risk_direction') == 'Stable') selected @endif>Stable</option>
                        <option value="Decreasing" @if(request('risk_direction') == 'Decreasing') selected @endif>Decreasing</option>
                    </select>
                </div>

                {{-- TRADE EXPOSURE --}}
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Trade Exposure</label>
                    <select name="trade_exposure_type" class="form-select">
                        <option value="">All</option>
                        @foreach(['Tariff', 'Trade Policy', 'Shipping', 'Freight', 'Export', 'Import', 'Port Operations', 'Supply Chain'] as $exposure)
                            <option value="{{ $exposure }}" @if(request('trade_exposure_type') == $exposure) selected @endif>{{ $exposure }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- SENTIMENT (Legacy but API-active) --}}
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold">Sentiment</label>
                    <select name="sentiment" class="form-select">
                        <option value="">All</option>
                        <option value="positive" @if(request('sentiment') == 'positive') selected @endif>Positive</option>
                        <option value="neutral" @if(request('sentiment') == 'neutral') selected @endif>Neutral</option>
                        <option value="negative" @if(request('sentiment') == 'negative') selected @endif>Negative</option>
                    </select>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button class="btn btn-primary w-100">🔍</button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($officialArticles->count() > 0)
<div class="mb-5">
    <h4 class="fw-bold mb-4 border-bottom pb-2"><i class="bi bi-file-earmark-text text-primary me-2"></i>Official Articles</h4>
    <div class="row g-4">
        @foreach($officialArticles as $article)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-primary border-top border-3">
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">
                            <span class="badge bg-primary">{{ $article->category }}</span>
                        </div>
                        <h5 class="fw-bold mb-2">{{ $article->title }}</h5>
                        <div class="text-muted small mb-3">
                            <i class="bi bi-person-fill"></i> {{ $article->author }} &bull; 
                            <i class="bi bi-clock"></i> {{ $article->created_at ? $article->created_at->format('d M Y') : 'Unknown Date' }}
                        </div>
                        <p class="small text-muted mb-4">
                            {{ Str::limit(strip_tags($article->content), 120) }}
                        </p>
                        <div class="mt-auto">
                            <a href="{{ route('articles.show', $article->id) }}" class="btn btn-outline-primary btn-sm w-100">
                                Read Article <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif


{{-- NEWS GRID --}}
<div class="row g-4">
    @forelse($news as $item)
        <div class="col-xl-4 col-lg-6">
            <div class="card h-100 shadow-sm border-0 overflow-hidden d-flex flex-column">
                
                {{-- IMAGE --}}
                @if($item->image_url)
                    <img src="{{ asset($item->image_url) }}" class="card-img-top" style="height:200px;object-fit:cover">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:200px;">
                        <span class="text-muted">No Image Available</span>
                    </div>
                @endif
                
                <div class="card-body d-flex flex-column">
                    
                    {{-- TOP BADGES --}}
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-primary fs-6">{{ strtoupper($item->category) }}</span>
                        
                        <div class="text-end">
                            <span class="badge @if($item->impact_level == 'High') bg-danger @elseif($item->impact_level == 'Medium') bg-warning text-dark @else bg-secondary @endif">
                                {{ $item->impact_level }} Impact
                            </span>
                            @if($item->impact_score)
                                <div class="small text-muted mt-1 fw-bold">Score: {{ $item->impact_score }}/100</div>
                            @endif
                        </div>
                    </div>

                    {{-- TITLE & META --}}
                    <h5 class="fw-bold mb-2">{{ $item->title }}</h5>
                    <div class="mb-3 text-muted small">
                        <strong>{{ $item->source ?? 'Unknown Source' }}</strong> • 
                        <i class="bi bi-clock"></i> {{ $item->published_at ? \Carbon\Carbon::parse($item->published_at)->format('d M Y, H:i') : 'Unknown Date' }}
                    </div>

                    {{-- INTELLIGENCE SUMMARY --}}
                    @if($item->intelligence_summary)
                        <div class="p-3 bg-light rounded mb-3 border-start border-4 border-primary">
                            <p class="mb-0 small fw-medium text-dark">{{ $item->intelligence_summary }}</p>
                        </div>
                    @endif

                    {{-- METADATA --}}
                    <div class="row g-2 mb-3 text-sm">
                        {{-- EXPOSURE --}}
                        @if($item->trade_exposure_type)
                            <div class="col-6">
                                <span class="text-muted small d-block">Trade Exposure</span>
                                <span class="fw-semibold"><i class="bi bi-tag me-1"></i>{{ $item->trade_exposure_type }}</span>
                            </div>
                        @endif

                        {{-- RISK DIRECTION --}}
                        @if($item->risk_direction)
                            <div class="col-6">
                                <span class="text-muted small d-block">Risk Direction</span>
                                <span class="fw-semibold @if($item->risk_direction == 'Increasing') text-danger @elseif($item->risk_direction == 'Decreasing') text-success @else text-muted @endif">
                                    @if($item->risk_direction == 'Increasing') ↑ @elseif($item->risk_direction == 'Decreasing') ↓ @else → @endif 
                                    {{ $item->risk_direction }}
                                </span>
                            </div>
                        @endif

                        {{-- GEOGRAPHY --}}
                        @if(!empty($item->mapped_countries) || !empty($item->regional_entities))
                            <div class="col-12 mt-2">
                                <span class="text-muted small d-block">Affected Geography</span>
                                <div>
                                    @if(!empty($item->mapped_countries))
                                        @foreach($item->mapped_countries as $country)
                                            <span class="badge border border-secondary text-secondary me-1">{{ $country['name'] }}</span>
                                        @endforeach
                                    @endif
                                    @if(!empty($item->regional_entities))
                                        @foreach($item->regional_entities as $region)
                                            <span class="badge border border-primary text-primary me-1">{{ $region['name'] }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- PORT INTELLIGENCE --}}
                        @if($item->port_impact_type == 'DIRECT' && !empty($item->mapped_ports))
                            <div class="col-12 mt-2">
                                <span class="text-muted small d-block">Maritime Port Exposure</span>
                                <div>
                                    @foreach($item->mapped_ports as $port)
                                        <span class="badge bg-info text-dark me-1"><i class="bi bi-water me-1"></i>{{ $port['name'] }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @elseif($item->port_impact_type == 'COUNTRY_LEVEL')
                            <div class="col-12 mt-2">
                                <span class="text-muted small d-block">Maritime Port Exposure</span>
                                <span class="badge bg-light text-secondary border"><i class="bi bi-water me-1"></i>Country-level maritime exposure</span>
                            </div>
                        @endif

                        {{-- SECTORS --}}
                        @if(!empty($item->affected_sectors))
                            <div class="col-12 mt-2">
                                <span class="text-muted small d-block">Affected Sectors</span>
                                <div>
                                    @foreach($item->affected_sectors as $sector)
                                        <span class="badge bg-light text-dark border me-1">{{ $sector }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- SPACER --}}
                    <div class="mt-auto"></div>

                    {{-- BUTTONS --}}
                    <div class="d-flex gap-2 pt-3 border-top mt-3">
                        <a href="{{ route('news.show', $item) }}" class="btn btn-primary btn-sm flex-grow-1">
                            View Analysis
                        </a>
                        
                        @if($item->original_url)
                            <a href="{{ $item->original_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm flex-grow-1">
                                Read Original Article <i class="bi bi-box-arrow-up-right ms-1"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-warning text-center p-5 bg-white border-0 shadow-sm rounded">
                <div class="fs-1 mb-3">📭</div>
                <h4 class="fw-bold">No validated Trade Intelligence articles currently available for this filter.</h4>
                <p class="text-muted mb-0">Adjust your search parameters or sync the latest news to populate the database.</p>
            </div>
        </div>
    @endforelse
</div>

{{-- PAGINATION --}}
<div class="mt-5">
    {{ $news->links() }}
</div>

</div>
@endsection