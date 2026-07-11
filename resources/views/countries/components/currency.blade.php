<div class="card border-0 shadow-sm rounded-4 h-100">

    <div class="card-header bg-white border-0">

        <h5 class="fw-bold text-orange">

            💱 Currency Information

        </h5>

    </div>

    <div class="card-body">

        @if($currency)

            @php

                $change = $currency->change_percentage;

                $status = 'Stable';
                $badge = 'success';

                if(abs($change)>8){

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

            <div class="text-center mb-4">

                <span class="badge bg-{{ $badge }} px-4 py-2">

                    {{ strtoupper($status) }}

                </span>

            </div>

            <table class="table table-borderless">

                <tr>

                    <th width="45%">

                        Currency

                    </th>

                    <td>

                        {{ $currency->currency_code }}

                    </td>

                </tr>

                <tr>

                    <th>

                        Exchange Rate

                    </th>

                    <td>

                        {{ number_format($currency->exchange_rate,2) }}

                    </td>

                </tr>

                <tr>

                    <th>

                        Daily Change

                    </th>

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

                </tr>

                <tr>

                    <th>

                        Updated

                    </th>

                    <td>

                        {{ optional($currency->recorded_at)->format('d M Y H:i') }}

                    </td>

                </tr>

            </table>

        @else

            <div class="text-center py-5">

                No currency information available.

            </div>

        @endif

    </div>

</div>