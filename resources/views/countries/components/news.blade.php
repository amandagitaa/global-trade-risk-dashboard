<div class="card border-0 shadow-sm rounded-4">

    <div class="card-header bg-white border-0">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h5 class="fw-bold text-orange mb-1">

                    📰 Trade & Geopolitical News

                </h5>

                <small class="text-muted">

                    Latest news affecting international trade

                </small>

            </div>

        </div>

    </div>

    <div class="card-body">

        @if($news)

            @php

                $sentiment = strtolower($news->sentiment);

                $badge = match($sentiment){

                    'positive' => 'success',

                    'neutral'  => 'secondary',

                    'negative' => 'danger',

                    default    => 'dark'

                };

            @endphp

            <div class="row">

                <div class="col-lg-9">

                    <h4 class="fw-bold">

                        {{ $news->title }}

                    </h4>

                </div>

                <div class="col-lg-3 text-end">

                    <span class="badge bg-{{ $badge }} px-3 py-2">

                        {{ strtoupper($news->sentiment) }}

                    </span>

                </div>

            </div>

            <hr>

            <p class="text-muted">

                {{ $news->summary }}

            </p>

            @if(!empty($news->source))

                <div class="mt-4">

                    <small class="text-muted">

                        Source

                    </small>

                    <br>

                    <strong>

                        {{ $news->source }}

                    </strong>

                </div>

            @endif

            @if(!empty($news->url))

                <div class="mt-3">

                    <a href="{{ $news->url }}"
                       target="_blank"
                       class="btn btn-outline-warning btn-sm">

                        Read Full Article →

                    </a>

                </div>

            @endif

            <div class="mt-4 text-muted">

                <small>

                    Published :

                    {{ optional($news->published_at)->format('d M Y H:i') }}

                </small>

            </div>

        @else

            <div class="text-center py-5">

                <h6 class="text-muted">

                    No news available.

                </h6>

            </div>

        @endif

    </div>

</div>