<div class="card border-0 shadow-sm rounded-4 h-100">

    <div class="card-header bg-white border-0">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h5 class="fw-bold text-orange mb-1">
                    📰 Trade News Intelligence
                </h5>

                <small class="text-muted">
                    Latest news and sentiment analysis affecting global trade
                </small>

            </div>

            <div class="d-flex align-items-center gap-2">

                <span class="badge bg-dark">

                    {{ count($newsPanel) }} News

                </span>

                <a href="{{ route('news.index') }}"
                   class="btn btn-sm btn-outline-warning rounded-pill">

                    View All

                </a>

            </div>

        </div>

    </div>

    <div class="card-body">

        @forelse($newsPanel->take(5) as $news)

            @php

                $sentiment = strtolower($news->sentiment);

                $sentimentBadge = match($sentiment){

                    'positive' => 'success',

                    'negative' => 'danger',

                    default => 'warning'

                };

                $impact = match($sentiment){

                    'negative' => 'High Risk',

                    'positive' => 'Low Risk',

                    default => 'Medium Risk'

                };

                $impactBadge = match($impact){

                    'High Risk' => 'danger',

                    'Medium Risk' => 'warning',

                    default => 'success'

                };

                $published = $news->published_at
                    ?? $news->created_at
                    ?? null;

            @endphp

            <div class="border rounded-4 p-3 mb-3">

                <div class="d-flex justify-content-between align-items-start mb-2">

                    <h6 class="fw-bold mb-0">

                        {{ Str::limit($news->title,70) }}

                    </h6>

                    @if($published)

                        <small class="text-muted ms-3">

                            {{ \Carbon\Carbon::parse($published)->diffForHumans() }}

                        </small>

                    @endif

                </div>

                <p class="small text-muted mb-3">

                    {{ Str::limit($news->description,120) }}

                </p>

                <div class="d-flex flex-wrap gap-2">

                    <span class="badge bg-secondary">

                        {{ ucfirst($news->category) }}

                    </span>

                    <span class="badge bg-{{ $sentimentBadge }}">

                        {{ ucfirst($news->sentiment) }}

                    </span>

                    <span class="badge bg-{{ $impactBadge }}">

                        {{ $impact }}

                    </span>

                </div>

            </div>

        @empty

            <div class="text-center py-5 text-muted">

                No news available.

            </div>

        @endforelse

    </div>

</div>