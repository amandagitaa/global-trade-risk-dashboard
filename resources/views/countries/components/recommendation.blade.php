<div class="card border-0 shadow-sm rounded-4 h-100">

    <div class="card-header bg-white border-0">

        <h5 class="fw-bold text-orange">

            💡 Trade Recommendation

        </h5>

    </div>

    <div class="card-body">

        @if($recommendation)

            @php

                $priorityColor = match(strtolower($recommendation->priority)) {

                    'critical' => 'danger',

                    'very high' => 'warning',

                    'high' => 'warning',

                    'medium' => 'primary',

                    'low' => 'success',

                    default => 'secondary'

                };

            @endphp

            <div class="text-center mb-4">

                <span class="badge bg-{{ $priorityColor }} px-4 py-2">

                    {{ strtoupper($recommendation->priority) }}

                </span>

            </div>

            <div class="mb-4">

                <small class="text-muted">

                    Trade Action

                </small>

                <h4 class="fw-bold mt-2">

                    {{ $recommendation->trade_action }}

                </h4>

            </div>

            <div class="mb-4">

                <small class="text-muted">

                    Recommendation

                </small>

                <div class="alert alert-warning mt-2">

                    {{ $recommendation->recommendation }}

                </div>

            </div>

            <div class="mb-4">

                <small class="text-muted">

                    Business Reason

                </small>

                <div class="alert alert-light border mt-2">

                    {{ $recommendation->business_reason }}

                </div>

            </div>

            <div>

                <small class="text-muted">

                    Generated At

                </small>

                <div class="fw-semibold mt-1">

                    {{ optional($recommendation->generated_at)->format('d M Y H:i') }}

                </div>

            </div>

        @else

            <div class="text-center py-5">

                <h6 class="text-muted">

                    Recommendation not available.

                </h6>

            </div>

        @endif

    </div>

</div>