<div class="card border-0 shadow-sm rounded-4">

    <div class="card-body">

        <div class="row align-items-center">

            <div class="col-lg-8">

                <div class="d-flex align-items-center">

                    <img
                        src="{{ $country->flag }}"
                        width="70"
                        class="rounded shadow-sm me-3">

                    <div>

                        <h2 class="fw-bold mb-1">

                            {{ $country->country_name }}

                        </h2>

                        <div class="text-muted">

                            {{ $country->capital }}
                            •
                            {{ $country->region }}
                            •
                            {{ $country->currency_code }}

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-lg-4 text-end">

                @php

                    $riskColor = match($risk?->risk_level){

                        'safe' => 'success',

                        'stable' => 'primary',

                        'alert' => 'warning',

                        'dangerous' => 'warning',

                        'critical' => 'danger',

                        default => 'secondary'

                    };

                @endphp

                <span class="badge bg-{{ $riskColor }} px-4 py-2">

                    {{ strtoupper($risk?->risk_level ?? '-') }}

                </span>

                <h2 class="fw-bold mt-3 text-orange">

                    {{ number_format($risk?->final_score ?? 0,2) }}

                </h2>

                <small class="text-muted">

                    Global Trade Risk Score

                </small>

            </div>

        </div>

    </div>

</div>