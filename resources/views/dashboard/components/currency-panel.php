<div class="card border-0 shadow-sm rounded-4 h-100">

    <div class="card-header bg-white border-0">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h5 class="fw-bold text-orange mb-1">

                    💱 Currency Monitoring

                </h5>

                <small class="text-muted">

                    Latest exchange rate information

                </small>

            </div>

            <span class="badge bg-success">

                {{ count($currencyPanel) }} Records

            </span>

        </div>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                <tr>

                    <th>Country</th>

                    <th>Currency</th>

                    <th>Rate</th>

                    <th>Change</th>

                    <th>Status</th>

                    <th>Updated</th>

                </tr>

                </thead>

                <tbody>

                @forelse($currencyPanel as $currency)

                    @php

                        $change = $currency->change_percentage;

                        $status = 'Stable';
                        $badge = 'success';

                        if(abs($change) > 8){
                            $status='Critical';
                            $badge='danger';
                        }elseif(abs($change)>5){
                            $status='High';
                            $badge='warning';
                        }elseif(abs($change)>2){
                            $status='Moderate';
                            $badge='primary';
                        }

                    @endphp

                    <tr>

                        <td>

                            <div class="d-flex align-items-center">

                                <img
                                    src="{{ $currency->country->flag }}"
                                    width="34"
                                    class="rounded shadow-sm me-2">

                                <div>

                                    <strong>

                                        {{ $currency->country->country_name }}

                                    </strong>

                                    <br>

                                    <small class="text-muted">

                                        {{ $currency->country->country_code }}

                                    </small>

                                </div>

                            </div>

                        </td>

                        <td>

                            {{ $currency->currency_code }}

                        </td>

                        <td>

                            {{ number_format($currency->exchange_rate,2) }}

                        </td>

                        <td>

                            @if($change>=0)

                                <span class="text-danger">

                                    ▲ {{ number_format($change,2) }}%

                                </span>

                            @else

                                <span class="text-success">

                                    ▼ {{ number_format(abs($change),2) }}%

                                </span>

                            @endif

                        </td>

                        <td>

                            <span class="badge bg-{{ $badge }}">

                                {{ $status }}

                            </span>

                        </td>

                        <td>

                            {{ optional($currency->recorded_at)->format('d M Y H:i') }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6"

                            class="text-center py-5">

                            No currency data available.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>